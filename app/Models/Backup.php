<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'filename',
        'filepath',
        'size_bytes',
        'type',
        'status',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeManual($query)
    {
        return $query->where('type', 'manual');
    }

    public function scopeAutomatico($query)
    {
        return $query->where('type', 'automatico');
    }

    public function sizeForHumans(): string
    {
        $bytes = $this->size_bytes;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function backupDir(): string
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public static function mysqldumpPath(): string
    {
        $paths = [
            'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysqldump.exe',
            'C:\wamp64\bin\mysql\mysql8.4.0\bin\mysqldump.exe',
            'C:\wamp64\bin\mysql\mysql8.0.0\bin\mysqldump.exe',
            'mysqldump',
        ];
        foreach ($paths as $p) {
            if (file_exists($p)) return $p;
        }
        return 'mysqldump';
    }

    public static function cleanOldBackups(int $keepDays = 30): int
    {
        $cutoff = now()->subDays($keepDays);
        $old = static::where('created_at', '<', $cutoff)->get();
        $count = 0;
        foreach ($old as $backup) {
            $filepath = storage_path($backup->filepath);
            if (file_exists($filepath)) @unlink($filepath);
            $backup->delete();
            $count++;
        }
        return $count;
    }
}
