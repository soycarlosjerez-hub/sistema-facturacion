<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand tipo enum with additional values
        DB::statement("ALTER TABLE garantias MODIFY COLUMN tipo ENUM('fabrica','extendida','servicio','reparacion','pieza') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original values
        DB::statement("ALTER TABLE garantias MODIFY COLUMN tipo ENUM('fabrica','extendida','servicio') NOT NULL");
    }
};
