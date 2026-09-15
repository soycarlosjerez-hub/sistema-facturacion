<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('grupo')->nullable()->after('key');
            $table->renameColumn('key', 'clave');
            $table->renameColumn('value', 'valor');
            $table->string('tipo')->default('string')->after('valor');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['grupo', 'tipo']);
            $table->renameColumn('clave', 'key');
            $table->renameColumn('valor', 'value');
        });
    }
};
