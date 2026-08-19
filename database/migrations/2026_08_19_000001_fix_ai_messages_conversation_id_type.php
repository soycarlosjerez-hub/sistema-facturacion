<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_messages', function (Blueprint $table) {
            $table->dropForeign('ai_messages_conversation_id_foreign');
        });

        Schema::table('ai_messages', function (Blueprint $table) {
            $table->string('conversation_id', 36)->change();
        });
    }

    public function down(): void
    {
        Schema::table('ai_messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->change();
        });

        Schema::table('ai_messages', function (Blueprint $table) {
            $table->foreign('conversation_id')->references('id')->on('ai_conversations')->cascadeOnDelete();
        });
    }
};