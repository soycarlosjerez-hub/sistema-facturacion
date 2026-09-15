<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Duplicada con 2026_09_12_150000: si la columna ya existe, no agregar.
        if (Schema::hasColumn('instance_api_keys', 'key_raw')) {
            return;
        }

        Schema::table('instance_api_keys', function (Blueprint $table) {
            $table->string('key_raw', 128)->nullable()->unique()->after('key');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('instance_api_keys', 'key_raw')) {
            return;
        }

        Schema::table('instance_api_keys', function (Blueprint $table) {
            $table->dropUnique(['key_raw']);
            $table->dropColumn('key_raw');
        });
    }
};
