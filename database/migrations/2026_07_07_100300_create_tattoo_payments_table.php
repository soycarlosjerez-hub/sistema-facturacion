<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tattoo_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('tattoo_appointments')->cascadeOnDelete();
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago', 30)->default('efectivo');
            $table->string('referencia', 100)->nullable();
            $table->string('tipo', 30)->default('saldo');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tattoo_payments');
    }
};
