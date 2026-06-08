<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecf_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('secuencia_ecf_id')->constrained('secuencias_ecf')->onDelete('restrict');
            $table->foreignId('certificado_digital_id')->nullable()->constrained('certificados_digitales')->nullOnDelete();

            $table->string('encf', 13)->comment('E-CF completo: E + tipo + 10 dígitos');
            $table->string('tipo_ecf', 3)->comment('E31, E32, etc.');
            $table->string('estado', 30)->default('borrador')
                ->comment('borrador, generado, enviado, aprobado, rechazado, anulado, expirado');

            $table->dateTime('fecha_emision');
            $table->dateTime('fecha_firma')->nullable();
            $table->dateTime('fecha_envio')->nullable();
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->dateTime('fecha_anulacion')->nullable();

            $table->decimal('monto_gravado_total', 14, 2)->default(0);
            $table->decimal('monto_exento_total', 14, 2)->default(0);
            $table->decimal('itbis_total', 14, 2)->default(0);
            $table->decimal('monto_total', 14, 2);

            $table->text('xml_path')->nullable()->comment('Ruta al XML firmado');
            $table->text('xml_content')->nullable()->comment('Contenido XML completo');
            $table->text('firma_digital')->nullable()->comment('Firma digital del e-CF');
            $table->string('codigo_seguridad', 100)->nullable()->comment('Código de seguridad para QR');

            $table->string('track_id_dgii', 100)->nullable()->comment('Track ID devuelto por DGII');
            $table->text('mensaje_dgii')->nullable();
            $table->integer('intentos_envio')->default(0);
            $table->text('motivo_anulacion')->nullable();
            $table->string('anulado_por_encf', 13)->nullable()->comment('NCF/Documento que anula a este');

            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('encf');
            $table->index('estado');
            $table->index('tipo_ecf');
            $table->index('fecha_emision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecf_documentos');
    }
};
