<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change the `value` column to TEXT to ensure it can store long strings like business names.
        Schema::table('system_settings', function (Blueprint $table) {
            $table->text('value')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to VARCHAR(255) if needed.
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('value', 255)->change();
        });
    }
};
