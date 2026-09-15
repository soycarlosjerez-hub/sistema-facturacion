<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedTinyInteger('garantia_meses')->default(0)->after('garantia_dias');
        });

        DB::statement('UPDATE productos SET garantia_meses = CASE garantia_dias
            WHEN 0 THEN 0
            WHEN 30 THEN 1
            WHEN 60 THEN 2
            WHEN 90 THEN 3
            WHEN 180 THEN 6
            WHEN 365 THEN 12
            WHEN 540 THEN 18
            WHEN 730 THEN 24
            WHEN 1095 THEN 36
            WHEN 1500 THEN 60
            ELSE ROUND(garantia_dias / 30.44)
            END WHERE garantia_dias > 0');
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('garantia_meses');
        });
    }
};
