<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('delivery_method')->nullable()->after('tipo_orden');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('delivery_method');
            $table->decimal('insurance_cost', 12, 2)->default(0)->after('shipping_cost');
            $table->decimal('packaging_cost', 12, 2)->default(0)->after('insurance_cost');
            $table->decimal('commission_amount', 12, 2)->default(0)->after('packaging_cost');
            $table->decimal('commission_percentage', 5, 2)->default(0)->after('commission_amount');
            $table->foreignId('gallery_id')->nullable()->after('commission_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['delivery_method', 'shipping_cost', 'insurance_cost', 'packaging_cost', 'commission_amount', 'commission_percentage', 'gallery_id']);
        });
    }
};
