<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('asistentes_revisiones_direccion')) {
            return;
        }

        Schema::create('asistentes_revisiones_direccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revision_direccion_id')->constrained('revisiones_direccion', 'fk_ar_d_id')->cascadeOnDelete();
            $table->unsignedBigInteger('usuario_id');
            $table->string('area_representada', 100)->nullable();
            $table->boolean('asisto')->default(false);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistentes_revisiones_direccion');
    }
};
