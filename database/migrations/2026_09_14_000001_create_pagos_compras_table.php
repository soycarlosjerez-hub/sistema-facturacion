<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("pagos_compras", function (Blueprint $table) {
            $table->id();
            $table->foreignId("tenant_id")->nullable()->constrained("business_instances")->nullOnDelete();
            $table->foreignId("compra_id")->nullable()->constrained("compras")->nullOnDelete();
            $table->decimal("monto", 10, 2)->default(0);
            $table->string("metodo_pago")->default("efectivo");
            $table->text("nota")->nullable();
            $table->timestamp("fecha_pago")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("pagos_compras");
    }
};
