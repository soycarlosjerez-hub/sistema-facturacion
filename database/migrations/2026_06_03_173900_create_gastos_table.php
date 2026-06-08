<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->decimal('monto', 12, 2);
            $table->string('categoria')->nullable();
            $table->text('notas')->nullable();
            $table->date('fecha_gasto');
            $table->string('metodo_pago')->nullable();
            $table->string('comprobante')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('sesion_caja_id')->nullable()->constrained('sesion_cajas')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
