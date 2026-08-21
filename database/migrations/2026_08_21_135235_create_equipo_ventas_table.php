<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipo_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')
                  ->constrained('equipos')
                  ->cascadeOnDelete();
            $table->foreignId('venta_id')
                  ->constrained('ventas')
                  ->cascadeOnDelete();
            $table->decimal('precio_vendido', 10, 2);
            $table->bigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->unique(['equipo_id', 'venta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipo_ventas');
    }
};
