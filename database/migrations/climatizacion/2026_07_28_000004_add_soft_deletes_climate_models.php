<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add soft_delete column to all climatización/hvac related tables
     * that don't already have it.
     */
    public function up(): void
    {
        $tables = [
            'instalacion_equipo_domotico',
            'servicios_domotica',
            'climatizacion_facturas',
            'contratos_mantenimiento',
            'mantenimientos',
            'ordenes_emergencia',
            'instalaciones',
            'visitas_programadas',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                if (!Schema::hasColumn($table, 'deleted_at')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->timestamp('deleted_at')->nullable()->after('updated_at');
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'instalacion_equipo_domotico',
            'servicios_domotica',
            'climatizacion_facturas',
            'contratos_mantenimiento',
            'mantenimientos',
            'ordenes_emergencia',
            'instalaciones',
            'visitas_programadas',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('deleted_at');
                });
            }
        }
    }
};
