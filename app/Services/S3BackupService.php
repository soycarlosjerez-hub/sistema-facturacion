<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\ObjectIterator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class S3BackupService
{
    protected S3Client $s3;
    protected string $bucket;
    protected string $region;
    protected string $prefix;

    public function __construct()
    {
        $this->region = config('filesystems.disks.s3.region', env('AWS_DEFAULT_REGION', 'us-east-1'));
        $this->bucket = config('filesystems.disks.s3.bucket', env('AWS_BUCKET'));
        $this->prefix = 'backups/';

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function uploadBackup(string $localPath, string $filename): bool
    {
        try {
            $key = $this->prefix . date('Y/m/d') . '/' . $filename;

            $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $localPath,
                'ACL' => 'private',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('S3BackupService: Upload failed', [
                'error' => $e->getMessage(),
                'file' => $filename,
            ]);
            return false;
        }
    }

    public function downloadBackup(string $filename): ?string
    {
        try {
            $key = $this->prefix . date('Y/m/d') . '/' . $filename;
            $tempFile = tempnam(sys_get_temp_dir(), 'backup_');

            $this->s3->getObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SaveAs' => $tempFile,
            ]);

            return $tempFile;
        } catch (\Exception $e) {
            Log::error('S3BackupService: Download failed', [
                'error' => $e->getMessage(),
                'file' => $filename,
            ]);
            return null;
        }
    }

    public function deleteBackup(string $filename): bool
    {
        try {
            $key = $this->prefix . date('Y/m/d') . '/' . $filename;

            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('S3BackupService: Delete failed', [
                'error' => $e->getMessage(),
                'file' => $filename,
            ]);
            return false;
        }
    }

    public function listBackups(): array
    {
        try {
            $iterator = $this->s3->getIterator('ListObjects', [
                'Bucket' => $this->bucket,
                'Prefix' => $this->prefix,
            ]);

            $backups = [];
            foreach ($iterator as $object) {
                if (str_ends_with($object['Key'], '.sql.gz')) {
                    $backups[] = [
                        'key' => $object['Key'],
                        'size' => $object['Size'],
                        'lastModified' => $object['LastModified'],
                        'filename' => basename($object['Key']),
                    ];
                }
            }

            return collect($backups)->sortByDesc('lastModified')->toArray();
        } catch (\Exception $e) {
            Log::error('S3BackupService: List failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function generateDatabaseDump(): string
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host', '127.0.0.1');

            $tempFile = tempnam(sys_get_temp_dir(), 'backup_');

            $cmd = sprintf(
                'mysqldump --defaults-extra-file="%s" --single-transaction --routines --triggers %s 2>/dev/null | gzip > "%s"',
                $this->createTmpConfig($dbHost, $dbUser, $dbPass),
                $dbName,
                $tempFile
            );

            exec($cmd);

            if (file_exists($tempFile) && filesize($tempFile) > 0) {
                return $tempFile;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('S3BackupService: Dump failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function fullBackupToS3(): bool
    {
        try {
            $dumpFile = $this->generateDatabaseDump();

            if (!$dumpFile) {
                return false;
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql.gz';
            $result = $this->uploadBackup($dumpFile, $filename);

            @unlink($dumpFile);

            return $result;
        } catch (\Exception $e) {
            Log::error('S3BackupService: Full backup failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function rotateBackups(int $keepDays = 30): int
    {
        try {
            $cutoff = now()->subDays($keepDays);
            $iterator = $this->s3->getIterator('ListObjects', [
                'Bucket' => $this->bucket,
                'Prefix' => $this->prefix,
            ]);

            $deleted = 0;
            foreach ($iterator as $object) {
                if (str_ends_with($object['Key'], '.sql.gz')) {
                    $modified = new \DateTime($object['LastModified']);
                    if ($modified < $cutoff) {
                        $this->s3->deleteObject([
                            'Bucket' => $this->bucket,
                            'Key' => $object['Key'],
                        ]);
                        $deleted++;
                    }
                }
            }

            return $deleted;
        } catch (\Exception $e) {
            Log::error('S3BackupService: Rotation failed', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    protected function createTmpConfig(string $host, string $user, string $pass): string
    {
        $tmpCnf = tempnam(sys_get_temp_dir(), 'mycnf_');
        file_put_contents($tmpCnf, "[client]\nhost=\"{$host}\"\nuser=\"{$user}\"\npassword=\"{$pass}\"\n");
        return $tmpCnf;
    }
}
