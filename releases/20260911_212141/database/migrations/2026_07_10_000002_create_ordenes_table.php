<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->string('ncf', 11)->nullable();
            $table->string('ncf_tipo')->nullable();
            $table->date('ncf_vencimiento')->nullable();
            $table->string('tipo_comprobante', 10)->default('ncf');
            $table->string('encf', 13)->nullable();
            $table->foreignId('terminal_id')->nullable()->constrained('terminales')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('sesion_caja_id')->nullable()->constrained('sesion_cajas')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('tipo_orden', 20)->default('mostrador');
            $table->foreignId('entrega_empresa_id')->nullable()->constrained('delivery_companies')->nullOnDelete();
            $table->text('direccion_entrega')->nullable();
            $table->string('telefono_contacto', 30)->nullable();
            $table->dateTime('hora_retiro')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->string('descuento_tipo', 20)->nullable();
            $table->string('descuento_motivo', 200)->nullable();
            $table->decimal('propina', 12, 2)->default(0);
            $table->decimal('cargo_servicio', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
