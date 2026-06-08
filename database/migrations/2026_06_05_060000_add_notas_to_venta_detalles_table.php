<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_detalles', 'notas')) {
                $table->string('notas', 200)->nullable()->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->dropColumn('notas');
        });
    }
};
