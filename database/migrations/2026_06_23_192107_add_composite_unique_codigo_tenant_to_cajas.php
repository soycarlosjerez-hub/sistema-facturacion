<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            if (Schema::hasColumn('cajas', 'tenant_id')) {
                $table->dropUnique(['codigo']);
                $table->unique(['tenant_id', 'codigo'], 'cajas_tenant_codigo_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            if (Schema::hasColumn('cajas', 'tenant_id')) {
                $table->dropUnique('cajas_tenant_codigo_unique');
                $table->unique(['codigo'], 'cajas_codigo_unique');
            }
        });
    }
};
