<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique()->comment('Formato: COT-YYYY-NNNNNN');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            
            $table->date('fecha');
            $table->date('fecha_validez');
            $table->enum('estado', ['borrador', 'enviada', 'aprobada', 'rechazada', 'vencida', 'convertida', 'anulada'])->default('borrador');
            
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('itbis', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            
            $table->text('notas')->nullable();
            $table->text('condiciones')->nullable()->comment('Términos y condiciones de la cotización');
            
            // Relación con venta (cuando se convierte)
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->timestamp('convertida_en')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['estado', 'fecha']);
            $table->index('fecha_validez');
            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
