<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquileres_inquilinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_instance_id')->constrained('business_instances')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('cedula')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres_inquilinos');
    }
};
