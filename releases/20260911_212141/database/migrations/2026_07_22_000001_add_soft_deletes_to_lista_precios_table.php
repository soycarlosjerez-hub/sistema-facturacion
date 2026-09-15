<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('lista_precios', 'deleted_at')) {
            return;
        }

        Schema::table('lista_precios', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lista_precios', 'deleted_at')) {
            Schema::table('lista_precios', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
