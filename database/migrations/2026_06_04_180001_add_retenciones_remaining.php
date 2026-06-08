<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('proveedores', 'rnc')) {
            Schema::table('proveedores', function (Blueprint $table) {
                $table->string('rnc', 20)->nullable()->after('direccion');
                $table->string('tipo_persona', 20)->default('juridica')->after('rnc');
                $table->boolean('sujeto_retencion_isr')->default(true)->after('tipo_persona');
                $table->boolean('sujeto_retencion_itbis')->default(true)->after('sujeto_retencion_isr');
            });
        }
    }

    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            if (Schema::hasColumn('proveedores', 'rnc')) {
                $table->dropColumn('rnc');
            }
            if (Schema::hasColumn('proveedores', 'tipo_persona')) {
                $table->dropColumn('tipo_persona');
            }
            if (Schema::hasColumn('proveedores', 'sujeto_retencion_isr')) {
                $table->dropColumn('sujeto_retencion_isr');
            }
            if (Schema::hasColumn('proveedores', 'sujeto_retencion_itbis')) {
                $table->dropColumn('sujeto_retencion_itbis');
            }
        });
    }
};
