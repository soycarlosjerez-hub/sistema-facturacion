<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantilla_impresiones', function (Blueprint $table) {
            $table->boolean('mostrar_columna_ncf')->default(true)->after('mostrar_columna_itbis');
            $table->boolean('mostrar_columna_cajero')->default(true)->after('mostrar_columna_ncf');
            $table->boolean('mostrar_columna_sucursal')->default(true)->after('mostrar_columna_cajero');
        });
    }

    public function down(): void
    {
        Schema::table('plantilla_impresiones', function (Blueprint $table) {
            $table->dropColumn([
                'mostrar_columna_ncf',
                'mostrar_columna_cajero',
                'mostrar_columna_sucursal',
            ]);
        });
    }
};
