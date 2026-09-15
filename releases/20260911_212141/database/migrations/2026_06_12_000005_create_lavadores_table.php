<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lavadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->string('tipo', 20)->default('temporal');
            $table->decimal('porcentaje', 5, 2)->default(30);
            $table->string('telefono', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('identificacion', 30)->nullable();
            $table->boolean('activo')->default(true);
            $table->text('notas')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('activo');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lavadores');
    }
};
