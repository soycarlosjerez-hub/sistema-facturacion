<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_sgc_id')->constrained('documentos_sgc')->onDelete('cascade');
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->string('descripcionDocumento', 255)->nullable();
            $table->date('fechaCarga')->nullable();
            $table->date('fechaVencimiento')->nullable();
            $table->string('estado', 20)->default('vigente');
            $table->string('archivo_path')->nullable();
            $table->string('archivo_original_name')->nullable();
            $table->string('archivo_mime_type')->nullable();
            $table->unsignedBigInteger('archivo_size_bytes')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('subido_por')->nullable();
            $table->timestamps();

            $table->index(['estado', 'fechaVencimiento']);
            $table->index(['proveedor_id']);
            $table->foreign('subido_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_proveedores');
    }
};
