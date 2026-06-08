<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->paginate(15);
        $totalSize = Backup::sum('size_bytes');
        $countManual = Backup::manual()->count();
        $countAuto = Backup::automatico()->count();

        return view('backups.index', compact('backups', 'totalSize', 'countManual', 'countAuto'));
    }

    public function create()
    {
        set_time_limit(300);

        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $mysqldump = Backup::mysqldumpPath();

        if (!file_exists($mysqldump) && !str_contains($mysqldump, 'mysqldump')) {
            return back()->with('error', "mysqldump no encontrado. Verifica la ruta: {$mysqldump}");
        }

        $timestamp = now()->format('Ymd_His');
        $filename = "backup_{$dbName}_{$timestamp}.sql";
        $relativePath = 'app/backups/' . $filename;
        $fullPath = storage_path($relativePath);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $tmpCnf = tempnam(sys_get_temp_dir(), 'mycnf_');
        file_put_contents($tmpCnf, "[client]\nhost=\"{$dbHost}\"\nuser=\"{$dbUser}\"\npassword=\"{$dbPass}\"\n");
        $tmpCnfEscaped = '"' . $tmpCnf . '"';

        $cmd = sprintf(
            '"%s" --defaults-extra-file=%s --routines --single-transaction --databases "%s" > "%s" 2>&1',
            $mysqldump,
            $tmpCnfEscaped,
            $dbName,
            $fullPath
        );

        $output = null;
        $resultCode = null;
        exec($cmd, $output, $resultCode);

        if (file_exists($tmpCnf)) @unlink($tmpCnf);

        if ($resultCode !== 0 || !file_exists($fullPath)) {
            $errorMsg = implode("\n", $output ?? []);

            Backup::create([
                'filename'   => $filename,
                'filepath'   => $relativePath,
                'size_bytes' => 0,
                'type'       => 'manual',
                'status'     => 'fallido',
                'notes'      => $errorMsg ?: 'Error desconocido',
                'user_id'    => Auth::id(),
            ]);

            return back()->with('error', "Backup falló: {$errorMsg}");
        }

        $size = filesize($fullPath);

        Backup::create([
            'filename'   => $filename,
            'filepath'   => $relativePath,
            'size_bytes' => $size,
            'type'       => 'manual',
            'status'     => 'completado',
            'user_id'    => Auth::id(),
            'notes'      => 'Backup manual desde la interfaz',
        ]);

        return redirect()->route('backups.index')
            ->with('success', "Backup creado: {$filename} (" . number_format($size / 1024, 1) . " KB)");
    }

    public function download(Backup $backup)
    {
        $fullPath = storage_path($backup->filepath);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'El archivo de backup no existe en el servidor.');
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

        return redirect()->route('backups.index')
            ->with('success', 'Backup eliminado correctamente.');
    }

    public function config()
    {
        $backupDir = Backup::backupDir();
        $backupCount = Backup::count();
        $totalSize = Backup::sum('size_bytes');
        $lastBackup = Backup::latest()->first();
        $mysqldumpPath = Backup::mysqldumpPath();

        return view('backups.config', compact('backupDir', 'backupCount', 'totalSize', 'lastBackup', 'mysqldumpPath'));
    }
}
