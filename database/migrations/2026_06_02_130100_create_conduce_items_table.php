<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conduce_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conduce_id')->constrained('conduces')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            
            // Snapshot del producto al momento del conduce
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 255);
            $table->string('unidad', 20)->nullable();
            $table->text('descripcion')->nullable();
            
            // Cantidades
            $table->decimal('cantidad', 10, 2);
            $table->decimal('cantidad_recibida', 10, 2)->nullable();
            $table->decimal('peso', 10, 3)->nullable();
            
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->index('conduce_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conduce_items');
    }
};
