<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar columna tipo_servicio a la tabla productos.
     * Los servicios de lavadero se registran como productos con tipo_servicio = 'servicio'.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'tipo_servicio')) {
                $table->enum('tipo_servicio', ['general', 'servicio', 'producto'])
                    ->default('producto')->after('categoria_clima');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'tipo_servicio')) {
                $table->dropColumn('tipo_servicio');
            }
        });
    }
};
