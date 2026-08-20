<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('cajas', 'allowed_comprobante_types')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->json('allowed_comprobante_types')->nullable()->after('activo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cajas', 'allowed_comprobante_types')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->dropColumn('allowed_comprobante_types');
            });
        }
    }
};
