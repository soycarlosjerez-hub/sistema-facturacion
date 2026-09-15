<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Expand tipo enum with additional values
        DB::statement("ALTER TABLE garantias MODIFY COLUMN tipo ENUM('fabrica','extendida','servicio','reparacion','pieza') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Revert to original values
        DB::statement("ALTER TABLE garantias MODIFY COLUMN tipo ENUM('fabrica','extendida','servicio') NOT NULL");
    }
};
