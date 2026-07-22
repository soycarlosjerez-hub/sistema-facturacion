<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('lista_precio_logs')) {
            return;
        }
        Schema::create('lista_precio_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('business_instances', 'id')->nullOnDelete();
            $table->foreignId('lista_precio_id')->constrained('lista_precios')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->decimal('precio_anterior', 12, 2)->nullable();
            $table->decimal('precio_nuevo', 12, 2)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cambio_en', 50)->nullable(); // 'precio', 'vigencia', 'activo', 'codigo'
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'created_at']);
            $table->index(['lista_precio_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_precio_logs');
    }
};
