<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupRun extends Command
{
    protected $signature = 'backup:run {--type=automatico : manual o automatico}';
    protected $description = 'Realiza backup de la base de datos';

    public function handle(): int
    {
        $type = $this->option('type');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');

        $mysqldump = Backup::mysqldumpPath();

        if (!file_exists($mysqldump) && !str_contains($mysqldump, 'mysqldump')) {
            $this->error("mysqldump no encontrado en: $mysqldump");
            return Command::FAILURE;
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "backup_{$dbName}_{$timestamp}.sql";
        $relativePath = 'app/backups/' . $filename;
        $fullPath = storage_path($relativePath);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $cmd = sprintf(
            '"%s" --host=%s --user=%s --password=%s --routines --single-transaction --databases %s > "%s" 2>&1',
            $mysqldump,
            $dbHost,
            $dbUser,
            $dbPass,
            $dbName,
            $fullPath
        );

        $this->info("Ejecutando: mysqldump {$dbName}...");
        $output = null;
        $resultCode = null;
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($fullPath)) {
            $errorMsg = implode("\n", $output ?? []);
            $this->error("Backup falló (código: $resultCode): $errorMsg");

            Backup::create([
                'filename'   => $filename,
                'filepath'   => $relativePath,
                'size_bytes' => 0,
                'type'       => $type,
                'status'     => 'fallido',
                'notes'      => $errorMsg ?: 'Error desconocido',
            ]);

            return Command::FAILURE;
        }

        $size = filesize($fullPath);
        $this->info("Backup creado: {$filename} (" . number_format($size / 1024, 1) . " KB)");

        Backup::create([
            'filename'   => $filename,
            'filepath'   => $relativePath,
            'size_bytes' => $size,
            'type'       => $type,
            'status'     => 'completado',
        ]);

        if ($type === 'automatico') {
            $cleaned = Backup::cleanOldBackups();
            if ($cleaned > 0) {
                $this->info("Backups antiguos limpiados: {$cleaned}");
            }
        }

        return Command::SUCCESS;
    }
}
