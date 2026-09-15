<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('radio_km', 8, 2)->default(5);
            $table->decimal('tarifa_base', 10, 2)->default(0);
            $table->decimal('tarifa_por_km', 8, 2)->default(0);
            $table->integer('tiempo_estimado_minutos')->default(30);
            $table->json('zona_poligono')->nullable();
            $table->decimal('minimo_para_envio_gratis', 10, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
