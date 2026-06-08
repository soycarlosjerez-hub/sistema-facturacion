<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // restaurante, retail, mayorista, servicios, mixto
            $table->string('nombre'); // Restaurante / Bar / Café
            $table->string('descripcion')->nullable();
            $table->string('color')->default('secondary'); // info, success, warning, primary, secondary
            $table->string('icon')->default('bi-grid'); // bi-cup-straw, bi-cart-plus, etc.
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->json('config')->nullable(); // config adicional: modulos_por_defecto, etc.
            $table->timestamps();
        });

        Schema::create('business_type_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_type_id')->constrained()->onDelete('cascade');
            $table->string('modulo_key'); // inventario, ventas, restaurante, etc.
            $table->boolean('visible')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->unique(['business_type_id', 'modulo_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_type_modules');
        Schema::dropIfExists('business_types');
    }
};