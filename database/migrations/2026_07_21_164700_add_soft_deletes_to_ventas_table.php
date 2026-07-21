<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('ncf')->nullable()->change();
            $table->string('ncf_tipo')->nullable()->change();
            $table->date('ncf_vencimiento')->nullable()->change();
            $table->string('tipo_comprobante')->nullable()->change();
            $table->string('encf')->nullable()->change();
            $table->foreignId('caja_id')->nullable()->change()->constrained()->nullOnDelete();
            $table->foreignId('sesion_caja_id')->nullable()->change()->constrained()->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->change()->constrained()->nullOnDelete();
            $table->foreignId('mesa_id')->nullable()->change()->constrained()->nullOnDelete();
            $table->string('notas')->nullable()->change();
            $table->string('tipo_orden')->nullable()->change();
            $table->decimal('propina', 10, 2)->default(0)->change();
            $table->foreignId('delivery_company_id')->nullable()->change()->constrained()->nullOnDelete();
            $table->decimal('delivery_fee', 10, 2)->default(0)->change();
            $table->decimal('cargo_servicio', 10, 2)->default(0)->change();
            $table->foreignId('vehiculo_id')->nullable()->change()->constrained()->nullOnDelete();
            $table->string('descuento_motivo')->nullable()->change();
            $table->string('descuento_tipo')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
