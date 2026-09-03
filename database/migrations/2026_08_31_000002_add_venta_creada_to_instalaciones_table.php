<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Añade la columna venta_creada para rastrear si se generó la Venta
     * al completar la instalación.
     */
    public function up(): void
    {
        Schema::table('instalaciones', function (Blueprint $table) {
            $table->boolean('venta_creada')->default(false)->after('total')
                ->comment('Indica si se creó la Venta formal al completar la instalación');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instalaciones', function (Blueprint $table) {
            $table->dropColumn('venta_creada');
        });
    }
};
