<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos_instancia', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('business_instance_id')->constrained('plans')->nullOnDelete();
            $table->string('referencia_externa')->nullable()->after('metodo_pago');
            $table->string('estado_pago')->default('completado')->after('referencia_externa');
        });
    }

    public function down(): void
    {
        Schema::table('pagos_instancia', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['referencia_externa', 'estado_pago']);
        });
    }
};
