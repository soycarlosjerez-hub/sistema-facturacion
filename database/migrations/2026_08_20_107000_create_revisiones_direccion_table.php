<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('revisiones_direccion')) {
            return;
        }

        Schema::create('revisiones_direccion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('titulo', 255);
            $table->date('fecha');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('lugar')->nullable();
            $table->text('agenda')->nullable();
            $table->text('acuerdos')->nullable();
            $table->text('decisiones')->nullable();
            $table->string('estado', 20)->default('programada');
            $table->unsignedBigInteger('presidente_id')->nullable();
            $table->unsignedBigInteger('secretario_id')->nullable();
            $table->string('archivo_acta')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc', 'fk_rd_dsgc_id')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('presidente_id', 'fk_rd_presidente_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('secretario_id', 'fk_rd_secretario_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('creado_por', 'fk_rd_creado_por_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisiones_direccion');
    }
};
