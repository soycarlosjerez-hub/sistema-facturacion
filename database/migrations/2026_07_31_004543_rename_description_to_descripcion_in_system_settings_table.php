<?php

use App\Support\SafeRenameColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            SafeRenameColumns::rename('system_settings', 'description', 'descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            SafeRenameColumns::rename('system_settings', 'descripcion', 'description');
        });
    }
};
