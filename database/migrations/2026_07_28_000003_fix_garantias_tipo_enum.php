<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SafeAlterTable::alter("ALTER TABLE garantias MODIFY COLUMN tipo ENUM('fabrica','extendida','servicio','reparacion','pieza') NOT NULL");
    }

    public function down(): void
    {
        SafeAlterTable::alter("ALTER TABLE garantias MODIFY COLUMN tipo ENUM('fabrica','extendida','servicio') NOT NULL");
    }
};
