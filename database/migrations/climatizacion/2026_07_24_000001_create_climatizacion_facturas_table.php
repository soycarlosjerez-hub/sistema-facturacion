<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('climatizacion_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Origen de la factura
            $table->enum('origen', ['mantenimiento', 'contrato_cuota', 'instalacion', 'emergencia'])->default('mantenimiento');
            $table->unsignedBigInteger('origen_id')->nullable(); // polymorphic-like reference

            // Datos de la factura
            $table->string('referencia')->nullable(); // NCF/NCF vinculado si ya existe factura DGII
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('itbis', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Estado
            $table->enum('estado', ['borrador', 'generada', 'enviada', 'anulada'])->default('borrador');

            // JSON con líneas de detalle
            $table->json('detalle')->nullable();

            $table->timestamps();

            $table->index('origen');
            $table->index('origen_id');
            $table->index('estado');
            $table->index('business_instance_id');
            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climatizacion_facturas');
    }
};
