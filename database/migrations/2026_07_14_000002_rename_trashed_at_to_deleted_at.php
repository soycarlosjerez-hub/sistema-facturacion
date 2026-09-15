<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('business_instances', 'deleted_at')) {
            return;
        }

        if (Schema::hasColumn('business_instances', 'trashed_at')) {
            try {
                DB::statement('ALTER TABLE business_instances RENAME COLUMN trashed_at TO deleted_at');
            } catch (\Throwable $e) {
                // SQLite fails on column rename when other tables have foreign key references.
                // In that case, create deleted_at directly (best effort).
                Schema::table('business_instances', function (Blueprint $table) {
                    $table->timestamp('deleted_at')->nullable()->after('setup_completed');
                });
            }
        } else {
            Schema::table('business_instances', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable()->after('setup_completed');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('business_instances', 'deleted_at')) {
            Schema::table('business_instances', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
};
