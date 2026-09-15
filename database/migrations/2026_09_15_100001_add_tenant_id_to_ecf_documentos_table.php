<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            if (! Schema::hasColumn('ecf_documentos', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->constrained('business_instances', 'id')->nullOnDelete()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            if (Schema::hasColumn('ecf_documentos', 'tenant_id')) {
                $table->dropConstrainedForeignId('tenant_id');
            }
        });
    }
};
