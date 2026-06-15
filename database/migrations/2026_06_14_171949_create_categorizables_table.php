<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorizables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('categorizable_type');           // 'App\Models\Restaurant', 'App\Models\Laundry', etc.
            $table->unsignedBigInteger('categorizable_id'); // FK to the business type instance
            $table->json('configuracion')->nullable();      // override/extiende config global para este tipo
            $table->boolean('soft_delete_enabled')->default(true); // soft delete opcional por tipo
            $table->timestamps();

            // Índices
            $table->unique(['category_id', 'categorizable_type', 'categorizable_id'], 'cat_cat_type_id_unique');
            $table->index(['categorizable_type', 'categorizable_id'], 'cat_cat_type_id_index');
            $table->index(['category_id'], 'cat_category_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorizables');
    }
};