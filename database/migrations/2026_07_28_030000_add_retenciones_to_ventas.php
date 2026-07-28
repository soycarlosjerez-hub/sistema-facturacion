<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ventas', 'retenciones')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->json('retenciones')->nullable()->after('tenant_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'retenciones')) {
                $table->dropColumn('retenciones');
            }
        });
    }
};