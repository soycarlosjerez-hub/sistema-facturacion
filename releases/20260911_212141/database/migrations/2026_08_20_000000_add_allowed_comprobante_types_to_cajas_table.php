<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->json('allowed_comprobante_types')
                ->nullable()
                ->after('activo');
        });

        // JSON columns can't have a DEFAULT constraint in MySQL 5.7+.
        // Set defaults for all existing rows via raw update.
        DB::connection()->table('cajas')->whereNull('allowed_comprobante_types')->update([
            'allowed_comprobante_types' => DB::raw('JSON_ARRAY("sin","ncf","ecf")'),
        ]);
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('allowed_comprobante_types');
        });
    }
};
