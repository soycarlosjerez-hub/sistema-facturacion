<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            if (!Schema::hasColumn('compras', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('compras', 'itbis_total')) {
                $table->decimal('itbis_total', 10, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('compras', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('itbis_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'itbis_total', 'observaciones']);
        });
    }
};
