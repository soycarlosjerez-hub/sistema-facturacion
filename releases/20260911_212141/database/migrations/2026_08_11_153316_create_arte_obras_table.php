<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arte_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete();
            $table->string('titulo', 300);
            $table->text('descripcion')->nullable();
            $table->foreignId('artista_id')->constrained('arte_artistas')->cascadeOnDelete();
            $table->foreignId('coleccion_id')->nullable()->constrained('arte_colecciones')->nullOnDelete();
            $table->string('tecnica', 150)->nullable();
            $table->year('ano_creacion')->nullable();
            $table->string('dimensiones', 100)->nullable();
            $table->string('material', 200)->nullable();
            $table->decimal('precio_compra', 14, 2)->default(0);
            $table->decimal('precio_venta', 14, 2)->default(0);
            $table->string('estado', 50)->default('disponible')->comment('vendida, disponible, en_exhibicion, en_consulta');
            $table->date('fecha_adquisicion')->nullable();
            $table->string('imagen', 300)->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arte_obras');
    }
};
