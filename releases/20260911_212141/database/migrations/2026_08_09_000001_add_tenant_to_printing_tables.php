<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('impresoras', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('business_instances')->nullOnDelete();
            $table->index(['tenant_id', 'activo']);
        });

        Schema::table('plantillas_impresion', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('business_instances')->nullOnDelete();
            $table->index(['tenant_id', 'modulo', 'codigo']);
        });

        Schema::table('historial_impresion', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('business_instances')->nullOnDelete();
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('historial_impresion', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'created_at']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('plantillas_impresion', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'modulo', 'codigo']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('impresoras', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'activo']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
