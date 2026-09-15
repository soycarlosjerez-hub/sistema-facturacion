<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("stock_transfers", function (Blueprint $table) {
            $table->id();
            $table->foreignId("tenant_id")->nullable()->constrained("business_instances")->nullOnDelete();
            $table->foreignId("sucursal_origen_id")->nullable()->constrained("sucursales")->nullOnDelete();
            $table->foreignId("sucursal_destino_id")->nullable()->constrained("sucursales")->nullOnDelete();
            $table->string("codigo")->unique();
            $table->string("estado")->default("borrador");
            $table->text("notas")->nullable();
            $table->foreignId("user_id")->nullable()->constrained("users")->nullOnDelete();
            $table->timestamps();
        });

        Schema::create("stock_transfer_details", function (Blueprint $table) {
            $table->id();
            $table->foreignId("transfer_id")->nullable()->constrained("stock_transfers")->cascadeOnDelete();
            $table->foreignId("producto_id")->nullable()->constrained("productos")->nullOnDelete();
            $table->integer("cantidad");
            $table->integer("recibido")->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("stock_transfer_details");
        Schema::dropIfExists("stock_transfers");
    }
};
