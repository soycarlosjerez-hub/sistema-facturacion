<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Añade campos de prioridad y presupuesto a las órdenes de reparación.
     * Permite controlar la urgencia del servicio y gestionar presupuestos
     * aprobados por el cliente antes de iniciar la reparación.
     */
    public function up(): void
    {
        Schema::table('ordenes_reparacion', function (Blueprint $table) {
            // Prioridad de la orden de reparación
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])
                ->default('media')->after('estado');

            // Descripción textual del presupuesto presentado al cliente
            $table->text('presupuesto_texto')->nullable()->after('prioridad');

            // ¿El cliente aprobó el presupuesto?
            $table->boolean('presupuesto_aprobado')->default(false)->after('presupuesto_texto');

            // Fecha de aprobación del presupuesto
            $table->date('presupuesto_fecha')->nullable()->after('presupuesto_aprobado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes_reparacion', function (Blueprint $table) {
            $table->dropColumn([
                'prioridad',
                'presupuesto_texto',
                'presupuesto_aprobado',
                'presupuesto_fecha',
            ]);
        });
    }
};
