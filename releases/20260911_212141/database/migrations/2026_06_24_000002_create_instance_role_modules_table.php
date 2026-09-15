<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instance_role_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instance_role_id')->constrained('instance_roles')->cascadeOnDelete();
            $table->string('modulo_key');
            $table->boolean('visible')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->unique(['instance_role_id', 'modulo_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instance_role_modules');
    }
};
