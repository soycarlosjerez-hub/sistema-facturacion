<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de paquetes de lavadero que agrupan servicios, productos (alimentos/bebidas),
     * y accesorios vehiculares con precios y configuración.
     */
    public function up(): void
    {
        Schema::create('lavadero_paquetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_type_id')->nullable()->constrained('business_types')->nullOnDelete();
            $table->foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 12, 2);
            $table->decimal('precio_anterior', 12, 2)->nullable();
            $table->smallInteger('duracion_minutos')->nullable();
            $table->string('aplicable_a_tipo', 50)->default('todos'); // sedan, suv, camion, motos, todos
            $table->boolean('activo')->default(true);
            $table->integer('max_usos_cliente')->nullable();
            $table->unsignedInteger('veces_usado')->default(0);
            $table->smallInteger('orden')->default(0);
            $table->json('configuracion')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['business_type_id', 'activo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavadero_paquetes');
    }
};
