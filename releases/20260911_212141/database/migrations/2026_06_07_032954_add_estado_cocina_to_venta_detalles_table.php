<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->string('estado_cocina', 20)->default('pendiente')->after('notas');
            $table->timestamp('cocina_updated_at')->nullable()->after('estado_cocina');
        });
    }

    public function down(): void
    {
        Schema::table('venta_detalles', function (Blueprint $table) {
            $table->dropColumn(['estado_cocina', 'cocina_updated_at']);
        });
    }
};
