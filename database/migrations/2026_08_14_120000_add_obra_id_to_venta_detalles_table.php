<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('obra_id')->nullable()->after('producto_id');
            $table->foreign('obra_id')->references('id')->on('arte_obras')->nullOnDelete();
            $table->index('obra_id');
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->dropForeign(['obra_id']);
            $table->dropIndex(['obra_id']);
            $table->dropColumn('obra_id');
        });
    }
};