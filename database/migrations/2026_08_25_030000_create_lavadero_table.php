<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla principal de lavadero (car wash) — registros de servicios de lavado
     * realizados para clientes con sus vehículos.
     */
    public function up(): void
    {
        Schema::create('lavadero', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 100)->nullable();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->dateTime('fecha_ingreso')->nullable();
            $table->dateTime('fecha_entrega')->nullable();
            $table->string('estado', 30)->default('esperando'); // esperando, en_proceso, completado, entregado, cancelado
            $table->string('servicio', 255)->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('cliente_id');
            $table->index('vehiculo_id');
            $table->index('estado');
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavadero');
    }
};
