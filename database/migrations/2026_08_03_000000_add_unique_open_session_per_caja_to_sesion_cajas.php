<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Impide físicamente que una caja tenga más de una sesión abierta a la vez.
     * Usa una columna generada que solo toma valor cuando estado='abierta',
     * de modo que el índice único solo aplica a sesiones abiertas.
     */
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

        // 2. Columna generada + índice único
        Schema::table('sesion_cajas', function (Blueprint $table) {
            $table->unsignedBigInteger('abierta_uid')->nullable()->storedAs("IF(estado = 'abierta', caja_id, NULL)");
        });

        Schema::table('sesion_cajas', function (Blueprint $table) {
            $table->unique('abierta_uid', 'sesion_cajas_abierta_uid_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sesion_cajas', function (Blueprint $table) {
            $table->dropUnique('sesion_cajas_abierta_uid_unique');
            $table->dropColumn('abierta_uid');
        });
    }
};
