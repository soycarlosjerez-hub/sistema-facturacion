<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objetivos_calidad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->string('indicador', 100);
            $table->decimal('meta', 12, 2)->comment('Valor objetivo');
            $table->decimal('valor_actual', 12, 2)->default(0);
            $table->string('unidad', 20)->default('%');
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->string('estado', 20)->default('en_curso');
            $table->decimal('cumplimiento', 5, 2)->default(0);
            $table->text('evidencias')->nullable();
            $table->text('acciones_mejora')->nullable();
            $table->foreignId('documento_sgc_id')->nullable()->constrained('documentos_sgc')->onDelete('set null');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('creado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objetivos_calidad');
    }
};
