<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if column is still bigint (needs migration)
        $colInfo = Schema::getConnection()->select("
            SELECT COLUMN_TYPE FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'ai_messages' 
            AND COLUMN_NAME = 'conversation_id'
        ");
        
        if (isset($colInfo[0]) && $colInfo[0]->COLUMN_TYPE === 'bigint unsigned') {
            // Try to drop FK, continue even if it fails (idempotent)
            try {
                Schema::table('ai_messages', function (Blueprint $table) {
                    $table->dropForeign('ai_messages_conversation_id_foreign');
                });
            } catch (\Exception $e) {
                // FK already removed, continue
            }

            Schema::table('ai_messages', function (Blueprint $table) {
                $table->string('conversation_id', 36)->change();
            });
        }
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