<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Triggers que impiden físicamente más de una sesión abierta por caja.
     *
     * Regla: máximo 1 sesión 'abierta' por caja_id (sin importar el usuario).
     */
    private function createTriggers(): void
    {
        $host = Config::get('database.connections.mysql.host', '127.0.0.1');
        $port = Config::get('database.connections.mysql.port', 3306);
        $database = Config::get('database.connections.mysql.database', 'sistema_facturacion');
        $username = Config::get('database.connections.mysql.username', 'root');
        $password = Config::get('database.connections.mysql.password', '');

        $mysqli = new \mysqli($host, $username, $password, $database, (int) $port);

        if ($mysqli->connect_error) {
            throw new \RuntimeException('MySQLi connection failed: ' . $mysqli->connect_error);
        }

        $mysqli->set_charset('utf8mb4');

        // Drop existing triggers first (idempotent)
        $mysqli->query("DROP TRIGGER IF EXISTS sesion_cajas_before_insert");
        $mysqli->query("DROP TRIGGER IF EXISTS sesion_cajas_before_update");

        // Trigger BEFORE INSERT: reject if another open session exists for this caja
        $insertSql = <<<SQL
            CREATE TRIGGER sesion_cajas_before_insert
            BEFORE INSERT ON sesion_cajas
            FOR EACH ROW
            BEGIN
                DECLARE cnt INT;
                IF NEW.estado = 'abierta' THEN
                    SELECT COUNT(*) INTO cnt
                    FROM sesion_cajas
                    WHERE caja_id = NEW.caja_id AND estado = 'abierta';
                    IF cnt > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Ya existe una sesión abierta para esta caja. Cierre la sesión actual antes de abrir una nueva.';
                    END IF;
                END IF;
            END
        SQL;

        if (!$mysqli->query($insertSql)) {
            throw new \RuntimeException('Failed to create insert trigger: ' . $mysqli->error);
        }

        // Trigger BEFORE UPDATE: reject reopening if another open session exists
        $updateSql = <<<SQL
            CREATE TRIGGER sesion_cajas_before_update
            BEFORE UPDATE ON sesion_cajas
            FOR EACH ROW
            BEGIN
                DECLARE cnt INT;
                IF NEW.estado = 'abierta' AND OLD.estado != 'abierta' THEN
                    SELECT COUNT(*) INTO cnt
                    FROM sesion_cajas
                    WHERE caja_id = NEW.caja_id AND estado = 'abierta' AND id != NEW.id;
                    IF cnt > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Ya existe una sesión abierta para esta caja. No se puede reabrir.';
                    END IF;
                END IF;
            END
        SQL;

        if (!$mysqli->query($updateSql)) {
            throw new \RuntimeException('Failed to create update trigger: ' . $mysqli->error);
        }

        $mysqli->close();
    }

    public function up(): void
    {
        // 1. Cerrar sesiones duplicadas existentes (conservar la más reciente por caja)
        $cajasConDuplicados = DB::table('sesion_cajas')
            ->select('caja_id')
            ->where('estado', 'abierta')
            ->groupBy('caja_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('caja_id');

        foreach ($cajasConDuplicados as $cajaId) {
            $sesionReciente = DB::table('sesion_cajas')
                ->where('caja_id', $cajaId)
                ->where('estado', 'abierta')
                ->orderByDesc('fecha_apertura')
                ->value('id');

            if (!$sesionReciente) {
                continue;
            }

            DB::table('sesion_cajas')
                ->where('caja_id', $cajaId)
                ->where('estado', 'abierta')
                ->where('id', '!=', $sesionReciente)
                ->update([
                    'estado'       => 'cerrada',
                    'fecha_cierre' => now(),
                    'notas'        => DB::raw("CONCAT(COALESCE(NULLIF(notas, ''), ''), '[auto] Cerrada por duplicado. Conservada sesión #{$sesionReciente}.')"),
                ]);
        }

        // 2. Crear triggers de protección
        $this->createTriggers();
    }

    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS sesion_cajas_before_insert");
        DB::statement("DROP TRIGGER IF EXISTS sesion_cajas_before_update");
    }
};
