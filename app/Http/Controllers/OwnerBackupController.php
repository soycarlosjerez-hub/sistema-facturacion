<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Services\OwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OwnerBackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->paginate(15);
        $totalSize = Backup::sum('size_bytes');
        $countManual = Backup::manual()->count();
        $countAuto = Backup::automatico()->count();
        $lastBackup = Backup::latest()->first();
        $last7Days = Backup::where('created_at', '>=', now()->subDays(7))->count();

        return view('owner.backups.index', compact('backups', 'totalSize', 'countManual', 'countAuto', 'lastBackup', 'last7Days'));
    }

    public function store(Request $request)
    {
        set_time_limit(300);

        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');

        // Sanitizar credenciales para prevenir command injection
        $sanitized = OwnerService::sanitizeDbCredentials([
            'host' => $dbHost,
            'user' => $dbUser,
            'pass' => $dbPass,
            'name' => $dbName,
        ]);

        // Obtener ruta de mysqldump
        $mysqldump = Backup::mysqldumpPath();

        if (!file_exists($mysqldump) && !str_contains($mysqldump, 'mysqldump')) {
            return back()->with('error', "mysqldump no encontrado. Verifica la ruta: {$mysqldump}");
        }

        $compress = $request->boolean('compress');
        $customName = $request->input('filename');

        // Sanitizar nombre de archivo de backup
        $sanitizedName = OwnerService::sanitizeBackupFilename($customName);

        $timestamp = now()->format('Ymd_His');
        if ($sanitizedName) {
            $filename = $sanitizedName . ($compress ? '.sql.gz' : '.sql');
        } else {
            $filename = "backup_{$sanitized['name']}_{$timestamp}" . ($compress ? '.sql.gz' : '.sql');
        }

        $relativePath = 'app/backups/' . $filename;
        $fullPath = storage_path($relativePath);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Usar archivo de configuración de MySQL (más seguro que pasar credenciales en línea de comandos)
        $tmpCnf = tempnam(sys_get_temp_dir(), 'mycnf_');
        file_put_contents($tmpCnf, "[client]\nhost=\"{$sanitized['host']}\"\nuser=\"{$sanitized['user']}\"\npassword=\"{$sanitized['pass']}\"\n");
        $tmpCnfEscaped = escapeshellarg($tmpCnf);

        // Sanitizar nombre de BD para uso en comando
        $dbNameEscaped = escapeshellarg($sanitized['name']);
        $mysqldumpEscaped = escapeshellarg($mysqldump);
        $fullPathEscaped = escapeshellarg($fullPath);

        if ($compress) {
            $cmd = sprintf(
                '%s --defaults-extra-file=%s --single-transaction --routines --triggers %s 2>/dev/null | gzip > %s',
                $mysqldumpEscaped,
                $tmpCnfEscaped,
                $dbNameEscaped,
                $fullPathEscaped
            );
        } else {
            $cmd = sprintf(
                '%s --defaults-extra-file=%s --single-transaction --routines --triggers %s > %s 2>&1',
                $mysqldumpEscaped,
                $tmpCnfEscaped,
                $dbNameEscaped,
                $fullPathEscaped
            );
        }

        $output = null;
        exec($cmd, $output);

        if (file_exists($tmpCnf)) @unlink($tmpCnf);

        if (!file_exists($fullPath) || filesize($fullPath) === 0) {
            $errorMsg = implode("\n", $output ?? []);
            Log::error('Owner backup failed: ' . $errorMsg);

            Backup::create([
                'filename'   => $filename,
                'filepath'   => $relativePath,
                'size_bytes' => 0,
                'type'       => 'manual',
                'status'     => 'fallido',
                'notes'      => $errorMsg ?: 'Error desconocido',
                'user_id'    => Auth::id(),
            ]);

            return back()->with('error', 'Backup falló: ' . substr($errorMsg, 0, 200));
        }

        $size = filesize($fullPath);
        Backup::create([
            'filename'   => $filename,
            'filepath'   => $relativePath,
            'size_bytes' => $size,
            'type'       => 'manual',
            'status'     => 'completado',
            'user_id'    => Auth::id(),
            'notes'      => $sanitizedName ? "Backup personalizado: {$sanitizedName}" : null,
        ]);

        return redirect()->route('owner.backups.index')
            ->with('success', "Backup creado: {$filename} (" . number_format($size / 1024, 1) . " KB)");
    }

    public function download(Backup $backup)
    {
        $fullPath = storage_path($backup->filepath);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'El archivo de backup no existe.');
        }

        return response()->download($fullPath, $backup->filename);
    }

    public function destroy(Backup $backup)
    {
        $fullPath = storage_path($backup->filepath);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
        $backup->delete();

        return redirect()->route('owner.backups.index')
            ->with('success', 'Backup eliminado.');
    }

    public function restore(Backup $backup)
    {
        $fullPath = storage_path($backup->filepath);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'El archivo de backup no existe.');
        }

        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');

        $confirm = $request()->boolean('confirm');

        if (!$confirm) {
            return view('owner.backups.restore-confirm', compact('backup', 'fullPath', 'dbName', 'dbUser', 'dbPass', 'dbHost'));
        }

        try {
            // Sanitizar credenciales para prevenir command injection
            $sanitized = OwnerService::sanitizeDbCredentials([
                'host' => $dbHost,
                'user' => $dbUser,
                'pass' => $dbPass,
                'name' => $dbName,
            ]);

            $isGz = str_ends_with($fullPath, '.sql.gz');

            $mysqldumpEscaped = escapeshellarg($fullPath);
            $dbHostEscaped = escapeshellarg($sanitized['host']);
            $dbUserEscaped = escapeshellarg($sanitized['user']);
            $dbNameEscaped = escapeshellarg($sanitized['name']);

            // Usar archivo de configuración temporal para credenciales (evita exposición en ps)
            $tmpCnf = tempnam(sys_get_temp_dir(), 'mycnf_');
            file_put_contents($tmpCnf, "[client]\nhost=\"{$sanitized['host']}\"\nuser=\"{$sanitized['user']}\"\npassword=\"{$sanitized['pass']}\"\n");
            $tmpCnfEscaped = escapeshellarg($tmpCnf);

            if ($isGz) {
                // Usar pipe con archivo de configuración en lugar de pasar password en línea de comandos
                $cmd = sprintf(
                    'gunzip -c %s 2>/dev/null | mysql --defaults-extra-file=%s %s',
                    $mysqldumpEscaped,
                    $tmpCnfEscaped,
                    $dbNameEscaped
                );
            } else {
                $cmd = sprintf(
                    'mysql --defaults-extra-file=%s %s < %s',
                    $tmpCnfEscaped,
                    $dbNameEscaped,
                    $mysqldumpEscaped
                );
            }

            $output = null;
            exec($cmd, $output, $resultCode);

            // Limpiar archivo de credenciales
            if (file_exists($tmpCnf)) @unlink($tmpCnf);

            if ($resultCode !== 0) {
                Log::error('Owner restore failed: ' . implode("\n", $output));
                return back()->with('error', 'Restauración falló (código: ' . $resultCode . ')');
            }

            Backup::create([
                'filename'   => 'RESTORE:' . $backup->filename,
                'filepath'   => '',
                'size_bytes' => 0,
                'type'       => 'restore',
                'status'     => 'completado',
                'notes'      => 'Restaurado desde: ' . $backup->filename . ' por ' . Auth::user()->name,
                'user_id'    => Auth::id(),
            ]);

            return redirect()->route('owner.backups.index')
                ->with('success', 'Base de datos restaurada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Owner restore exception: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
