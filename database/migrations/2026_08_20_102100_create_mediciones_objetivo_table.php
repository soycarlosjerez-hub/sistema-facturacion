<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mediciones_objetivo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objetivo_calidad_id')->constrained('objetivos_calidad')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('valor', 12, 2);
            $table->decimal('cumplimiento', 5, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->foreign('registrado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediciones_objetivo');
    }
};
