<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla_impresiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('business_instances')->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('modulo');
            $table->string('tipo_formato')->default('ticket');
            $table->boolean('incluir_logo')->default(true);
            $table->boolean('incluir_encabezado')->default(true);
            $table->boolean('incluir_pie')->default(true);
            $table->boolean('activo')->default(true);
            $table->unsignedTinyInteger('orden')->default(0);
            $table->json('configuracion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_impresiones');
    }
};
