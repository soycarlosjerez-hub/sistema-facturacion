<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lista_precios', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('lista_precio_items', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('mesa_categorias', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('split_bill_persons', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('waitlist_entries', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });

        $firstInstance = DB::table('business_instances')->orderBy('id')->first();
        $fallbackId = $firstInstance?->id;

        // Parent-JOIN backfill
        if ($fallbackId) {
            DB::table('lista_precio_items')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(lp.tenant_id, ' . $fallbackId . ') FROM lista_precios lp WHERE lp.id = lista_precio_items.lista_precio_id)')]);
            DB::table('split_bill_persons')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(v.tenant_id, ' . $fallbackId . ') FROM ventas v WHERE v.id = split_bill_persons.venta_id)')]);
            DB::table('waitlist_entries')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(s.tenant_id, ' . $fallbackId . ') FROM sucursales s WHERE s.id = waitlist_entries.sucursal_id)')]);
        }

        foreach (['lista_precios', 'mesa_categorias'] as $table) {
            if ($fallbackId) {
                DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $fallbackId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('lista_precios', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('lista_precio_items', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('mesa_categorias', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('split_bill_persons', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('waitlist_entries', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
    }
};
