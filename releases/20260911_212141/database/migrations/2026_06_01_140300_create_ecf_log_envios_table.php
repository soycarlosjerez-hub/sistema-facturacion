<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecf_log_envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecf_documento_id')->constrained('ecf_documentos')->onDelete('cascade');
            $table->string('accion', 50)->comment('firmar, enviar, consultar, anular');
            $table->string('estado_resultado', 30)->comment('exito, error, pendiente');
            $table->integer('codigo_http')->nullable();
            $table->text('request_payload')->nullable();
            $table->text('response_payload')->nullable();
            $table->text('mensaje')->nullable();
            $table->integer('duracion_ms')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ecf_documento_id', 'accion']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecf_log_envios');
    }
};
