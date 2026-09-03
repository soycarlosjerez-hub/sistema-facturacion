<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ventas', 'general_descuento')) {
            Schema::table('ventas', function ($table) {
                $table->decimal('general_descuento', 12, 2)->default(0)->after('descuento');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ventas', 'general_descuento')) {
            Schema::table('ventas', function ($table) {
                $table->dropColumn('general_descuento');
            });
        }
    }
};
