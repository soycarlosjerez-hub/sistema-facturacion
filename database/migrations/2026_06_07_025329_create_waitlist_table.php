<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->string('cliente_nombre', 200);
            $table->string('cliente_telefono', 30)->nullable();
            $table->unsignedInteger('personas');
            $table->text('notas')->nullable();
            $table->string('estado', 20)->default('esperando'); // esperando, llamando, sentado, cancelado
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist_entries');
    }
};
