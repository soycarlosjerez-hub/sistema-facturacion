<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea el sistema de especialidades técnicas para tecnicos.
     * La tabla tecnicos ya tiene una columna 'especialidad' simple que se mantiene
     * para compatibilidad con datos existentes. Se añaden tablas separadas para
     * permitir múltiples especialidades por técnico mediante una relación Many-to-Many.
     */
    public function up(): void
    {
        // Tabla de especialidades técnicas disponibles
        Schema::create('tecnica_especialidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['nombre', 'activo']);
        });

        // Tabla pivote: relación muchos a muchos entre técnicos y especialidades
        Schema::create('tecnico_tecnica_especialidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tecnico_id')
                ->constrained('tecnicos')
                ->cascadeOnDelete();
            $table->foreignId('tecnica_especialidad_id')
                ->constrained('tecnica_especialidades')
                ->cascadeOnDelete();
            $table->boolean('es_primary')->default(false)
                ->comment('Especialidad principal del técnico');
            $table->timestamps();

            // Un técnico solo puede tener una especialidad principal
            $table->unique(['tecnico_id', 'tecnica_especialidad_id'],
                'tecnico_especialidad_unique');
            $table->index('tecnico_id');
            $table->index('tecnica_especialidad_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnico_tecnica_especialidad');
        Schema::dropIfExists('tecnica_especialidades');
    }
};
