<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('incumplimientos_proveedores')) {
            return;
        }

        Schema::create('incumplimientos_proveedores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluacion_proveedor_id')->nullable();
            $table->unsignedBigInteger('evaluado_proveedor_id')->nullable();
            $table->date('fecha_ocurrencia');
            $table->text('descripcion');
            $table->string('tipo', 50)->default('otro');
            $table->string('gravedad', 20)->default('moderada');
            $table->text('accion_inmediata')->nullable();
            $table->string('estado', 20)->default('abierta');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreignId('documento_sgc_id')->nullable();
            $table->foreign('documento_sgc_id', 'fk_ip_dsgc_id')->references('id')->on('documentos_sgc')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incumplimientos_proveedores');
    }
};
