<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->decimal('costo_mensual', 10, 2)->nullable()->after('configuracion');
            $table->boolean('bloqueado')->default(false)->after('costo_mensual');
            $table->string('motivo_bloqueo')->nullable()->after('bloqueado');
            $table->timestamp('bloqueado_en')->nullable()->after('motivo_bloqueo');
        });
    }

    public function down(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->dropColumn(['costo_mensual', 'bloqueado', 'motivo_bloqueo', 'bloqueado_en']);
        });
    }
};
