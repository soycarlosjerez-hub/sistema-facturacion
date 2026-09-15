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
        $firstInstance = DB::table('business_instances')->orderBy('id')->value('id');

        if (! $firstInstance) {
            return;
        }

        $almacenesSinTenant = DB::table('almacenes')->whereNull('tenant_id')->get();

        foreach ($almacenesSinTenant as $almacen) {
            $tenantFromMovimientos = DB::table('almacen_movimientos')
                ->where('almacen_id', $almacen->id)
                ->whereNotNull('tenant_id')
                ->value('tenant_id');

            DB::table('almacenes')
                ->where('id', $almacen->id)
                ->update(['tenant_id' => $tenantFromMovimientos ?? $firstInstance]);
        }
    }

    public function down(): void
    {
        DB::table('almacenes')->update(['tenant_id' => null]);
    }
};
