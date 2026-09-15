<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantilla_impresiones', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('configuracion');
            $table->string('color_primario')->default('#1a1a2e')->after('logo_path');
            $table->string('color_secundario')->default('#16213e')->after('color_primario');
            $table->string('formato_papel')->default('a4')->after('configuracion')->comment('a4, letter, ticket_80, ticket_58');
            $table->string('orientation')->default('portrait')->after('formato_papel')->comment('portrait, landscape');
            $table->boolean('mostrar_logo')->default(true)->after('incluir_pie');
            $table->boolean('mostrar_encabezado')->default(true)->after('mostrar_logo');
            $table->boolean('mostrar_pie')->default(true)->after('mostrar_encabezado');
            $table->boolean('mostrar_datos_cliente')->default(true)->after('mostrar_pie');
            $table->boolean('mostrar_datos_fiscales')->default(true)->after('mostrar_datos_cliente');
            $table->boolean('mostrar_columna_cantidad')->default(true)->after('mostrar_datos_fiscales');
            $table->boolean('mostrar_columna_precio')->default(true)->after('mostrar_columna_cantidad');
            $table->boolean('mostrar_columna_subtotal')->default(true)->after('mostrar_columna_precio');
            $table->boolean('mostrar_columna_itbis')->default(true)->after('mostrar_columna_subtotal');
            $table->boolean('mostrar_garantias')->default(true)->after('mostrar_columna_itbis');
            $table->boolean('mostrar_pagos')->default(true)->after('mostrar_garantias');
            $table->boolean('mostrar_notas')->default(true)->after('mostrar_pagos');
            $table->text('encabezado_texto')->nullable()->after('mostrar_notas');
            $table->text('pie_pagina_texto')->nullable()->after('encabezado_texto');
            $table->json('orden_campos')->nullable()->after('pie_pagina_texto');
            $table->boolean('modificable')->default(true)->after('orden_campos');
            $table->boolean('es_default')->default(false)->after('modificable');
            $table->string('modulo_target')->nullable()->after('es_default')->comment('ventas, compras, cotizaciones, devoluciones');
            $table->unsignedInteger('tipo_doc_target')->nullable()->after('modulo_target')->comment('1=ncf, 2=ecf');
        });
    }

    public function down(): void
    {
        Schema::table('plantilla_impresiones', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'color_primario',
                'color_secundario',
                'formato_papel',
                'orientation',
                'mostrar_logo',
                'mostrar_encabezado',
                'mostrar_pie',
                'mostrar_datos_cliente',
                'mostrar_datos_fiscales',
                'mostrar_columna_cantidad',
                'mostrar_columna_precio',
                'mostrar_columna_subtotal',
                'mostrar_columna_itbis',
                'mostrar_garantias',
                'mostrar_pagos',
                'mostrar_notas',
                'encabezado_texto',
                'pie_pagina_texto',
                'orden_campos',
                'modificable',
                'es_default',
                'modulo_target',
                'tipo_doc_target',
            ]);
        });
    }
};
