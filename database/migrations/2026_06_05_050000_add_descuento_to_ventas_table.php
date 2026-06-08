<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'descuento_tipo')) {
                $table->string('descuento_tipo', 20)->nullable()->after('descuento');
            }
            if (!Schema::hasColumn('ventas', 'descuento_motivo')) {
                $table->string('descuento_motivo', 200)->nullable()->after('descuento_tipo');
            }
            if (!Schema::hasColumn('ventas', 'notas')) {
                $table->string('notas', 500)->nullable()->after('descuento_motivo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['descuento_tipo', 'descuento_motivo', 'notas']);
        });
    }
};
