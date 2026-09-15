<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tattoo_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('artist_id')->nullable()->constrained('tattoo_artists')->nullOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('estilo', 60)->nullable();
            $table->string('imagen_portada')->nullable();
            $table->json('galeria_imagenes')->nullable();
            $table->decimal('precio_minimo', 10, 2)->default(0);
            $table->decimal('precio_maximo', 10, 2)->default(0);
            $table->unsignedInteger('duracion_estimada_min')->default(60);
            $table->boolean('popular')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tattoo_designs');
    }
};
