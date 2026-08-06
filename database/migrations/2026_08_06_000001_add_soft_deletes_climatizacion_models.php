<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade la columna soft-delete (deleted_at) a las tablas de climatización
     * cuyos modelos usan el trait SoftDeletes. La migración original que lo hacía
     * vive en database/migrations/climatizacion/ (subdirectorio no escaneado por
     * Laravel), por lo que las columnas nunca fueron creadas.
     */
    public function up(): void
    {
        $tables = [
            'servicios_domotica',
            'instalacion_equipo_domotico',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->timestamp('deleted_at')->nullable()->after('updated_at');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'servicios_domotica',
            'instalacion_equipo_domotico',
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