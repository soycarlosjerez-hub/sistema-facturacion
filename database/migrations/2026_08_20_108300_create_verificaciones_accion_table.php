<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('verificaciones_accion')) {
            return;
        }

        Schema::create('verificaciones_accion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accion_correctiva_id')->constrained('acciones_correctivas', 'fk_va_acr_id')->cascadeOnDelete();
            $table->text('descripcion_verificacion');
            $table->date('fecha_verificacion');
            $table->boolean('efectiva')->default(true);
            $table->string('resultado', 50)->default('aprobada');
            $table->text('evidencia')->nullable();
            $table->unsignedBigInteger('verificador_id')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('verificador_id', 'fk_va_verificador_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verificaciones_accion');
    }
};
