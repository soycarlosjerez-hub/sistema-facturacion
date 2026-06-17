<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->integer('pos_x')->nullable()->after('activa');
            $table->integer('pos_y')->nullable()->after('pos_x');
        });

        // Asignar posiciones iniciales - usar división entera compatible con SQLite y MySQL
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement("UPDATE mesas SET pos_x = (id % 5) * 160 + 20, pos_y = (id / 5) * 140 + 20");
        } else {
            DB::statement("UPDATE mesas SET pos_x = (id % 5) * 160 + 20, pos_y = FLOOR(id / 5) * 140 + 20");
        }
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropColumn(['pos_x', 'pos_y']);
        });
    }
};
