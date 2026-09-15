<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->boolean('aprobado')->default(false)->after('setup_completed');
            $table->timestamp('aprobado_en')->nullable()->after('aprobado');
            $table->string('rechazo_motivo', 500)->nullable()->after('aprobado_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            $table->dropColumn(['aprobado', 'aprobado_en', 'rechazo_motivo']);
        });
    }
};
