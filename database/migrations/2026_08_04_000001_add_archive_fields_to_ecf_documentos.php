<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->boolean('xml_archivado')->default(false)->after('xml_path');
            $table->string('xml_archivo_path', 500)->nullable()->after('xml_archivado');
            $table->dateTime('xml_archivado_en')->nullable()->after('xml_archivo_path');
            $table->dateTime('ultimo_informe_diario')->nullable()->after('fecha_anulacion');

            $table->index('xml_archivado');
            $table->index('ultimo_informe_diario');
        });
    }

    public function down(): void
    {
        Schema::table('ecf_documentos', function (Blueprint $table) {
            $table->dropIndex(['xml_archivado']);
            $table->dropIndex(['ultimo_informe_diario']);
            $table->dropColumn(['xml_archivado', 'xml_archivo_path', 'xml_archivado_en', 'ultimo_informe_diario']);
        });
    }
};
