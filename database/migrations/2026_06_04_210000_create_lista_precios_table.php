<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_precios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->date('vigencia_desde')->nullable();
            $table->date('vigencia_hasta')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('lista_precio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_precio_id')->constrained('lista_precios')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->decimal('precio', 12, 2);
            $table->timestamps();
            $table->unique(['lista_precio_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_precio_items');
        Schema::dropIfExists('lista_precios');
    }
};
