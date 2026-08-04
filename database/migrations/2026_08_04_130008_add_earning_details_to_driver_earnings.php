<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_earning_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained('business_instances')
                ->nullOnDelete();
            $table->foreignId('driver_earning_id')
                ->nullable()
                ->constrained('driver_earnings')
                ->nullOnDelete();
            $table->foreignId('orden_id')
                ->nullable()
                ->constrained('ordenes')
                ->nullOnDelete();
            $table->foreignId('venta_id')
                ->nullable()
                ->constrained('ventas')
                ->nullOnDelete();
            $table->decimal('monto_ganancia', 10, 2);
            $table->decimal('propina', 10, 2)->default(0);
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_earning_details');
    }
};
