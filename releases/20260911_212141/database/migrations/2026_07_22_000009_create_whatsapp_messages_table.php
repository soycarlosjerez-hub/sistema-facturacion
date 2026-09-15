<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_phone', 30);
            $table->text('mensaje');
            $table->string('tipo', 50);
            $table->string('related_type', 50)->nullable();
            $table->unsignedInteger('related_id')->nullable();
            $table->boolean('enviado')->default(false);
            $table->text('respuesta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
