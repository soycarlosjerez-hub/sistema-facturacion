<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('evaluaciones_periodicas_proveedores')) {
            return;
        }

        Schema::create('evaluaciones_periodicas_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id');
            $table->foreign('proveedor_id', 'fk_epp_prov_id')->references('id')->on('proveedores')->onDelete('cascade');
            $table->integer('periodo');
            $table->integer('evaluacion_general')->default(1)->comment('1-5');
            $table->integer('cumplimiento_ncf')->default(1)->comment('1-5');
            $table->integer('cumplimiento_calidad')->default(1)->comment('1-5');
            $table->integer('tiempo_entrega')->default(1)->comment('1-5');
            $table->integer('comunicacion')->default(1)->comment('1-5');
            $table->text('observaciones')->nullable();
            $table->string('estado', 20)->default('aprobado');
            $table->unsignedBigInteger('evaluado_por')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_periodicas_proveedores');
    }
};
