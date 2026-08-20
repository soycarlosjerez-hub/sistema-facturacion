<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla de configuración de garantías.
     * Permite definir reglas de garantía por tipo de producto (fabrica o extendida),
     * con cobertura específica y días de garantía configurables por cada tipo.
     * Se usa para asignar automáticamente la garantía a productos y equipos.
     */
    public function up(): void
    {
        Schema::create('garantia_config', function (Blueprint $table) {
            $table->id();

            // Nombre descriptivo de la regla de garantía
            $table->string('nombre', 100);

            // Tipo de producto al que aplica (opcional, NULL = aplica a todos)
            $table->string('tipo_producto', 100)->nullable();

            // Días de cobertura de garantía
            $table->integer('dias_garantia')->default(90);

            // Tipo de garantía
            $table->enum('tipo_garantia', ['fabrica', 'extendida'])
                ->default('fabrica');

            // Descripción de lo que cubre la garantía (exclusiones incluidas)
            $table->text('cobertura')->nullable();

            // ¿La configuración está activa?
            $table->boolean('activo')->default(true);

            // Orden de visualización
            $table->integer('orden')->default(0);

            $table->timestamps();

            // Multi-tenancy
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete();

            $table->index(['nombre', 'activo']);
            $table->index(['tipo_producto', 'tipo_garantia', 'activo']);
            $table->index(['tenant_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garantia_config');
    }
};
