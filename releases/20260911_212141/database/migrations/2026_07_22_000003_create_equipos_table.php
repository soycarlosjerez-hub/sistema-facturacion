<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('equipos')) {
            return;
        }
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('serial_imei', 50)->unique();
            $table->string('serial_esn', 50)->nullable();
            $table->string('marca', 100);
            $table->string('modelo', 200)->nullable();
            $table->string('almacenamiento_gb', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->enum('estado', ['disponible', 'vendido', 'en_reparacion', 'dañado', 'reservado', 'mantenimiento'])
                ->default('disponible');
            $table->decimal('precio_compra', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->foreignId('comprado_a_proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->date('fecha_compra')->nullable();
            $table->string('factura_compra', 100)->nullable();
            $table->date('garantia_desde')->nullable();
            $table->date('garantia_hasta')->nullable();
            $table->enum('garantia_tipo', ['fabrica', 'extendida'])->default('fabrica');
            $table->boolean('bloqueado_icloud')->default(false);
            $table->boolean('bloqueado_fr')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('serial_esn');
            $table->index('estado');
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
