<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('almacenes', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('almacen_movimientos', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('sesion_cajas', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('cajas', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });

        // Backfill existing records — assumes all existing data belongs to instance 1
        $firstInstance = DB::table('business_instances')->orderBy('id')->first();
        if ($firstInstance) {
            $id = $firstInstance->id;
            DB::table('almacenes')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('almacen_movimientos')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('pagos')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('sesion_cajas')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('cajas')->whereNull('tenant_id')->update(['tenant_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('almacenes', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('almacen_movimientos', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('pagos', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('sesion_cajas', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('cajas', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
    }
};
