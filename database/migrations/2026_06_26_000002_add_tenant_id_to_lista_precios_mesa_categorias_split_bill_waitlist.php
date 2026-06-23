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
        DB::statement("UPDATE lista_precio_items li LEFT JOIN lista_precios lp ON lp.id = li.lista_precio_id SET li.tenant_id = COALESCE(lp.tenant_id, ?) WHERE li.tenant_id IS NULL", [$fallbackId]);
        DB::statement("UPDATE split_bill_persons sb LEFT JOIN ventas v ON v.id = sb.venta_id SET sb.tenant_id = COALESCE(v.tenant_id, ?) WHERE sb.tenant_id IS NULL", [$fallbackId]);
        DB::statement("UPDATE waitlist_entries we LEFT JOIN sucursales s ON s.id = we.sucursal_id SET we.tenant_id = COALESCE(s.tenant_id, ?) WHERE we.tenant_id IS NULL", [$fallbackId]);

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
