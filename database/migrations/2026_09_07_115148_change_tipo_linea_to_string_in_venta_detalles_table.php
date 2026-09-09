<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('venta_detalles', 'tipo_linea')) {
                $table->string('tipo_linea', 30)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('venta_detalles', 'tipo_linea')) {
                $table->enum('tipo_linea', ['servicio', 'alimentos_bebidas', 'accesorios'])->nullable()->change();
            }
        });
    }
};
