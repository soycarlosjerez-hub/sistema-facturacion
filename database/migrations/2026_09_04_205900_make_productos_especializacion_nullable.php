<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SafeAlterTable::alter('ALTER TABLE productos MODIFY especializacion VARCHAR(50) NULL DEFAULT NULL');
    }

    public function down(): void
    {
        SafeAlterTable::alter("ALTER TABLE productos MODIFY especializacion ENUM('celular','accesorio','domotica','servicio','pieza') NOT NULL DEFAULT 'accesorio'");
    }
};
