<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arte_artistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete();
            $table->string('nombre', 200);
            $table->string('email', 200)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->text('bio')->nullable();
            $table->string('nacionalidad', 100)->nullable();
            $table->year('ano_nacimiento')->nullable();
            $table->string('foto', 300)->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arte_artistas');
    }
};
