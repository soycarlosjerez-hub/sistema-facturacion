<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->foreignId('nota_credito_id')->nullable()->after('motivo_anulacion')
                ->constrained('ecf_documentos')->nullOnDelete();
            $table->foreignId('documento_original_id')->nullable()->after('nota_credito_id')
                ->constrained('ecf_documentos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->dropForeign(['nota_credito_id']);
            $table->dropForeign(['documento_original_id']);
            $table->dropColumn(['nota_credito_id', 'documento_original_id']);
        });
    }
};
