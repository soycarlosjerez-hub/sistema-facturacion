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
        Schema::table('historial_impresion', function (Blueprint $table) {
            $table->string('imprimible_type')->nullable()->change();
            $table->integer('imprimible_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_impresion', function (Blueprint $table) {
            $table->string('imprimible_type')->nullable(false)->change();
            $table->integer('imprimible_id')->nullable(false)->change();
        });
    }
};
