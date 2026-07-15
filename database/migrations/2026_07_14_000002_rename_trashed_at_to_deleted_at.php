<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            if (Schema::hasColumn('business_instances', 'trashed_at')) {
                $table->renameColumn('trashed_at', 'deleted_at');
            } elseif (!Schema::hasColumn('business_instances', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('setup_completed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            if (Schema::hasColumn('business_instances', 'deleted_at')) {
                $table->renameColumn('deleted_at', 'trashed_at');
            }
        });
    }
};
