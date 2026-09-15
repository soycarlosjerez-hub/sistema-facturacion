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
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('label', 100);
            $table->string('icon', 50)->default('bi-grid');
            $table->string('categoria', 50)->default('otros');
            $table->string('sidebar_route', 100)->nullable();
            $table->string('sidebar_is_route', 100)->nullable();
            $table->string('sidebar_exact_route', 100)->nullable();
            $table->string('sidebar_url', 255)->nullable();
            $table->string('sidebar_permission', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
