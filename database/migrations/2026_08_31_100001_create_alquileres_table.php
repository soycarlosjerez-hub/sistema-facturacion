<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquileres', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50);
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 20)->default('activa');
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('deposito', 12, 2)->default(0);
            $table->text('notas')->nullable();
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'estado']);
            $table->index(['tenant_id', 'cliente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres');
    }
};
