<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'activo')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->boolean('activo')->default(true)->after('stock_minimo');
            });
        }

        if (!Schema::hasColumn('productos', 'incluir_kds')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->boolean('incluir_kds')->default(true)->after('activo');
            });
        }
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('incluir_kds', 'activo');
        });
    }
};
