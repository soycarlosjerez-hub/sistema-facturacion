<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('revisiones_direccion_salidas')) {
            return;
        }

        Schema::create('revisiones_direccion_salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revision_direccion_id');
            $table->foreign('revision_direccion_id', 'fk_rds_d_id')->references('id')->on('revisiones_direccion')->onDelete('cascade');
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->text('documento_resultado')->nullable();
            $table->integer('orden')->default(0);
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisiones_direccion_salidas');
    }
};
