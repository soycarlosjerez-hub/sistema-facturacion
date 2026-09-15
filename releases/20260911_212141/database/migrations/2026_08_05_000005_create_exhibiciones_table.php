<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhibiciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique()->nullable();
            $table->string('lugar');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('tipo')->default('individual');
            $table->boolean('activo')->default(true);
            $table->string('featured_image')->nullable();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activo', 'tenant_id']);
        });

        Schema::create('exhibicion_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibicion_id')->constrained('exhibiciones')->cascadeOnDelete();
            $table->foreignId('obra_id')->constrained('obras')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['exhibicion_id', 'obra_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhibicion_obras');
        Schema::dropIfExists('exhibiciones');
    }
};
