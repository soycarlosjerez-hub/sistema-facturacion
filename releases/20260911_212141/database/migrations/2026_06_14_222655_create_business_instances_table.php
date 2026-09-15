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
        Schema::create('business_instances', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('rnc')->nullable()->unique(); // RNC/Cédula fiscal
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('business_type_id')->constrained()->onDelete('restrict');
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('configuracion')->nullable(); // Configuración específica del negocio
            $table->boolean('activo')->default(true);
            $table->timestamp('fecha_vencimiento')->nullable(); // Para licencias/suscripciones
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_instances');
    }
};
