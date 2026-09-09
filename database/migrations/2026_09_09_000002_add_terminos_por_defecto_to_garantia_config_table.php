<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garantia_config', function (Blueprint $table) {
            $table->text('terminos_por_defecto')->nullable()->after('cobertura');
        });
    }

    public function down(): void
    {
        Schema::table('garantia_config', function (Blueprint $table) {
            $table->dropColumn('terminos_por_defecto');
        });
    }
};
