<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lealtad_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuenta_id')->constrained('lealtad_cuentas')->cascadeOnDelete();
            $table->enum('tipo', ['ganar', 'canjear', 'vencer', 'ajuste']);
            $table->integer('cantidad');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lealtad_movimientos');
    }
};
