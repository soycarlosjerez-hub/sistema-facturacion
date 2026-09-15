<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('tipo_documento', 15)->default('ninguno')
                ->comment('rnc, cedula, pasaporte, ninguno')
                ->after('rnc_cedula');
            $table->string('tipo_cliente', 20)->default('consumo')
                ->comment('credito_fiscal, consumo, gubernamental, especial')
                ->after('tipo_documento');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['tipo_documento', 'tipo_cliente']);
        });
    }
};
