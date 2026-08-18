<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('incluir_kds')->default(true)->after('activo');
        });

        DB::table('productos')->whereNull('incluir_kds')->update(['incluir_kds' => true]);
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('incluir_kds');
        });
    }
};