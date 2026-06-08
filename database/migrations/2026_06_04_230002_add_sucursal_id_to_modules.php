<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'ventas' => 'ventas',
            'compras' => 'compras',
            'conduces' => 'conduces',
            'gastos' => 'gastos',
            'cajas' => 'cajas',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'sucursal_id')) {
                    $t->foreignId('sucursal_id')->nullable()->after('id')->constrained('sucursales')->nullOnDelete();
                }
            });
        }

        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'sucursal_id')) {
                $t->foreignId('sucursal_id')->nullable()->after('id')->constrained('sucursales')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        $tables = ['users', 'ventas', 'compras', 'conduces', 'gastos', 'cajas'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (Schema::hasColumn($table, 'sucursal_id')) {
                    $t->dropForeign(['sucursal_id']);
                    $t->dropColumn('sucursal_id');
                }
            });
        }
    }
};
