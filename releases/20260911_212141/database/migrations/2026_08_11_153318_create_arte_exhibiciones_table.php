<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arte_exhibiciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete();
            $table->string('nombre', 300);
            $table->text('descripcion')->nullable();
            $table->string('ubicacion', 300)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('arte_exhibicion_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibicion_id')->constrained('arte_exhibiciones')->cascadeOnDelete();
            $table->foreignId('obra_id')->constrained('arte_obras')->cascadeOnDelete();
            $table->string('ubicacion_en_sala')->nullable();
            $table->date('fecha_asignacion')->nullable();
            $table->unique(['exhibicion_id', 'obra_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arte_exhibicion_obras');
        Schema::dropIfExists('arte_exhibiciones');
    }
};
