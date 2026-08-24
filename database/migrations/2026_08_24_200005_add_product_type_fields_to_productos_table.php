<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar campos de tipo de producto y línea de negocio al módulo de lavadero.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'tipo_producto')) {
                $table->enum('tipo_producto', ['fisico', 'consumible', 'servicio', 'digital'])
                    ->default('fisico')->after('categoria_clima');
            }

            if (!Schema::hasColumn('productos', 'linea_negocio')) {
                $table->enum('linea_negocio', ['alimentos', 'bebidas', 'accesorios', 'todos'])
                    ->default('todos')->after('tipo_producto');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'linea_negocio')) {
                $table->dropColumn('linea_negocio');
            }
            if (Schema::hasColumn('productos', 'tipo_producto')) {
                $table->dropColumn('tipo_producto');
            }
        });
    }
};
