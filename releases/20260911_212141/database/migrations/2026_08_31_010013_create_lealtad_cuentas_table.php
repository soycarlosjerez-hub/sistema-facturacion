<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lealtad_cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('cliente_id')->unique()->constrained('clientes')->cascadeOnDelete();
            $table->integer('puntos_acumulados')->default(0);
            $table->integer('puntos_canjeados')->default(0);
            $table->integer('puntos_vencidos')->default(0);
            $table->enum('nivel', ['bronce', 'plata', 'oro'])->default('bronce');
            $table->decimal('tasa_cambio', 5, 2)->default(1);
            $table->date('ultima_actividad')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lealtad_cuentas');
    }
};
