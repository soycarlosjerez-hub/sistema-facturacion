<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conduce_items', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('cotizacion_items', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('ecf_log_envios', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });

        // Backfill via parent table JOINs
        $firstInstance = DB::table('business_instances')->orderBy('id')->first();
        $fallbackId = $firstInstance?->id;

        if ($fallbackId) {
            DB::table('conduce_items')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(c.tenant_id, ' . $fallbackId . ') FROM conduces c WHERE c.id = conduce_items.conduce_id)')]);
            DB::table('cotizacion_items')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(c.tenant_id, ' . $fallbackId . ') FROM cotizaciones c WHERE c.id = cotizacion_items.cotizacion_id)')]);
            DB::table('ecf_log_envios')->whereNull('tenant_id')
                ->update(['tenant_id' => DB::raw('(SELECT COALESCE(ed.tenant_id, ' . $fallbackId . ') FROM ecf_documentos ed WHERE ed.id = ecf_log_envios.ecf_documento_id)')]);
        }
    }

    public function down(): void
    {
        Schema::table('conduce_items', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('cotizacion_items', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('ecf_log_envios', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
    }
};
