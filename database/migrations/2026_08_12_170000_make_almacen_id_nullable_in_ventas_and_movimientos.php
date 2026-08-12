<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite que las ventas funcionen sin almacenes configurados.
     */
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->foreignId('almacen_id')->nullable()->change();
        });
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->foreignId('almacen_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->foreignId('almacen_id')->nullable(false)->change();
        });
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->foreignId('almacen_id')->nullable(false)->change();
        });
    }
};