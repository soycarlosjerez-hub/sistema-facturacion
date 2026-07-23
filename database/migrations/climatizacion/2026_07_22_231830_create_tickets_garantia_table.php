<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_garantia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('instalacion_id')->nullable()->constrained('instalaciones')->nullOnDelete();
            $table->foreignId('compra_original_id')->nullable()->constrained('compras')->nullOnDelete();
            $table->date('fecha_compra');
            $table->date('fecha_vencimiento_garantia');
            $table->enum('tipo_garantia', ['fabrica', 'instalacion']);
            $table->text('descripcion_problema');
            $table->enum('estado', ['abierto', 'evaluando', 'aprobado', 'rechazado', 'cerrado'])->default('abierto');
            $table->text('resultado_evaluacion')->nullable();
            $table->enum('accion', ['reparar', 'reemplazar', 'devolver'])->nullable();
            $table->foreignId('tecnico_asignado_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('cerrado_en')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('codigo');
            $table->index('estado');
            $table->index('business_instance_id');
            $table->index('cliente_id');
            $table->index('fecha_vencimiento_garantia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_garantia');
    }
};
