<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('orden_id')->nullable()->after('venta_id')->constrained('ordenes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['orden_id']);
            $table->dropColumn('orden_id');
        });
    }
};
