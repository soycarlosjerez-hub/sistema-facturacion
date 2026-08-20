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
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'especializacion')) {
                $table->enum('especializacion', ['celular', 'accesorio', 'domotica', 'servicio', 'pieza'])
                    ->default('accesorio');
            }

            if (!Schema::hasColumn('productos', 'vendible_imei')) {
                $table->boolean('vendible_imei')->default(false);
            }

            if (!Schema::hasColumn('productos', 'requiere_imei')) {
                $table->boolean('requiere_imei')->default(false);
            }

            if (!Schema::hasColumn('productos', 'marca')) {
                $table->string('marca', 100)->nullable();
            }

            if (!Schema::hasColumn('productos', 'modelo')) {
                $table->string('modelo', 200)->nullable();
            }

            if (!Schema::hasColumn('productos', 'almacenamiento_gb')) {
                $table->string('almacenamiento_gb', 20)->nullable();
            }

            if (!Schema::hasColumn('productos', 'color')) {
                $table->string('color', 50)->nullable();
            }

            if (!Schema::hasColumn('productos', 'precio_servicio')) {
                $table->decimal('precio_servicio', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('productos', 'duracion_servicio_horas')) {
                $table->unsignedInteger('duracion_servicio_horas')->default(0);
            }

            if (!Schema::hasColumn('productos', 'garantia_dias')) {
                $table->unsignedInteger('garantia_dias')->default(30);
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
                'especializacion',
                'vendible_imei',
                'requiere_imei',
                'marca',
                'modelo',
                'almacenamiento_gb',
                'color',
                'precio_servicio',
                'duracion_servicio_horas',
                'garantia_dias',
            ]);
        });
    }
};
