<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Add tenant_id column for multi‑tenant scoping
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            $table->foreign('tenant_id')->references('id')->on('business_instances')->onDelete('cascade');

            // Ensure precio_compra has a default value to avoid null errors
            $table->decimal('precio_compra', 12, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            // Revert precio_compra to original definition (no default, assuming it was nullable false)
            $table->decimal('precio_compra', 12, 2)->nullable(false)->default(null)->change();
        });
    }
};
?>
