<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredientes', function (Blueprint $table) {
            if (! Schema::hasColumn('ingredientes', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ingredientes', function (Blueprint $table) {
            if (Schema::hasColumn('ingredientes', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
        });
    }
};
