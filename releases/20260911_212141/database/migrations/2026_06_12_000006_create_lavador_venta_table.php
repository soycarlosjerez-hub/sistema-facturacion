<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lavador_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lavador_id')->constrained('lavadores')->cascadeOnDelete();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->decimal('porcentaje_aplicado', 5, 2);
            $table->decimal('comision', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['lavador_id', 'venta_id']);
            $table->index('venta_id');
            $table->index('lavador_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavador_venta');
    }
};
