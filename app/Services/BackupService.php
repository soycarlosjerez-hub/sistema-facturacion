<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para manejar backups de la base de datos.
 * 
 * Responsabilidades:
 * - Crear backups manuales y automáticos
 * - Restaurar backups
 * - Limpiar backups antiguos
 * - Verificar integridad de backups
 */
class BackupService
{
    protected Backup $backupModel;
    protected string $backupDir;

    public function __construct(Backup $backupModel)
    {
        $this->backupModel = $backupModel;
        $this->backupDir = $backupModel::backupDir();
    }

    /**
     * Crear un backup de la base de datos.
     * 
     * @param string $type 'manual' o 'automatico'
     * @param string|null $customName Nombre personalizado del archivo
     * @param bool $compress Comprimir con gzip
     * @return Backup
     */
    public function createBackup(string $type = 'manual', ?string $customName = null, bool $compress = true): Backup
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');
            
            $timestamp = now()->format('Ymd_His');
            $filename = $customName 
                ? ($customName . ($compress ? '.sql.gz' : '.sql'))
                : "backup_{$dbName}_{$timestamp}" . ($compress ? '.sql.gz' : '.sql');

            $relativePath = 'backups/' . $filename;
            $fullPath = storage_path($relativePath);

            // Asegurar directorio existe
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            // Crear archivo de configuración temporal para mysqldump
            $tmpCnf = tempnam(sys_get_temp_dir(), 'mycnf_');
            file_put_contents($tmpCnf, "[client]\nhost=\"{$dbHost}\"\nuser=\"{$dbUser}\"\npassword=\"{$dbPass}\"\n");

            // Comando de backup
            if ($compress) {
                $cmd = sprintf(
                    'mysqldump --defaults-extra-file="%s" --single-transaction --routines --triggers %s 2>/dev/null | gzip > "%s"',
                    $tmpCnf,
                    $dbName,
                    $fullPath
                );
            } else {
                $cmd = sprintf(
                    'mysqldump --defaults-extra-file="%s" --single-transaction --routines --triggers %s > "%s" 2>&1',
                    $tmpCnf,
                    $dbName,
                    $fullPath
                );
            }

            // Ejecutar backup
            $output = [];
            $returnVar = 0;
            exec($cmd, $output, $returnVar);
            
            @unlink($tmpCnf);

            if ($returnVar !== 0 || !file_exists($fullPath) || filesize($fullPath) === 0) {
                $errorMsg = implode("\n", $output);
                Log::error('BackupService: Backup falló', ['error' => $errorMsg, 'cmd' => $cmd]);
                
                return $this->createBackupRecord([
                    'filename' => $filename,
                    'filepath' => $relativePath,
                    'size_bytes' => 0,
                    'type' => $type,
                    'status' => 'fallido',
                    'notes' => $errorMsg,
                    'user_id' => Auth::id(),
                ]);
            }

            // Registrar backup exitoso
            $size = filesize($fullPath);

            return $this->createBackupRecord([
                'filename' => $filename,
                'filepath' => $relativePath,
                'size_bytes' => $size,
                'type' => $type,
                'status' => 'completado',
                'user_id' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            Log::error('BackupService: Excepción al crear backup', ['exception' => $e->getMessage()]);
            
            return $this->createBackupRecord([
                'filename' => '',
                'filepath' => '',
                'size_bytes' => 0,
                'type' => $type,
                'status' => 'fallido',
                'notes' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
        }
    }

    /**
     * Restaurar un backup desde un archivo SQL.
     * 
     * @param string $filepath Ruta del archivo de backup
     * @return bool
     */
    public function restoreBackup(string $filepath): bool
    {
        $fullPath = storage_path($filepath);

        if (!file_exists($fullPath)) {
            Log::error('BackupService: Archivo de backup no encontrado', ['filepath' => $filepath]);
            return false;
        }

        try {
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');

            // Determinar si está comprimido
            if (str_ends_with($filepath, '.gz')) {
                $cmd = sprintf(
                    'gunzip -c "%s" | mysql --defaults-extra-file="%s" %s 2>&1',
                    $fullPath,
                    $this->createTmpConfig($dbHost, $dbUser, $dbPass),
                    $dbName
                );
            } else {
                $cmd = sprintf(
                    'mysql --defaults-extra-file="%s" %s < "%s" 2>&1',
                    $this->createTmpConfig($dbHost, $dbUser, $dbPass),
                    $dbName,
                    $fullPath
                );
            }

            $output = [];
            $returnVar = 0;
            exec($cmd, $output, $returnVar);

            if ($returnVar !== 0) {
                Log::error('BackupService: Restauración falló', ['output' => $output]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('BackupService: Excepción al restaurar', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Eliminar un backup.
     * 
     * @param Backup $backup
     * @return bool
     */
    public function deleteBackup(Backup $backup): bool
    {
        try {
            $filepath = storage_path($backup->filepath);
            
            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            return $backup->delete();
        } catch (\Exception $e) {
            Log::error('BackupService: Error al eliminar backup', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Limpiar backups antiguos.
     * 
     * @param int $days Días a mantener
     * @return int Número de backups eliminados
     */
    public function cleanOldBackups(int $days = 30): int
    {
        $cutoff = now()->subDays($days);
        $oldBackups = $this->backupModel->where('created_at', '<', $cutoff)->get();
        
        $count = 0;
        foreach ($oldBackups as $backup) {
            if ($this->deleteBackup($backup)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Verificar integridad de un archivo de backup.
     * 
     * @param string $filepath
     * @return array
     */
    public function verifyBackup(string $filepath): array
    {
        $fullPath = storage_path($filepath);

        if (!file_exists($fullPath)) {
            return ['valid' => false, 'error' => 'Archivo no encontrado'];
        }

        $size = filesize($fullPath);
        if ($size === 0) {
            return ['valid' => false, 'error' => 'Archivo vacío'];
        }

        // Verificar formato SQL
        $content = '';
        if (str_ends_with($filepath, '.gz')) {
            $content = gzread(fopen($fullPath, 'rb'), 1024);
        } else {
            $content = fread(fopen($fullPath, 'rb'), 1024);
        }

        if (strpos($content, 'CREATE') !== false || strpos($content, 'DROP') !== false) {
            return [
                'valid' => true,
                'size' => $size,
                'size_human' => $this->formatBytes($size),
                'file' => $filepath,
            ];
        }

        return ['valid' => false, 'error' => 'Archivo no parece ser un SQL válido'];
    }

    /**
     * Obtener estadísticas de backups.
     * 
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total' => $this->backupModel->count(),
            'total_size_bytes' => $this->backupModel->sum('size_bytes'),
            'total_size_human' => $this->formatBytes($this->backupModel->sum('size_bytes') ?? 0),
            'manual' => $this->backupModel->where('type', 'manual')->count(),
            'automatic' => $this->backupModel->where('type', 'automatico')->count(),
            'failed' => $this->backupModel->where('status', 'fallido')->count(),
            'last_7_days' => $this->backupModel->where('created_at', '>=', now()->subDays(7))->count(),
            'last_backup' => $this->backupModel->latest()->first(),
        ];
    }

    /**
     * Crear registro de backup en la BD.
     */
    protected function createBackupRecord(array $data): Backup
    {
        return $this->backupModel->create($data);
    }

    /**
     * Crear archivo temporal de configuración MySQL.
     */
    protected function createTmpConfig(string $host, string $user, string $pass): string
    {
        $tmpCnf = tempnam(sys_get_temp_dir(), 'mycnf_');
        file_put_contents($tmpCnf, "[client]\nhost=\"{$host}\"\nuser=\"{$user}\"\npassword=\"{$pass}\"\n");
        return $tmpCnf;
    }

    /**
     * Formatear bytes a human readable.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
