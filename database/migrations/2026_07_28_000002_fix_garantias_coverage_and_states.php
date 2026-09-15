<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SafeAlterTable::alter("UPDATE garantias SET cobertura = 100.00 WHERE cobertura = 'ambos'");
        SafeAlterTable::alter("UPDATE garantias SET cobertura = 50.00 WHERE cobertura IN ('piezas', 'mano_obra')");
        SafeAlterTable::alter('ALTER TABLE garantias MODIFY COLUMN cobertura DECIMAL(10,2) DEFAULT 0');
    }

    public function down(): void
    {
        if (SafeAlterTable::hasColumn('garantias', 'cobertura')) {
            SafeAlterTable::alter("UPDATE garantias SET cobertura = 'ambos' WHERE cobertura >= 90");
            SafeAlterTable::alter("UPDATE garantias SET cobertura = 'piezas' WHERE cobertura BETWEEN 40 AND 60 AND cobertura != 'ambos'");
            SafeAlterTable::alter("ALTER TABLE garantias MODIFY COLUMN cobertura ENUM('piezas', 'mano_obra', 'ambos') DEFAULT 'ambos'");
        }
    }
};
