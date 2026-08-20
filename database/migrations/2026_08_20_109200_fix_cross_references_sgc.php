<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add cross-FKs for riesgos -> auditorias_internas and riesgos -> mejoras_continuas
        // These referenced tables are created in later migration files (106000 and 109000)
        Schema::table('riesgos', function (Blueprint $table) {
            $table->foreign('auditoria_id', 'fk_ri_auditoria_id')->references('id')->on('auditorias_internas')->onDelete('set null');
            $table->foreign('mejora_continua_id', 'fk_ri_mejora_continua_id')->references('id')->on('mejoras_continuas')->onDelete('set null');
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
