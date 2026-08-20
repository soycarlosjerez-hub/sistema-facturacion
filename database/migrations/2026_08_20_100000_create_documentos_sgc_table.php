<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_sgc', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->string('categoria', 50)->default('procedimiento');
            $table->string('formato', 50)->default('pdf');
            $table->string('version', 20)->default('1.0');
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_revision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado', 20)->default('borrador');
            $table->json('versiones')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->unsignedBigInteger('modificado_por')->nullable();
            $table->unsignedBigInteger('aprobado_por')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('proveedor_id')->nullable()->comment('Relacionado con proveedor');
            $table->unsignedBigInteger('auditoria_id')->nullable()->comment('Relacionado con auditoría interna');
            $table->unsignedBigInteger('nc_id')->nullable()->comment('Relacionado con no conformidad');
            $table->unsignedBigInteger('riesgo_id')->nullable()->comment('Relacionado con riesgo');
            $table->unsignedBigInteger('capacitacion_id')->nullable()->comment('Relacionado con capacitación');
            $table->unsignedBigInteger('mejora_id')->nullable()->comment('Relacionado con mejora continua');
            $table->unsignedBigInteger('revision_direccion_id')->nullable()->comment('Relacionado con revisión por dirección');
            $table->string('archivo_path')->nullable();
            $table->string('archivo_original_name')->nullable();
            $table->string('archivo_mime_type')->nullable();
            $table->unsignedBigInteger('archivo_size_bytes')->nullable();
            $table->string('checksum_sha256')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['estado', 'categoria']);
            $table->index(['fecha_revision']);
            $table->index(['fecha_vencimiento']);
            $table->index(['tenant_id', 'estado']);
            $table->foreign('creado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modificado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprobado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tenant_id')->references('id')->on('business_instances')->onDelete('set null');
            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_sgc');
    }
};
