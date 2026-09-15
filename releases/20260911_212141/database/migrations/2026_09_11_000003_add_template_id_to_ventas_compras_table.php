<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('sucursal_id')
                ->constrained('plantilla_impresiones')->onDelete('set null');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('sucursal_id')
                ->constrained('plantilla_impresiones')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });
    }
};
