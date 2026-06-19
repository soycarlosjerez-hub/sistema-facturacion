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
        Schema::create('business_instance_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_instance_id')->constrained()->onDelete('cascade');
            $table->string('modulo_key');
            $table->boolean('visible')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->unique(['business_instance_id', 'modulo_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_instance_modules');
    }
};
