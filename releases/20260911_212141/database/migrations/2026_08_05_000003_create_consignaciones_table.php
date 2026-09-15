<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignaciones', function (Blueprint $table) {
            $table->id();
            $table->string('galeria_nombre');
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('comision_percentage', 5, 2)->default(30);
            $table->string('estado', 50)->default('activa');
            $table->date('fecha_venta')->nullable();
            $table->decimal('precio_venta', 12, 2)->nullable();
            $table->decimal('comision_monto', 12, 2)->nullable();
            $table->boolean('pago_recibido')->default(false);
            $table->date('pago_fecha')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['estado', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignaciones');
    }
};
