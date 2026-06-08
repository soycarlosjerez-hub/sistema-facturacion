<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conduces', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            $table->date('fecha_entrega')->nullable();
            $table->timestamp('fecha_recibido')->nullable();
            
            // Relaciones
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('venta_id')->nullable()->constrained('ventas');
            
            // Datos del transporte
            $table->string('transportista', 200)->nullable();
            $table->string('vehiculo', 100)->nullable();
            $table->string('placa', 20)->nullable();
            $table->string('chofer', 200)->nullable();
            $table->string('chofer_cedula', 20)->nullable();
            
            // Direcciones
            $table->string('direccion_origen', 500)->nullable();
            $table->string('direccion_entrega', 500);
            $table->string('contacto_entrega', 200)->nullable();
            $table->string('telefono_entrega', 20)->nullable();
            $table->text('referencia')->nullable();
            
            // Estado
            $table->enum('estado', ['borrador', 'en_transito', 'entregado', 'devuelto', 'cancelado'])
                  ->default('borrador');
            
            // Recibido
            $table->string('recibido_por', 200)->nullable();
            $table->string('recibido_cedula', 20)->nullable();
            
            // Otros
            $table->text('observaciones')->nullable();
            $table->integer('total_items')->default(0);
            $table->decimal('peso_total', 10, 2)->nullable();
            
            $table->timestamps();
            
            $table->index(['estado', 'fecha']);
            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conduces');
    }
};
