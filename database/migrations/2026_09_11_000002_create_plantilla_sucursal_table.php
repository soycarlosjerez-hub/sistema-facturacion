<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plantilla_impresion_id')->constrained('plantilla_impresiones')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->boolean('es_default')->default(false);
            $table->timestamps();

            $table->unique(['plantilla_impresion_id', 'sucursal_id'], 'ps_unique');
            $table->index('sucursal_id', 'ps_sucursal_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_sucursal');
    }
};
