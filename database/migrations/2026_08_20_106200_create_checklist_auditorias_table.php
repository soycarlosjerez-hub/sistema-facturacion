<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditoria_interna_id')->constrained('auditorias_internas')->cascadeOnDelete();
            $table->string('item', 255);
            $table->text('criterio')->nullable();
            $table->integer('orden')->default(0);
            $table->string('resultado', 20)->default('pendiente');
            $table->text('evidencia')->nullable();
            $table->string('estatus', 20)->default('pendiente');
            $table->unsignedBigInteger('auditor_id')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_auditorias');
    }
};
