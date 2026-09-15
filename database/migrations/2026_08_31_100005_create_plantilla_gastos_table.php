<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicada: si la tabla ya existe (migración anterior), no recrear.
        if (Schema::hasTable('plantilla_gastos')) {
            return;
        }

        Schema::create('plantilla_gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->string('categoria', 100)->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->string('comprobante', 100)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('tenant_id')->constrained('business_instances');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_gastos');
    }
};
