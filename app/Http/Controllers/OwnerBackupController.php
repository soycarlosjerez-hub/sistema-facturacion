<?php

namespace App\Http\Controllers;

use App\Models\Backup;
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

    public function store()
    {
        set_time_limit(300);

        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $mysqldump = Backup::mysqldumpPath();

        if (!file_exists($mysqldump) && !str_contains($mysqldump, 'mysqldump')) {
            return back()->with('error', "mysqldump no encontrado. Verifica la ruta: {$mysqldump}");
        }

        $compress = request()->boolean('compress');
        $customName = request('filename');

        $timestamp = now()->format('Ymd_His');
        if ($customName) {
            $filename = $customName . ($compress ? '.sql.gz' : '.sql');
        } else {
            $filename = "backup_{$dbName}_{$timestamp}" . ($compress ? '.sql.gz' : '.sql');
        }

        $relativePath = 'app/backups/' . $filename;
        $fullPath = storage_path($relativePath);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmpCnf = tempnam(sys_get_temp_dir(), 'mycnf_');
        file_put_contents($tmpCnf, "[client]\nhost=\"{$dbHost}\"\nuser=\"{$dbUser}\"\npassword=\"{$dbPass}\"\n");
        $tmpCnfEscaped = '"' . $tmpCnf . '"';

        if ($compress) {
            $cmd = sprintf(
                '"%s" --defaults-extra-file=%s --single-transaction --routines --triggers %s 2>/dev/null | gzip > "%s"',
                $mysqldump,
                $tmpCnfEscaped,
                $dbName,
                $fullPath
            );
        } else {
            $cmd = sprintf(
                '"%s" --defaults-extra-file=%s --single-transaction --routines --triggers %s > "%s" 2>&1',
                $mysqldump,
                $tmpCnfEscaped,
                $dbName,
                $fullPath
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
            'notes'      => $customName ? "Backup personalizado: {$customName}" : null,
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

        $confirm = request()->boolean('confirm');

        if (!$confirm) {
            return view('owner.backups.restore-confirm', compact('backup', 'fullPath', 'dbName', 'dbUser', 'dbPass', 'dbHost'));
        }

        try {
            $isGz = str_ends_with($fullPath, '.sql.gz');

            if ($isGz) {
                $cmd = sprintf(
                    'gunzip -c "%s" 2>/dev/null | mysql --host=%s --user=%s --password=%s %s',
                    $fullPath,
                    $dbHost,
                    $dbUser,
                    $dbPass,
                    $dbName
                );
            } else {
                $cmd = sprintf(
                    'mysql --host=%s --user=%s --password=%s %s < "%s"',
                    $dbHost,
                    $dbUser,
                    $dbPass,
                    $dbName,
                    $fullPath
                );
            }

            exec($cmd, $output, $resultCode);

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
