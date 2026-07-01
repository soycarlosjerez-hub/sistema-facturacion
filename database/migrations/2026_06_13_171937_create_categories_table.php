<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->index();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->string('color', 7)->nullable();           // #RRGGBB
            $table->string('icono', 50)->nullable();          // bi-*
            $table->integer('orden')->default(0);
            $table->json('configuracion')->nullable();        // extensible
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['tenant_id', 'nombre']);
            $table->index(['tenant_id', 'activa']);
            $table->index(['tenant_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};