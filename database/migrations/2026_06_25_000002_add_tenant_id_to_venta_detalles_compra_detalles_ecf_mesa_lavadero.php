<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('compra_detalles', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('ncf_sequences', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('secuencias_ecf', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('mesas', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('reservaciones', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('lavadero_citas', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('lavadero_servicios', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('lavadores', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });

        // Backfill — prefer parent table join, fallback to first instance
        $firstInstance = DB::table('business_instances')->orderBy('id')->first();
        $fallbackId = $firstInstance?->id;

        if ($fallbackId) {
            DB::table('venta_detalles')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(v.tenant_id, ' . $fallbackId . ') FROM ventas v WHERE v.id = venta_detalles.venta_id)')]);
            DB::table('compra_detalles')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(c.tenant_id, ' . $fallbackId . ') FROM compras c WHERE c.id = compra_detalles.compra_id)')]);
            DB::table('ecf_documentos')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(v.tenant_id, ' . $fallbackId . ') FROM ventas v WHERE v.id = ecf_documentos.venta_id)')]);
        }

        foreach (['ncf_sequences', 'secuencias_ecf', 'mesas', 'reservaciones', 'lavadero_citas', 'lavadero_servicios', 'lavadores'] as $table) {
            if ($fallbackId) {
                DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $fallbackId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('venta_detalles', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('compra_detalles', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('ncf_sequences', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('ecf_documentos', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('secuencias_ecf', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('mesas', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('reservaciones', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('lavadero_citas', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('lavadero_servicios', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('lavadores', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
    }
};
