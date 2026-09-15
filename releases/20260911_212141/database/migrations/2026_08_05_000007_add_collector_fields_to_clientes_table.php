<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('collector_level')->default('casual')->after('sector_actividad');
            $table->json('preferred_mediums')->nullable()->after('collector_level');
            $table->string('communication_preference')->default('whatsapp')->after('preferred_mediums');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['collector_level', 'preferred_mediums', 'communication_preference']);
        });
    }
};
