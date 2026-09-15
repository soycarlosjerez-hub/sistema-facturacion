<?php

use App\Support\SafeAlterTable;
use App\Support\SafeRenameColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renombrar primero para que las columnas nuevas apunten al nombre correcto
        SafeRenameColumns::rename('system_settings', 'key', 'clave');
        SafeRenameColumns::rename('system_settings', 'value', 'valor');

        Schema::table('system_settings', function (Blueprint $table) {
            if (SafeAlterTable::hasColumn('system_settings', 'grupo')) {
                return;
            }

            if (DB::getDriverName() === 'mysql') {
                $table->string('grupo')->nullable()->after('clave');
                $table->string('tipo')->default('string')->after('valor');
            } else {
                $table->string('grupo')->nullable();
                $table->string('tipo')->default('string');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::table('system_settings')->orderBy('clave')->get()->each(function ($row, $i) {
                DB::table('system_settings')
                    ->where('id', $row->id)
                    ->update(['grupo' => (string) floor($i / 10)]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['grupo', 'tipo']);
        });

        SafeRenameColumns::rename('system_settings', 'clave', 'key');
        SafeRenameColumns::rename('system_settings', 'valor', 'value');
    }
};
