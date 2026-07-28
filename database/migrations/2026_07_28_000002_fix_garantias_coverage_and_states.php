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
        // Migrate existing ENUM values to numeric percentages
        DB::statement("UPDATE garantias SET cobertura = 100.00 WHERE cobertura = 'ambos'");
        DB::statement("UPDATE garantias SET cobertura = 50.00 WHERE cobertura IN ('piezas', 'mano_obra')");

        // Convert cobertura from ENUM to DECIMAL(10,2)
        DB::statement("ALTER TABLE garantias MODIFY COLUMN cobertura DECIMAL(10,2) DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset numeric values back to ENUM-compatible defaults
        DB::statement("UPDATE garantias SET cobertura = 'ambos' WHERE cobertura >= 90");
        DB::statement("UPDATE garantias SET cobertura = 'piezas' WHERE cobertura BETWEEN 40 AND 60 AND cobertura != 'ambos'");

        // Restore ENUM type
        DB::statement("ALTER TABLE garantias MODIFY COLUMN cobertura ENUM('piezas', 'mano_obra', 'ambos') DEFAULT 'ambos'");
    }
};
