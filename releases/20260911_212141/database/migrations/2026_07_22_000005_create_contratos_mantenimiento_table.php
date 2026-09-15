<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->enum('tipo_periodicidad', ['mensual', 'trimestral', 'semestral', 'anual']);
            $table->json('equipos_cubiertos')->nullable();
            $table->date('vigencia_desde');
            $table->date('vigencia_hasta');
            $table->decimal('valor_mensual', 12, 2)->default(0);
            $table->enum('estado', ['borrador', 'activo', 'vencido', 'cancelado'])->default('borrador');
            $table->boolean('incluye_visitas')->default(false);
            $table->unsignedSmallInteger('num_visitas_anuales')->default(0);
            $table->unsignedSmallInteger('visitas_realizadas')->default(0);
            $table->decimal('deducible', 12, 2)->default(0);
            $table->decimal('cobertura_maxima', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('codigo');
            $table->index('estado');
            $table->index('business_instance_id');
            $table->index('cliente_id');
            $table->index('vigencia_desde');
            $table->index('vigencia_hasta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos_mantenimiento');
    }
};
