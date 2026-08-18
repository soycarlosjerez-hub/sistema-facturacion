<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instance_notification_settings', function (Blueprint $table) {
            $table->boolean('subscription_expiring')->default(true)->after('user_registered');
            $table->boolean('subscription_suspended')->default(true)->after('subscription_expiring');
        });
    }

    public function down(): void
    {
        Schema::table('instance_notification_settings', function (Blueprint $table) {
            $table->dropColumn(['subscription_expiring', 'subscription_suspended']);
        });
    }
};