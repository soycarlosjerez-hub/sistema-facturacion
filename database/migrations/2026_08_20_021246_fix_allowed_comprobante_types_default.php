<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SafeAlterTable::alter(
            'ALTER TABLE cajas MODIFY allowed_comprobante_types JSON NULL COMMENT \'Tipos de comprobante permitidos en esta terminal/caja\' AFTER activo'
        );
    }

    public function down(): void
    {
        SafeAlterTable::alter('ALTER TABLE cajas MODIFY allowed_comprobante_types JSON NULL AFTER activo');
    }
};
