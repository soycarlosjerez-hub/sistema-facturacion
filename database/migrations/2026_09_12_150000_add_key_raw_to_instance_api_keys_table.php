<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instance_api_keys', function (Blueprint $table) {
            $table->string('key_raw', 128)->nullable()->unique()->after('key');
        });
    }

    public function down(): void
    {
        Schema::table('instance_api_keys', function (Blueprint $table) {
            $table->dropUnique(['key_raw']);
            $table->dropColumn('key_raw');
        });
    }
};
