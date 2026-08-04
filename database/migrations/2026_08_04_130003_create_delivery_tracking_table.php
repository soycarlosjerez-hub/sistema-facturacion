<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')
                ->constrained('ordenes')
                ->nullOnDelete();
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('delivery_drivers')
                ->nullOnDelete();
            $table->string('status', 30);
            $table->text('notas')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->foreignId('creado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['orden_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_tracking');
    }
};
