<?php

namespace App\Console\Commands;

use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Console\Command;

class SyncInstanceOwners extends Command
{
    protected $signature = 'instances:sync-owners';
    protected $description = 'Sync owner_email and owner_nombre fields for all business instances';

    public function handle(): int
    {
        $count = 0;
        $skipped = 0;
        $errors = 0;

        BusinessInstance::withTrashed()->chunkById(100, function ($instances) use (&$count, &$skipped, &$errors) {
            foreach ($instances as $instance) {
                try {
                    if (!$instance->owner_user_id) {
                        $skipped++;
                        continue;
                    }

                    $owner = User::find($instance->owner_user_id);
                    if (!$owner) {
                        $skipped++;
                        continue;
                    }

                    $changed = false;
                    $updates = [];

                    if ($instance->owner_email !== $owner->email) {
                        $instance->owner_email = $owner->email;
                        $changed = true;
                        $updates['owner_email'] = $owner->email;
                    }

                    if ($instance->owner_nombre !== $owner->name) {
                        $instance->owner_nombre = $owner->name;
                        $changed = true;
                        $updates['owner_nombre'] = $owner->name;
                    }

                    if ($changed) {
                        $instance->save();
                        $count++;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $this->error("Error syncing instance #{$instance->id}: {$e->getMessage()}");
                }
            }
        });

        $this->info("Successfully synced: {$count}");
        $this->info("Skipped (no owner): {$skipped}");
        $this->info("Errors: {$errors}");

        return 0;
    }
}
