<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arte_consignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete();
            $table->foreignId('obra_id')->constrained('arte_obras')->cascadeOnDelete();
            $table->string('consignante', 200);
            $table->decimal('porcentaje_comision', 5, 2)->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 50)->default('activa')->comment('activa, completada, cancelada');
            $table->decimal('monto_entregado', 14, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arte_consignaciones');
    }
};
