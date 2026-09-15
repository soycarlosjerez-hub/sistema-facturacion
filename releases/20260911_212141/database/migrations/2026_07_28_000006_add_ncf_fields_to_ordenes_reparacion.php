<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ordenes_reparacion', function (Blueprint $table) {
            $table->string('ncf_tipo', 10)->nullable()->after('total')
                ->comment('Tipo de NCF: G01, R01, IN01, ND01, NP01');
            $table->string('ncf_numero', 50)->nullable()->after('ncf_tipo');
            $table->date('ncf_vencimiento')->nullable()->after('ncf_numero');
            $table->string('tipo_comprobante', 50)->nullable()->after('ncf_vencimiento');
            $table->string('encf', 100)->nullable()->after('tipo_comprobante')
                ->comment('Número de confirmación electrónica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes_reparacion', function (Blueprint $table) {
            $table->dropColumn(['ncf_tipo', 'ncf_numero', 'ncf_vencimiento', 'tipo_comprobante', 'encf']);
        });
    }
};
