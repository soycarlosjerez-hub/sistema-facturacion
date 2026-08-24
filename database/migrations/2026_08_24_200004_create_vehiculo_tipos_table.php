<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tipos de vehículo (sedan, suv, camioneta, moto, etc.) configurables por tenant.
     */
    public function up(): void
    {
        Schema::create('vehiculo_tipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete();
            $table->string('nombre', 100)->unique();
            $table->string('slug', 50)->unique();
            $table->string('icono', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->smallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculo_tipos');
    }
};
