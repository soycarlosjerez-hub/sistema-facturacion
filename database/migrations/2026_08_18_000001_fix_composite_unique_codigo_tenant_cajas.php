<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('cajas'));

            if ($indexes->contains('name', 'cajas_codigo_unique')) {
                $table->dropUnique('cajas_codigo_unique');
            }

            if (!$indexes->contains('name', 'cajas_tenant_codigo_unique')) {
                $table->unique(['tenant_id', 'codigo'], 'cajas_tenant_codigo_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('cajas'));

            if ($indexes->contains('name', 'cajas_tenant_codigo_unique')) {
                $table->dropUnique('cajas_tenant_codigo_unique');
            }

            if (!$indexes->contains('name', 'cajas_codigo_unique')) {
                $table->unique(['codigo'], 'cajas_codigo_unique');
            }
        });
    }
};