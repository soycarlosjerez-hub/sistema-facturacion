<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->date('trial_ends_at')->nullable()->after('activo');
            $table->date('trial_started_at')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'trial_started_at']);
        });
    }
};
