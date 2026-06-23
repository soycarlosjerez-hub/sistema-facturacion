<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('gastos', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('conduces', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('detalles_devolucion', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });
        Schema::table('sucursales', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
        });

        $firstInstance = DB::table('business_instances')->orderBy('id')->first();
        if ($firstInstance) {
            $id = $firstInstance->id;
            DB::table('proveedores')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('gastos')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('cotizaciones')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('conduces')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('devoluciones')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('detalles_devolucion')->whereNull('tenant_id')->update(['tenant_id' => $id]);
            DB::table('sucursales')->whereNull('tenant_id')->update(['tenant_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('proveedores', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('gastos', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('cotizaciones', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('conduces', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('devoluciones', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('detalles_devolucion', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
        Schema::table('sucursales', fn(Blueprint $t) => $t->dropConstrainedForeignId('tenant_id'));
    }
};
