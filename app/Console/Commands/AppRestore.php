<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AppRestore extends Command
{
    protected $signature = 'app:restore {file : Ruta del archivo .sql o .sql.gz a restaurar}
                               {--db= : Base de datos destino (por defecto la del .env)}
                               {--user= : Usuario de MySQL (por defecto el del .env)}
                               {--password= : Password de MySQL (por defecto el del .env)}
                               {--host= : Host de MySQL (por defecto el del .env)}
                               {--force : Saltar la confirmación}';

    protected $description = 'Restaurar base de datos desde un backup';

    public function handle(): int
    {
        $file = $this->argument('file');
        $force = $this->option('force');

        if (str_starts_with($file, 'storage/')) {
            $file = storage_path(str_replace('storage/', '', $file));
        }

        if (!file_exists($file)) {
            $this->error("❌ Archivo no encontrado: {$file}");
            $this->info('Usa: php artisan app:list-backups');
            return Command::FAILURE;
        }

        $dbName = $this->option('db') ?: config('database.connections.mysql.database', DB::connection()->getDatabaseName());
        $dbUser = $this->option('user') ?: config('database.connections.mysql.username');
        $dbPass = $this->option('password') ?: config('database.connections.mysql.password');
        $dbHost = $this->option('host') ?: config('database.connections.mysql.host', '127.0.0.1');

        if (!$dbPass) {
            $this->error('No se pudo obtener la contraseña de la BD.');
            return Command::FAILURE;
        }

        if (!$force) {
            $this->warn('⚠️  ATENCIÓN: Esto eliminará TODOS los datos de ' . $dbName);
            $this->warn('La operación NO SE PUEDE DESHACER.');

            $confirmed = $this->confirm('¿Estás seguro de continuar? (se recomienda hacer un backup antes)', false);
            if (!$confirmed) {
                $this->info('Restauración cancelada.');
                return Command::FAILURE;
            }
        }

        $fileSize = filesize($file);
        $fileSizeHuman = $this->formatSize($fileSize);

        $this->info("📂 Restaurando: " . basename($file));
        $this->info("💾 Tamaño: {$fileSizeHuman}");
        $this->info("🗄️  Base de datos: {$dbName}");
        $this->info("⏳ Esto puede tomar unos minutos...");
        $this->newLine();

        if (str_ends_with($file, '.sql.gz')) {
            $cmd = sprintf(
                'gunzip -c "%s" | mysql --host=%s --user=%s --password=%s %s 2>&1',
                $file,
                $dbHost,
                $dbUser,
                $dbPass,
                $dbName
            );
        } else {
            $cmd = sprintf(
                'mysql --host=%s --user=%s --password=%s %s < "%s" 2>&1',
                $dbHost,
                $dbUser,
                $dbPass,
                $dbName,
                $file
            );
        }

        $output = [];
        $resultCode = 0;
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0) {
            $errorOutput = implode("\n", $output);
            $this->error("❌ ERROR: Falló la restauración (código: $resultCode)");
            if ($errorOutput) {
                $this->error($errorOutput);
            }
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("✅ Restauración completada exitosamente!");
        $this->info("📋 Archivo restaurado: {$file}");

        return Command::SUCCESS;
    }

    private function formatSize(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
