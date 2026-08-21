<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Permite vincular cada detalle de compra con un Equipo individual.
     * Esto es necesario cuando el negocio tiene facturacion_modo='equipos',
     * donde las compras se registran a nivel de equipo (IMEI/Serial)
     * y no a nivel de stock genérico.
     */
    public function up(): void
    {
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->foreignId('equipo_id')
                  ->nullable()
                  ->after('producto_id')
                  ->constrained('equipos')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
            $table->dropColumn('equipo_id');
        });
    }
};
