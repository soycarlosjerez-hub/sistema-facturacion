<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('titulo');
            $table->string('codigo_unico')->unique();
            $table->string('slug')->unique()->nullable();
            $table->string('dimensiones')->nullable();
            $table->decimal('peso_kg', 8, 2)->nullable();
            $table->string('medium')->nullable();
            $table->string('technique')->nullable();
            $table->year('year_created')->nullable();
            $table->integer('edition_number')->nullable();
            $table->integer('edition_total')->nullable();
            $table->string('certificate_number')->nullable();
            $table->json('photos')->nullable();
            $table->string('condition_status')->default('excelente');
            $table->date('creation_date')->nullable();
            $table->json('exhibition_history')->nullable();
            $table->boolean('is_original')->default(true);
            $table->string('status')->default('disponible');
            $table->decimal('cost_materials', 12, 2)->nullable();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'medium']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obras');
    }
};
