<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            if (!Schema::hasColumn('ordenes', 'driver_id')) {
                $table->foreignId('driver_id')->nullable()->after('telefono_contacto')->constrained('delivery_drivers')->nullOnDelete();
            }
            if (!Schema::hasColumn('ordenes', 'delivery_company_id')) {
                $table->foreignId('delivery_company_id')->nullable()->after('driver_id')->constrained('delivery_companies')->nullOnDelete();
            }
            if (!Schema::hasColumn('ordenes', 'tracking_status')) {
                $table->string('tracking_status')->default('creado')->after('driver_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropForeign(['delivery_company_id']);
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['driver_id', 'delivery_company_id', 'tracking_status']);
        });
    }
};
