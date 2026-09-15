<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->date('fecha');
            $table->unsignedBigInteger('evaluado_por')->nullable();
            $table->boolean('documentos_cumplen')->default(true);
            $table->boolean('documentacion_completa')->default(true);
            $table->integer('calidad')->default(1)->comment('1-5');
            $table->integer('precio')->default(1)->comment('1-5');
            $table->integer('entrega_puntualidad')->default(1)->comment('1-5');
            $table->integer('servicio_soporte')->default(1)->comment('1-5');
            $table->integer('cumplimiento_normas')->default(1)->comment('1-5');
            $table->decimal('total_puntuacion', 5, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->string('estado', 20)->default('calificado');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('evaluado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_proveedores');
    }
};
