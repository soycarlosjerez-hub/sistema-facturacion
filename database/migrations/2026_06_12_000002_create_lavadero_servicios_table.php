<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lavadero_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 12, 2)->default(0);
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->smallInteger('duracion_minutos')->nullable();
            $table->string('categoria', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavadero_servicios');
    }
};
