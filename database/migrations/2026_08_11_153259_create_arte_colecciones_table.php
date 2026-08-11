<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arte_colecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->string('tipo', 50)->nullable()->comment('reunion, tematica, temporal');
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arte_colecciones');
    }
};
