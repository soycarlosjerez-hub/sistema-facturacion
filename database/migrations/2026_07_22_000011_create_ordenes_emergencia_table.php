<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_emergencia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->enum('prioridad', ['critica', 'alta', 'media', 'baja'])->default('media');
            $table->enum('tipo_falla', ['sin_frio', 'sin_calor', 'fuga_gas', 'ruido_excesivo', 'cortocircuito', 'otro']);
            $table->string('direccion')->nullable();
            $table->string('contacto_telefono')->nullable();
            $table->enum('estado', ['reportada', 'asignada', 'en_camino', 'en_lugar', 'resuelta', 'cerrada'])->default('reportada');
            $table->text('descripcion')->nullable();
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('costo_estimado', 12, 2)->default(0);
            $table->decimal('costo_final', 12, 2)->default(0);
            $table->timestamp('sla_deadline')->nullable();
            $table->timestamp('respondida_en')->nullable();
            $table->timestamp('resuelta_en')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('codigo');
            $table->index('estado');
            $table->index('prioridad');
            $table->index('business_instance_id');
            $table->index('cliente_id');
            $table->index('tecnico_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_emergencia');
    }
};
