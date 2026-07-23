<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_clima', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->enum('categoria', ['residencial', 'comercial', 'industrial'])->default('residencial');
            $table->string('icono')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->cascadeOnDelete();
            $table->index('slug');
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_clima');
    }
};
