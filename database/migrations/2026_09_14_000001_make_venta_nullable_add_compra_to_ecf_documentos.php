<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los e-CF de compra (E41/E43/E44) no tienen venta asociada:
     * venta_id pasa a nullable y se agrega compra_id.
     */
    public function up(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
        });

        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->foreignId('venta_id')->nullable()->change();
            $table->foreignId('compra_id')->nullable()->constrained('compras')->nullOnDelete();
        });

        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->dropForeign(['compra_id']);
            $table->dropColumn('compra_id');
            $table->dropForeign(['venta_id']);
        });

        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->foreignId('venta_id')->nullable(false)->change();
        });

        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
        });
    }
};
