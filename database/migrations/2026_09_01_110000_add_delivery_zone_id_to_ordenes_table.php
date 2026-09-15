<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (SafeAlterTable::hasColumn('ordenes', 'delivery_zone_id')) {
            return;
        }

        Schema::table('ordenes', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_zone_id')->nullable()->after('driver_id');
            $table->index('delivery_zone_id');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn('delivery_zone_id');
        });
    }
};
