<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secuencias_ecf', function (Blueprint $table) {
            $table->dropUnique('secuencias_ecf_tipo_ecf_unique');
            $table->unique(['tenant_id', 'tipo_ecf']);
        });
    }

    public function down(): void
    {
        Schema::table('secuencias_ecf', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'tipo_ecf']);
            $table->unique('tipo_ecf');
        });
    }
};
