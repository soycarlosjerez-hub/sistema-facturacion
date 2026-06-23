<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instance_role_modules', function (Blueprint $table) {
            $table->renameColumn('visible', 'is_visible');
        });
    }

    public function down(): void
    {
        Schema::table('instance_role_modules', function (Blueprint $table) {
            $table->renameColumn('is_visible', 'visible');
        });
    }
};
