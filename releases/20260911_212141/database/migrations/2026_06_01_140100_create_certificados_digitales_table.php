<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados_digitales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('rnc_emisor', 20)->comment('RNC del emisor del certificado');
            $table->string('rnc_titular', 20)->comment('RNC del titular (empresa)');
            $table->string('archivo_path', 500)->comment('Ruta al archivo .p12/.pfx');
            $table->text('password_encrypted')->comment('Contraseña del certificado (encriptada)');
            $table->string('serial_number', 100)->nullable();
            $table->string('emisor_cert', 255)->nullable()->comment('Entidad emisora (ej: Digicert, Certec)');
            $table->dateTime('fecha_emision')->nullable();
            $table->dateTime('fecha_vencimiento');
            $table->boolean('activo')->default(true);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('activo');
            $table->index('fecha_vencimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados_digitales');
    }
};
