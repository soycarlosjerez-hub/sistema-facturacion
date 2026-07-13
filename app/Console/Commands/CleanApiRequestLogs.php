<?php

namespace App\Console\Commands;

use App\Models\ApiRequestLog;
use Illuminate\Console\Command;

class CleanApiRequestLogs extends Command
{
    protected $signature = 'api-logs:clean {--days=90 : Number of days to retain logs}';
    protected $description = 'Delete old API request logs older than specified days';

    public function handle(): int
    {
        $days = (int)$this->option('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = ApiRequestLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deletedCount} API request logs older than {$days} days.");

        return Command::SUCCESS;
    }
}
