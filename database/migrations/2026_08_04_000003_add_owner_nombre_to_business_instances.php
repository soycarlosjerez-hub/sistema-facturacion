<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('business_instances', 'owner_nombre')) {
            Schema::table('business_instances', function (Blueprint $table) {
                $table->string('owner_nombre', 100)->nullable()->after('owner_email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('business_instances', 'owner_nombre')) {
            Schema::table('business_instances', function (Blueprint $table) {
                $table->dropColumn('owner_nombre');
            });
        }
    }
};
