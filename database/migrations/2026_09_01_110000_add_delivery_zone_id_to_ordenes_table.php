<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ordenesColumns = DB::select("SHOW COLUMNS FROM ordenes");
        $ordenNames = array_column($ordenesColumns, 'Field');

        if (!in_array('delivery_zone_id', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->unsignedBigInteger('delivery_zone_id')->nullable()->after('driver_id');
                $table->index('delivery_zone_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn('delivery_zone_id');
        });
    }
};
