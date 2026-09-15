<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla de marcas tecnológicas para gestionar marcas de productos
     * (Dell, HP, Lenovo, Cisco, Epson, Samsung, etc.).
     * Cada marca tiene su perfil con logo, sitio web y país de origen.
     */
    public function up(): void
    {
        Schema::create('marca_tecnologicas', function (Blueprint $table) {
            $table->id();

            // Nombre único de la marca
            $table->string('nombre', 100)->unique();

            // URL del logo de la marca
            $table->text('logo_url')->nullable();

            // Sitio web oficial de la marca
            $table->string('website', 255)->nullable();

            // País de origen de la marca
            $table->string('pais', 100)->nullable();

            // Contacto comercial del proveedor/distribuidor
            $table->string('contacto_email', 255)->nullable();

            // ¿La marca está activa?
            $table->boolean('activo')->default(true);

            // Orden de visualización en listados (más bajo = primero)
            $table->integer('orden')->default(0);

            // Multi-tenancy
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete()
                ->comment('NULL = marca global compartida por todos los tenants');

            $table->timestamps();

            $table->index(['nombre', 'activo']);
            $table->index(['tenant_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marca_tecnologicas');
    }
};
