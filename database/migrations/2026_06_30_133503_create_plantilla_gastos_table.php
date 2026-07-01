<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla_gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('descripcion', 500)->nullable();
            $table->string('categoria', 100)->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->string('comprobante', 100)->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_gastos');
    }
};
