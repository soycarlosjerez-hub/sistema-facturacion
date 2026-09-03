<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'serial_imei')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->string('serial_imei', 100)->nullable()->after('requiere_serial');
            });
        }
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('serial_imei');
        });
    }
};
