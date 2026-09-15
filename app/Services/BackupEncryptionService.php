<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BackupEncryptionService
{
    /**
     * Encrypt a backup file using openssl (AES-256-CBC).
     */
    public function encrypt(string $filePath): ?string
    {
        $key = $this->getEncryptionKey();
        if (!$key) {
            Log::warning('Backup encryption key not found, skipping encryption');
            return null;
        }

        $encryptedPath = $filePath . '.enc';
        $iv = openssl_random_pseudo_bytes(16);
        
        if (!openssl_encrypt(
            file_get_contents($filePath),
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $ciphertext
        )) {
            Log::error('Failed to encrypt backup file', ['file' => $filePath]);
            return null;
        }

        $result = $iv . $ciphertext;
        if (file_put_contents($encryptedPath, $result) === false) {
            Log::error('Failed to write encrypted backup', ['file' => $encryptedPath]);
            return null;
        }

        return $encryptedPath;
    }

    /**
     * Decrypt a backup file.
     */
    public function decrypt(string $filePath, string $outputPath): bool
    {
        $key = $this->getEncryptionKey();
        if (!$key) {
            return false;
        }

        $data = file_get_contents($filePath);
        if (!$data) {
            return false;
        }

        $iv = substr($data, 0, 16);
        $ciphertext = substr($data, 16);

        if (!openssl_decrypt(
            $ciphertext,
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $decrypted
        )) {
            Log::error('Failed to decrypt backup file', ['file' => $filePath]);
            return false;
        }

        return file_put_contents($outputPath, $decrypted) !== false;
    }

    private function getEncryptionKey(): ?string
    {
        // Use the APP_KEY (without base64 prefix) or a dedicated BACKUP_ENCRYPTION_KEY
        $key = env('BACKUP_ENCRYPTION_KEY');
        if ($key) {
            return $key;
        }

        $appKey = env('APP_KEY');
        if ($appKey && str_starts_with($appKey, 'base64:')) {
            return base64_decode(substr($appKey, 7));
        }

        return null;
    }
}
