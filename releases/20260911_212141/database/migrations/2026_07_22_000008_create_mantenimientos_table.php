<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', ['preventivo', 'correctivo']);
            $table->enum('estado', ['pendiente', 'programada', 'en_curso', 'completado', 'cancelado'])->default('pendiente');
            $table->foreignId('contrato_mantenimiento_id')->nullable()->constrained('contratos_mantenimiento')->nullOnDelete();
            $table->text('descripcion_falla')->nullable();
            $table->text('solucion_aplicada')->nullable();
            $table->json('repuestos_usados')->nullable();
            $table->decimal('costo_repuestos', 12, 2)->default(0);
            $table->decimal('mano_de_obra', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->dateTime('programada_para')->nullable();
            $table->dateTime('completada_en')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('numero');
            $table->index('estado');
            $table->index('tipo');
            $table->index('business_instance_id');
            $table->index('cliente_id');
            $table->index('tecnico_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
