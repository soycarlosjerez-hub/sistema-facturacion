<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terminales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->string('nombre', 100);
            $table->string('codigo', 50)->unique();
            $table->string('ubicacion', 200)->nullable();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terminales');
    }
};
