<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicada con 2026_08_20_200010: si la tabla ya existe, no recrear.
        if (Schema::hasTable('marca_tecnologicas')) {
            return;
        }

        Schema::create('marca_tecnologicas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('logo_url', 500)->nullable();
            $table->string('website', 500)->nullable();
            $table->string('pais', 100)->nullable();
            $table->string('contacto_email', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marca_tecnologicas');
    }
};
