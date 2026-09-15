<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('delivery_company_id')
                ->nullable()
                ->after('tipo_orden')
                ->constrained('delivery_companies')
                ->nullOnDelete();
            $table->decimal('delivery_fee', 10, 2)
                ->default(0)
                ->after('delivery_company_id');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['delivery_company_id']);
            $table->dropColumn(['delivery_company_id', 'delivery_fee']);
        });
    }
};
