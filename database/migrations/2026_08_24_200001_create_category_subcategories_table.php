<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('business_type_id')->nullable()->constrained('business_types')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('category_subcategories')->nullOnDelete();
            $table->string('nombre', 200);
            $table->smallInteger('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->json('configuracion')->nullable();
            $table->timestamps();

            $table->index(['category_id', 'business_type_id']);
            $table->index('parent_id');
            $table->unique(['category_id', 'business_type_id', 'parent_id', 'nombre'], 'cat_sub_unq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_subcategories');
    }
};
