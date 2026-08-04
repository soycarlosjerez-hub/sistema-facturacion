<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->string('owner_email', 100)->nullable()->after('owner_user_id');
            $table->string('owner_nombre', 100)->nullable()->after('owner_email');
        });
    }

    public function down(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->dropIndex(['owner_email']);
            $table->dropColumn(['owner_email', 'owner_nombre']);
        });
    }
};
