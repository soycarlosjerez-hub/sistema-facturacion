<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('evaluaciones_proveedores_documentos')) {
            return;
        }

        Schema::create('evaluaciones_proveedores_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_proveedor_id')->constrained('evaluaciones_proveedores', 'fk_epd_ep_id')->cascadeOnDelete();
            $table->string('nombre_doc', 200);
            $table->string('archivo_path')->nullable();
            $table->string('archivo_original_name')->nullable();
            $table->string('archivo_mime_type')->nullable();
            $table->unsignedBigInteger('archivo_size_bytes')->nullable();
            $table->boolean('aprobado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_proveedores_documentos');
    }
};
