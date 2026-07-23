<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instalaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('instalador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['pendiente', 'programada', 'en_progreso', 'completada', 'cancelada'])->default('pendiente');
            $table->string('direccion_instalacion')->nullable();
            $table->enum('tipo_inmueble', ['casa', 'apartamento', 'local', 'industrial'])->default('casa');
            $table->dateTime('programada_para')->nullable();
            $table->dateTime('completada_en')->nullable();
            $table->text('nota_interna')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('numero');
            $table->index('estado');
            $table->index('business_instance_id');
            $table->index('cliente_id');
            $table->index('instalador_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instalaciones');
    }
};
