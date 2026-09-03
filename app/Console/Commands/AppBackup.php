<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppBackup extends Command
{
    protected $signature = 'app:backup {--filename= : Nombre personalizado del archivo (sin .sql o .sql.gz)}
                              {--compress : Comprimir con gzip (.sql.gz)}
                              {--target= : Ruta custom para guardar el backup (por defecto storage/app/backups/)}
                              {--db= : Nombre de la base de datos (por defecto la del .env)}
                              {--user= : Usuario de MySQL (por defecto el del .env)}
                              {--password= : Password de MySQL (por defecto el del .env)}
                              {--host= : Host de MySQL (por defecto el del .env)}
                              {--keep= : Días de retención de backups antiguos (por defecto 7)}
                              {--no-clean : No borrar backups antiguos}';

    protected $description = 'Crear backup de la base de datos';

    public function handle(): int
    {
        $compress = $this->option('compress');
        $customFile = $this->option('filename');
        $targetDir = $this->option('target') ?: 'app/backups/';
        $noClean = $this->option('no-clean');
        $keepDays = (int) $this->option('keep');

        if ($keepDays === 0) {
            $keepDays = 7;
        }

        $dbName = $this->option('db') ?: config('database.connections.mysql.database', DB::connection()->getDatabaseName());
        $dbUser = $this->option('user') ?: config('database.connections.mysql.username');
        $dbPass = $this->option('password') ?: config('database.connections.mysql.password');
        $dbHost = $this->option('host') ?: config('database.connections.mysql.host', '127.0.0.1');

        if (!$dbPass) {
            $this->error('No se pudo obtener la contraseña de la BD. Usa --password o configúrala en .env');
            return Command::FAILURE;
        }

        $mysqldump = Backup::mysqldumpPath();
        if (!file_exists($mysqldump) && !str_contains($mysqldump, 'mysqldump')) {
            $this->error("mysqldump no encontrado. Instala mysqldump o usa --host/--user/--password");
            return Command::FAILURE;
        }

        $timestamp = now()->format('Ymd_His');
        if ($customFile) {
            $filename = $customFile . ($compress ? '.sql.gz' : '.sql');
        } else {
            $filename = "backup_{$dbName}_{$timestamp}" . ($compress ? '.sql.gz' : '.sql');
        }

        $fullPath = storage_path('app/backups/' . $filename);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->info("Iniciando backup de: {$dbName}...");

        if ($compress) {
            $cmd = sprintf(
                '"%s" --host=%s --user=%s --password=%s --single-transaction --routines --triggers %s 2>/dev/null | gzip > "%s"',
                $mysqldump,
                $dbHost,
                $dbUser,
                $dbPass,
                $dbName,
                $fullPath
            );
            $this->newLine();
            $this->warn("Compresión activa: archivo más pequeño");
        } else {
            $cmd = sprintf(
                '"%s" --host=%s --user=%s --password=%s --single-transaction --routines --triggers %s > "%s" 2>/dev/null',
                $mysqldump,
                $dbHost,
                $dbUser,
                $dbPass,
                $dbName,
                $fullPath
            );
        }

        $output = null;
        $resultCode = null;
        exec($cmd, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($fullPath)) {
            $errorMsg = implode("\n", $output);
            $this->error("ERROR: El backup falló (código: $resultCode)");
            if ($errorMsg) {
                $this->error($errorMsg);
            }

            return Command::FAILURE;
        }

        $size = filesize($fullPath);
        $sizeHuman = $this->formatSize($size);

        $this->info("✅ Backup completado: {$filename} ({$sizeHuman})");
        $this->info("📁 Ubicación: {$fullPath}");

        $relativePath = str_replace(storage_path('/') . '/', 'storage/', $fullPath);

        Backup::create([
            'filename'   => $filename,
            'filepath'   => $relativePath,
            'size_bytes' => $size,
            'type'       => 'manual',
            'status'     => 'completado',
        ]);

        if (!$noClean && $keepDays > 0) {
            $cleaned = Backup::where('created_at', '<', now()->subDays($keepDays))->get();
            foreach ($cleaned as $backup) {
                $filepath = storage_path($backup->filepath);
                if (file_exists($filepath)) @unlink($filepath);
                $backup->delete();
            }
            if ($cleaned->count() > 0) {
                $this->info("🗑️ Backups antiguos borrados: {$cleaned->count()} (>{ $keepDays} días)");
            }
        }

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
