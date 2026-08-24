<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar campos de línea de negocio y lavadero a los detalles de venta.
     * Permite distinguir entre servicio de lavadero, alimentos/bebidas, y accesorios.
     */
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_detalles', 'tipo_linea')) {
                $table->enum('tipo_linea', ['servicio', 'alimentos_bebidas', 'accesorios'])
                    ->nullable()->after('subtotal');
            }

            if (!Schema::hasColumn('venta_detalles', 'servicio_id')) {
                $table->foreignId('servicio_id')->nullable()->constrained('lavadero_servicios')->nullOnDelete()->after('producto_id');
            }

            if (!Schema::hasColumn('venta_detalles', 'lavador_id')) {
                $table->foreignId('lavador_id')->nullable()->constrained('lavadores')->nullOnDelete()->after('servicio_id');
            }

            // Índice para filtrar por tipo de línea
            if (!Schema::hasIndex('venta_detalles', ['tipo_linea'])) {
                $table->index('tipo_linea');
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (Schema::hasColumn('venta_detalles', 'tipo_linea')) {
                $table->dropColumn('tipo_linea');
            }
            if (Schema::hasColumn('venta_detalles', 'lavador_id')) {
                $table->dropForeign(['lavador_id']);
                $table->dropColumn('lavador_id');
            }
            if (Schema::hasColumn('venta_detalles', 'servicio_id')) {
                $table->dropForeign(['servicio_id']);
                $table->dropColumn('servicio_id');
            }
        });
    }
};
