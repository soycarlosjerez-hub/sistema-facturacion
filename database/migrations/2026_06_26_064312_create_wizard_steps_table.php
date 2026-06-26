<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wizard_steps', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('module_key');
            $table->string('label');
            $table->string('icon')->nullable();
            $table->boolean('required')->default(true);
            $table->boolean('skipable')->default(false);
            $table->string('entity_class')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wizard_steps');
    }
};
