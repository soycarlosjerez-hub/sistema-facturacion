<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 1;
    public $backoff = 0;

    protected string $type;
    protected ?string $customName;
    protected bool $compress;

    public function __construct(string $type = 'automatico', ?string $customName = null, bool $compress = true)
    {
        $this->type = $type;
        $this->customName = $customName;
        $this->compress = $compress;
    }

    public function handle(BackupService $backupService): void
    {
        $backup = $backupService->createBackup(
            $this->type,
            $this->customName,
            $this->compress
        );

        // TODO: Notificar por email si falló
        if ($backup->status === 'fallido') {
            // \Illuminate\Support\Facades\Mail::to('admin@empresa.com')
            //     ->send(new BackupFailedNotification($backup));
        }
    }
}
