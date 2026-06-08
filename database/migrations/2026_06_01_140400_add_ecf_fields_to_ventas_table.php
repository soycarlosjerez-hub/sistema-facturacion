<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('tipo_comprobante', 10)->default('ncf')
                ->comment('ncf (formato anterior), ecf (electrónico), ticket, sin_comprobante')
                ->after('ncf_tipo');
            $table->string('encf', 13)->nullable()->comment('e-CF generado')
                ->after('tipo_comprobante');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['tipo_comprobante', 'encf']);
        });
    }
};
