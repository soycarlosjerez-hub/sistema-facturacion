<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('ai_conversations')
                ->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'tool_call', 'tool_response']);
            $table->text('content');
            $table->string('tool_name', 128)->nullable();
            $table->json('tool_arguments')->nullable();
            $table->json('tool_result')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};
