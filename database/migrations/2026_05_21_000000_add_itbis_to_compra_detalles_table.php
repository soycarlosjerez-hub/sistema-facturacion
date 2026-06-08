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
        if (Schema::hasTable('compra_detalles')) {
            Schema::table('compra_detalles', function (Blueprint $table) {
                if (!Schema::hasColumn('compra_detalles', 'itbis_porcentaje')) {
                    $table->decimal('itbis_porcentaje', 5, 2)
                          ->default(18.00)
                          ->after('precio_unitario')
                          ->comment('Porcentaje de ITBIS aplicado al producto');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('compra_detalles')) {
            Schema::table('compra_detalles', function (Blueprint $table) {
                $table->dropColumn('itbis_porcentaje');
            });
        }
    }
};
?>
