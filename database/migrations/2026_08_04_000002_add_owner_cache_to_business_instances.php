<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            if (!Schema::hasColumn('business_instances', 'owner_email')) {
                $table->string('owner_email', 100)->nullable()->after('owner_user_id');
            }
            if (!Schema::hasColumn('business_instances', 'owner_nombre')) {
                $table->string('owner_nombre', 100)->nullable()->after('owner_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            if (Schema::hasColumn('business_instances', 'owner_email')) {
                $table->dropColumn('owner_email');
            }
            if (Schema::hasColumn('business_instances', 'owner_nombre')) {
                $table->dropColumn('owner_nombre');
            }
        });
    }
};
