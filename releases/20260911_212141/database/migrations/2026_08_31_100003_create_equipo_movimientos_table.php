<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos');
            $table->string('tipo_movimiento', 50);
            $table->integer('cantidad')->default(1);
            $table->text('motivo')->nullable();
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();

            $table->index(['tenant_id', 'equipo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_movimientos');
    }
};
