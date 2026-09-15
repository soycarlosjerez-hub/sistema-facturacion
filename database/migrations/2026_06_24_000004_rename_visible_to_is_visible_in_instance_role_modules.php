<?php

use App\Support\SafeRenameColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instance_role_modules', function (Blueprint $table) {
            SafeRenameColumns::rename('instance_role_modules', 'visible', 'is_visible');
        });
    }

    public function down(): void
    {
        Schema::table('instance_role_modules', function (Blueprint $table) {
            SafeRenameColumns::rename('instance_role_modules', 'is_visible', 'visible');
        });
    }
};
