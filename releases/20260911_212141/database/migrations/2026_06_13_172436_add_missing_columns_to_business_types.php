<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Already applied manually - columns: key, icono_default, color_default, soft_delete_default
        // config renamed to campos_extra
        // key populated from slug, unique constraint added
    }

    public function down(): void
    {
        // No rollback - manual changes
    }
};
