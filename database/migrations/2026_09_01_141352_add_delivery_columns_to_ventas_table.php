<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->after('delivery_fee')->constrained('delivery_drivers')->nullOnDelete();
            }
            if (!Schema::hasColumn('ventas', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('driver_id');
            }
            if (!Schema::hasColumn('ventas', 'delivery_zone_id')) {
                $table->foreignId('delivery_zone_id')->nullable()->after('delivery_address')->constrained('delivery_zones')->nullOnDelete();
            }
            if (!Schema::hasColumn('ventas', 'distancia_km')) {
                $table->decimal('distancia_km', 8, 2)->nullable()->after('delivery_zone_id');
            }
            if (!Schema::hasColumn('ventas', 'tarifa_delivery')) {
                $table->decimal('tarifa_delivery', 10, 2)->nullable()->after('distancia_km');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['delivery_zone_id']);
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['driver_id', 'delivery_address', 'delivery_zone_id', 'distancia_km', 'tarifa_delivery']);
        });
    }
};
