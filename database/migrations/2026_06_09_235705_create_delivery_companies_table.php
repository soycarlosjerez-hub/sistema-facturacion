<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_companies', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('nombre_corto', 30)->unique();
            $table->decimal('comision_porcentaje', 5, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_companies');
    }
};
