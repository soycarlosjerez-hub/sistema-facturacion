<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                if (!Schema::hasColumn('compras', 'fecha')) {
                    $table->date('fecha')
                          ->default(now())
                          ->comment('Fecha de la compra');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('compras')) {
            Schema::table('compras', function (Blueprint $table) {
                $table->dropColumn('fecha');
            });
        }
    }
};
?>
