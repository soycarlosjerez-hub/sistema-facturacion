<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Añade campos tecnológicos específicos al equipo existente.
     * Permite registrar especificaciones técnicas detalladas para laptops, desktops,
     * servidores, impresoras y demás dispositivos.
     */
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            // Tipo de dispositivo tecnológico
            $table->enum('tipo_dispositivo', [
                'celular', 'laptop', 'desktop', 'tablet',
                'servidor', 'impresora', 'monitor',
                'router', 'switch', 'camara', 'ups', 'otro'
            ])->default('celular')->after('estado');

            // Procesador
            $table->string('procesador', 200)->nullable()->after('tipo_dispositivo');

            // Memoria RAM
            $table->string('memoria_ram', 50)->nullable()->after('procesador');

            // Tipo de almacenamiento
            $table->enum('almacenamiento_tipo', ['HDD', 'SSD', 'NVMe', 'hybrid'])
                ->nullable()->after('memoria_ram');

            // Capacidad de almacenamiento
            $table->string('almacenamiento_capacidad', 50)->nullable()->after('almacenamiento_tipo');

            // Sistema operativo preinstalado
            $table->string('sistema_operativo', 100)->nullable()->after('almacenamiento_capacidad');

            // Descripción de puertos disponibles
            $table->text('puertos')->nullable()->after('sistema_operativo');

            // Peso en gramos
            $table->decimal('peso_gramos', 8, 2)->nullable()->after('puertos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_dispositivo',
                'procesador',
                'memoria_ram',
                'almacenamiento_tipo',
                'almacenamiento_capacidad',
                'sistema_operativo',
                'puertos',
                'peso_gramos',
            ]);
        });
    }
};
