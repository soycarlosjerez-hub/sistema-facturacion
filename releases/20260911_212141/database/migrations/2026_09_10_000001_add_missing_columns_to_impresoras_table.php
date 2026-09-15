<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('impresoras', 'tenant_id')) {
            Schema::table('impresoras', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('business_instances')->onDelete('set null');
            });
        }

        if (! Schema::hasColumn('impresoras', 'sucursal_id')) {
            Schema::table('impresoras', function (Blueprint $table) {
                $table->foreignId('sucursal_id')->nullable()->after('tenant_id')->constrained('sucursales')->onDelete('set null');
            });
        }

        if (! Schema::hasColumn('impresoras', 'configuracion')) {
            Schema::table('impresoras', function (Blueprint $table) {
                $table->json('configuracion')->nullable()->after('orden');
            });
        }

        if (! Schema::hasColumn('impresoras', 'created_at')) {
            Schema::table('impresoras', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // Migrar datos existentes: asignar tenant_id de los usuarios activos
        $result = DB::select("
            SELECT i.id, MAX(u.business_instance_id) as tenant_id
            FROM impresoras i
            INNER JOIN users u ON i.nombre LIKE CONCAT('%', u.name, '%') OR i.id = 1
            WHERE u.business_instance_id IS NOT NULL
            GROUP BY i.id
        ");

        foreach ($result as $row) {
            if ($row->tenant_id) {
                DB::table('impresoras')
                    ->where('id', $row->id)
                    ->whereNull('tenant_id')
                    ->update(['tenant_id' => $row->tenant_id]);
            }
        }

        // Asignar tenant_id por defecto a las impresoras sin tenant
        DB::table('impresoras')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => DB::table('business_instances')->first()->id]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('impresoras', 'configuracion')) {
            Schema::table('impresoras', function (Blueprint $table) {
                $table->dropColumn('configuracion');
            });
        }

        if (Schema::hasColumn('impresoras', 'sucursal_id')) {
            Schema::table('impresoras', function (Blueprint $table) {
                $table->dropForeign(['sucursal_id']);
                $table->dropColumn('sucursal_id');
            });
        }

        if (Schema::hasColumn('impresoras', 'tenant_id')) {
            Schema::table('impresoras', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }
    }
};
