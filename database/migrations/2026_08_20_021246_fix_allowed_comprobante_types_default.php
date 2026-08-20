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
        // Set default value for all existing cajas (MySQL 5.7 doesn't support JSON default, so we use UPDATE)
        DB::table('cajas')
            ->whereNull('allowed_comprobante_types')
            ->update([
                'allowed_comprobante_types' => json_encode(['sin', 'ncf', 'ecf'])
            ]);

        // Add comment to the column
        try {
            DB::statement("ALTER TABLE cajas MODIFY allowed_comprobante_types JSON NULL COMMENT 'Tipos de comprobante permitidos en esta terminal/caja' AFTER activo");
        } catch (\Throwable $e) {
            // MySQL 5.7 may not support comments on JSON columns
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep the data but remove comment if possible
        try {
            DB::statement("ALTER TABLE cajas MODIFY allowed_comprobante_types JSON NULL AFTER activo");
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
