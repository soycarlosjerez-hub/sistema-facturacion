<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('climatizacion_facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50);
            $table->foreignId('instalacion_id')->nullable()->constrained('instalaciones');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('itbis', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('estado', 20)->default('borrador');
            $table->string('metodo_pago', 50)->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climatizacion_facturas');
    }
};
