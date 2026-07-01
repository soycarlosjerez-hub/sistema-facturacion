<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->foreignId('planta_gasto_id')->nullable()->after('tenant_id')->constrained('plantilla_gastos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropForeign(['planta_gasto_id']);
            $table->dropColumn('planta_gasto_id');
        });
    }
};
