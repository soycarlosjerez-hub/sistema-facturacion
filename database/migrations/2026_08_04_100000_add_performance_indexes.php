<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        return DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]) !== [];
    }

    public function up(): void
    {
        if (!$this->indexExists('clientes', 'idx_clientes_tipo_cliente')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->index('tipo_cliente', 'idx_clientes_tipo_cliente');
            });
        }

        if (!$this->indexExists('proveedores', 'idx_proveedores_rnc')) {
            Schema::table('proveedores', function (Blueprint $table) {
                $table->index('rnc', 'idx_proveedores_rnc');
            });
        }

        if (!$this->indexExists('proveedores', 'idx_proveedores_tenant_id')) {
            Schema::table('proveedores', function (Blueprint $table) {
                $table->index('tenant_id', 'idx_proveedores_tenant_id');
            });
        }

        if (!$this->indexExists('gastos', 'idx_gastos_fecha_gasto')) {
            Schema::table('gastos', function (Blueprint $table) {
                $table->index('fecha_gasto', 'idx_gastos_fecha_gasto');
            });
        }

        if (!$this->indexExists('gastos', 'idx_gastos_categoria')) {
            Schema::table('gastos', function (Blueprint $table) {
                $table->index('categoria', 'idx_gastos_categoria');
            });
        }

        if (!$this->indexExists('gastos', 'idx_gastos_sucursal_id')) {
            Schema::table('gastos', function (Blueprint $table) {
                $table->index('sucursal_id', 'idx_gastos_sucursal_id');
            });
        }

        if (!$this->indexExists('cajas', 'idx_cajas_sucursal_id')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->index('sucursal_id', 'idx_cajas_sucursal_id');
            });
        }

        if (!$this->indexExists('cajas', 'idx_cajas_estado')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->index('estado', 'idx_cajas_estado');
            });
        }
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('idx_clientes_tipo_cliente');
        });

        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropIndex('idx_proveedores_rnc');
            $table->dropIndex('idx_proveedores_tenant_id');
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->dropIndex('idx_gastos_fecha_gasto');
            $table->dropIndex('idx_gastos_categoria');
            $table->dropIndex('idx_gastos_sucursal_id');
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropIndex('idx_cajas_sucursal_id');
            $table->dropIndex('idx_cajas_estado');
        });
    }
};
