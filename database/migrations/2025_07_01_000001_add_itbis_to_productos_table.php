<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'itbis_porcentaje')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->decimal('itbis_porcentaje', 5, 2)->default(18.00)->after('precio'); // Default 18% ITBIS
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('itbis_porcentaje');
        });
    }
};
