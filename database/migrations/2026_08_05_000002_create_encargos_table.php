<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encargos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('boceto_path')->nullable();
            $table->boolean('sketch_approved')->default(false);
            $table->decimal('precio_total', 12, 2)->default(0);
            $table->decimal('deposito', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            $table->integer('avance_porcentaje')->default(0);
            $table->date('estimated_completion')->nullable();
            $table->date('actual_completion')->nullable();
            $table->string('status')->default('solicitado');
            $table->text('notas')->nullable();
            $table->json('progress_photos')->nullable();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encargos');
    }
};
