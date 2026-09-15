<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instance_error_logs', function (Blueprint $table) {
            $table->boolean('resolved')->default(false)->after('user_agent');
            $table->timestamp('resolved_at')->nullable()->after('resolved');
            $table->foreignId('resolved_by')->nullable()->after('resolved_at')
                ->constrained('users')->nullOnDelete();
            $table->index('resolved');
        });
    }

    public function down(): void
    {
        Schema::table('instance_error_logs', function (Blueprint $table) {
            $table->dropIndex(['resolved']);
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['resolved', 'resolved_at', 'resolved_by']);
        });
    }
};
