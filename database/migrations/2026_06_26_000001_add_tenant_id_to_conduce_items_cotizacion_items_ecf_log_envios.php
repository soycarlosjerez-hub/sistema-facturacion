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

        DB::statement("UPDATE conduce_items ci LEFT JOIN conduces c ON c.id = ci.conduce_id SET ci.tenant_id = COALESCE(c.tenant_id, ?) WHERE ci.tenant_id IS NULL", [$fallbackId]);
        DB::statement("UPDATE cotizacion_items ci LEFT JOIN cotizaciones c ON c.id = ci.cotizacion_id SET ci.tenant_id = COALESCE(c.tenant_id, ?) WHERE ci.tenant_id IS NULL", [$fallbackId]);
        DB::statement("UPDATE ecf_log_envios el LEFT JOIN ecf_documentos ed ON ed.id = el.ecf_documento_id SET el.tenant_id = COALESCE(ed.tenant_id, ?) WHERE el.tenant_id IS NULL", [$fallbackId]);
    }

    public function down(): void
    {
        Schema::table('conduce_items', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('cotizacion_items', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('ecf_log_envios', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
    }
};
