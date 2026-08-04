<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('delivery_zone_id')
                ->nullable()
                ->after('delivery_company_id')
                ->constrained('delivery_zones')
                ->nullOnDelete();
            $table->decimal('distancia_km', 8, 2)
                ->nullable()
                ->after('delivery_zone_id');
            $table->decimal('tarifa_delivery', 10, 2)
                ->nullable()
                ->after('distancia_km');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['delivery_zone_id']);
            $table->dropColumn(['delivery_zone_id', 'distancia_km', 'tarifa_delivery']);
        });
    }
};
