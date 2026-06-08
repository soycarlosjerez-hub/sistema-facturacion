<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->after('nombre')->unique();
            $table->string('ubicacion', 100)->nullable()->after('codigo');
            $table->boolean('activo')->default(true)->after('ubicacion');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->string('metodo_pago', 30)->nullable()->after('monto');
            $table->foreignId('caja_id')->nullable()->after('metodo_pago')->constrained('cajas')->nullOnDelete();
            $table->foreignId('sesion_caja_id')->nullable()->after('caja_id')->constrained('sesion_cajas')->nullOnDelete();
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('caja_id')->nullable()->after('user_id')->constrained('cajas')->nullOnDelete();
            $table->foreignId('sesion_caja_id')->nullable()->after('caja_id')->constrained('sesion_cajas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
            $table->dropForeign(['sesion_caja_id']);
            $table->dropColumn(['caja_id', 'sesion_caja_id']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
            $table->dropForeign(['sesion_caja_id']);
            $table->dropColumn(['metodo_pago', 'caja_id', 'sesion_caja_id']);
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropUnique(['codigo']);
            $table->dropColumn(['codigo', 'ubicacion', 'activo']);
        });
    }
};
