<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impresoras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo_conexion', 20)->default('local'); // local, red, compartida, pdf
            $table->string('direccion_ip')->nullable();
            $table->integer('puerto')->nullable();
            $table->string('ruta_compartida')->nullable();
            $table->string('driver', 50)->default('escpos');
            $table->string('papel_tamano', 10)->default('80mm'); // 58mm, 80mm, letter
            $table->integer('caracteres_por_linea')->default(42);
            $table->boolean('auto_imprimir_ventas')->default(false);
            $table->boolean('auto_imprimir_cotizaciones')->default(false);
            $table->boolean('auto_imprimir_conduces')->default(false);
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('plantillas_impresion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 50)->unique(); // venta_ticket, cotizacion_ticket, conduce_ticket, ecf_pdf
            $table->string('modulo', 30); // ventas, cotizaciones, conduces, ecf
            $table->string('tipo_formato', 10)->default('ticket'); // ticket, pdf, html
            $table->boolean('incluir_logo')->default(true);
            $table->boolean('incluir_encabezado')->default(true);
            $table->boolean('incluir_pie')->default(true);
            $table->text('encabezado_personalizado')->nullable();
            $table->text('pie_personalizado')->nullable();
            $table->json('configuracion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('historial_impresion', function (Blueprint $table) {
            $table->id();
            $table->morphs('imprimible');
            $table->foreignId('impresora_id')->nullable()->constrained('impresoras')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tipo_documento', 30); // venta, cotizacion, conduce, ecf
            $table->string('documento_numero', 30)->nullable();
            $table->string('formato', 10)->default('ticket'); // ticket, pdf
            $table->integer('copias')->default(1);
            $table->string('papel_tamano', 10)->nullable();
            $table->boolean('exitoso')->default(true);
            $table->text('error_mensaje')->nullable();
            $table->integer('tamanio_bytes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_impresion');
        Schema::dropIfExists('plantillas_impresion');
        Schema::dropIfExists('impresoras');
    }
};
