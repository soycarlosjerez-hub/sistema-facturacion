<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redes_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->string('nombre_red', 255);
            $table->string('direccion_red', 255)->nullable();
            $table->integer('vlan_id')->nullable();
            $table->string('ssid_wifi', 255)->nullable();
            $table->integer('canal_wifi')->nullable();
            $table->string('cobertura', 100)->nullable();
            $table->boolean('dhcp_activado')->default(true);
            $table->string('dhcp_rango', 100)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'cliente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redes_config');
    }
};
