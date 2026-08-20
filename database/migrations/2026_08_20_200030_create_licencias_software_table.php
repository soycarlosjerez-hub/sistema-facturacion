<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla de licencias de software para gestionar claves de licencias,
     * usuarios asignados, fechas de vencimiento y plataformas compatibles.
     * Permite controlar el inventario de licencias de software de la tienda.
     */
    public function up(): void
    {
        Schema::create('licencias_software', function (Blueprint $table) {
            $table->id();

            // Relación con el producto de software (NULL si licencia sin producto asociado)
            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            // Clave única de la licencia (ej: XXXXX-XXXXX-XXXXX-XXXXX)
            $table->string('clave_licencia', 255)->unique();

            // Tipo/descripción de la licencia
            $table->string('tipo_licencia', 100)->nullable();

            // Usuario al que se asignó la licencia
            $table->string('usuario_asignado', 255)->nullable();

            // ¿La licencia está activa/vigente?
            $table->boolean('licencia_activa')->default(true);

            // Fecha de vencimiento de la licencia
            $table->date('fecha_vencimiento')->nullable();

            // Plataforma: Windows, macOS, Linux, Cloud, etc.
            $table->string('plataforma', 50)->nullable();

            // Notas adicionales sobre la licencia
            $table->text('notas')->nullable();

            // Multi-tenancy
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['producto_id', 'licencia_activa']);
            $table->index(['fecha_vencimiento', 'licencia_activa']);
            $table->index(['tenant_id', 'licencia_activa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencias_software');
    }
};
