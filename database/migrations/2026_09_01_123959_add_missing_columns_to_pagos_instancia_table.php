<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos_instancia', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos_instancia', 'recurrente')) {
                $table->boolean('recurrente')->default(false)->after('mes_pagado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pagos_instancia', function (Blueprint $table) {
            $table->dropColumn('recurrente');
        });
    }
};
