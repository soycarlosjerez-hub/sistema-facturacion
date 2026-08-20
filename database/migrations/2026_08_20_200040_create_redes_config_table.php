<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla de configuración de redes para clientes empresariales.
     * Permite registrar información de redes LAN/WiFi instaladas: direcciones
     * de red, VLANs, SSID, canales, rango DHCP y cobertura.
     */
    public function up(): void
    {
        Schema::create('redes_config', function (Blueprint $table) {
            $table->id();

            // Cliente que posee la red (NULL si es red genérica del proveedor)
            $table->foreignId('cliente_id')
                ->nullable()
                ->constrained('clientes')
                ->nullOnDelete();

            // Nombre descriptivo de la red
            $table->string('nombre_red', 200);

            // Dirección de red en notación CIDR (ej: 192.168.1.0/24)
            $table->string('direccion_red', 50)->nullable();

            // ID de VLAN asociada
            $table->integer('vlan_id')->nullable();

            // SSID de la red WiFi
            $table->string('ssid_wifi', 100)->nullable();

            // Canal WiFi (1, 6, 11 para 2.4GHz; 36-165 para 5GHz)
            $table->string('canal_wifi', 20)->nullable();

            // Descripción de la cobertura (oficinas, pisos, edificios)
            $table->text('cobertura')->nullable();

            // ¿DHCP activado en el router?
            $table->boolean('dhcp_activado')->default(true);

            // Rango de DHCP (ej: 192.168.1.100 - 192.168.1.200)
            $table->text('dhcp_rango')->nullable();

            // Notas adicionales
            $table->text('notas')->nullable();

            // ¿La red está activa?
            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Multi-tenancy
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete();

            $table->index(['cliente_id', 'activo']);
            $table->index(['ssid_wifi', 'activo']);
            $table->index(['tenant_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redes_config');
    }
};
