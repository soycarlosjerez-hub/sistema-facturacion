<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add cross-FKs for riesgos -> auditorias_internas and riesgos -> mejoras_continuas
        // Also add no_conformidades -> mejoras_continuas FK
        Schema::table('riesgos', function (Blueprint $table) {
            $table->foreign('auditoria_id', 'fk_ri_auditoria_id')->references('id')->on('auditorias_internas')->onDelete('set null');
            $table->foreign('mejora_continua_id', 'fk_ri_mejora_continua_id')->references('id')->on('mejoras_continuas')->onDelete('set null');
        });
        Schema::table('no_conformidades', function (Blueprint $table) {
            $table->foreign('mejora_continua_id', 'fk_nc_mc_id')->references('id')->on('mejoras_continuas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('riesgos', function (Blueprint $table) {
            $table->dropForeign(['auditoria_id']);
            $table->dropForeign(['mejora_continua_id']);
        });
    }
};
