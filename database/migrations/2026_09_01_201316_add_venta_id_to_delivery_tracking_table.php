<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_tracking', function (Blueprint $table) {
            $table->foreignId('venta_id')->nullable()->after('orden_id')
                ->constrained('ventas')
                ->nullOnDelete();
            $table->index(['venta_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('delivery_tracking', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropIndex(['venta_id', 'status']);
            $table->dropColumn('venta_id');
        });
    }
};
