<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promocions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->enum('tipo', ['porcentaje', 'monto', '2x1', 'envio_gratis', 'regalo']);
            $table->decimal('valor', 10, 2)->default(0);
            $table->enum('aplica_a', ['categoria', 'producto', 'todos'])->default('todos');
            $table->foreignId('aplica_a_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->date('valido_desde')->nullable();
            $table->date('valido_hasta')->nullable();
            $table->decimal('minimo_compra', 12, 2)->default(0);
            $table->integer('uso_maximo')->nullable();
            $table->integer('uso_actual')->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promocions');
    }
};
