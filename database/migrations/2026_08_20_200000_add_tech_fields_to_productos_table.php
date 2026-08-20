<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Añade campos específicos para tienda de tecnología a la tabla productos.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Tipo de producto tecnológico
            if (!Schema::hasColumn('productos', 'tipo_producto')) {
                $table->enum('tipo_producto', [
                    'hardware', 'software', 'accesorio', 'servicio',
                    'red', 'almacenamiento', 'impresora', 'consumible'
                ])->default('hardware')->after('descripcion');
            }

            // ¿Requiere serial/IMEI para rastreo?
            if (!Schema::hasColumn('productos', 'requiere_serial')) {
                $table->boolean('requiere_serial')->default(false)->after('tipo_producto');
            }

            // Categoría técnica específica (laptops, desktops, servidores, redes, cámaras, etc.)
            if (!Schema::hasColumn('productos', 'categoria_tecnica')) {
                $table->string('categoria_tecnica', 100)->nullable()->after('requiere_serial');
            }

            // Días de garantía por defecto para este producto
            if (!Schema::hasColumn('productos', 'garantia_dias')) {
                $table->integer('garantia_dias')->default(90)->after('categoria_tecnica');
            }

            // ¿Es producto de licencia de software?
            if (!Schema::hasColumn('productos', 'es_licencia')) {
                $table->boolean('es_licencia')->default(false)->after('garantia_dias');
            }

            // Tipo de licencia (solo para software/licencias)
            if (!Schema::hasColumn('productos', 'tipo_licencia')) {
                $table->enum('tipo_licencia', ['perpetua', 'suscripcion', 'open_source'])
                    ->nullable()->after('es_licencia');
            }

            // Máximo de usuarios para licencias concurrentes
            if (!Schema::hasColumn('productos', 'licencia_max_usuarios')) {
                $table->integer('licencia_max_usuarios')->nullable()->after('tipo_licencia');
            }

            // ¿Requiere configuración/instalación profesional?
            if (!Schema::hasColumn('productos', 'requires_setup')) {
                $table->boolean('requires_setup')->default(false)->after('licencia_max_usuarios');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_producto',
                'requiere_serial',
                'categoria_tecnica',
                'garantia_dias',
                'es_licencia',
                'tipo_licencia',
                'licencia_max_usuarios',
                'requires_setup',
            ]);
        });
    }
};
