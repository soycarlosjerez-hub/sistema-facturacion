<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->timestamp('trial_started_at')->nullable()->after('fecha_vencimiento');
            $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at');
        });

        // Backfill: las instancias existentes conservan su vencimiento actual como fin de prueba.
        DB::table('business_instances')
            ->whereNull('trial_started_at')
            ->whereNull('deleted_at')
            ->update([
                'trial_started_at' => now(),
                'trial_ends_at'    => DB::raw('fecha_vencimiento'),
            ]);
    }

    public function down(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->dropColumn(['trial_started_at', 'trial_ends_at']);
        });
    }
};