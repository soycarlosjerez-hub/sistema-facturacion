<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillaImpresionesSeeder extends Seeder
{
    /**
     * Definición base de plantillas - se duplica para cada instancia de negocio.
     */
    private function definicionBase(): array
    {
        return [
            // ==================== VENTAS ====================

            [
                'codigo' => 'ticket_80_default',
                'nombre' => 'Ticket 80mm (Default)',
                'modulo' => 'ventas',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_80',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => '¡Gracias por su compra!',
                'pie_pagina_texto' => 'Este comprobante es válido según DGII. Conserve este ticket.',
                'color_primario' => '#2563eb',
                'color_secundario' => '#1d4ed8',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["fecha","cliente","productos","totales","pagos"]',
            ],

            [
                'codigo' => 'ticket_carta',
                'nombre' => 'Ticket Carta',
                'modulo' => 'ventas',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'letter',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Su compra nos hace feliz',
                'pie_pagina_texto' => 'Conserve este comprobante para cualquier reclamo.',
                'color_primario' => '#dc2626',
                'color_secundario' => '#b91c1c',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["fecha","cliente","productos","totales"]',
            ],

                        [
                'codigo' => 'ticket_58_mini',
                'nombre' => 'Ticket 58mm Mini',
                'modulo' => 'ventas',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_58',
                'orientation' => 'portrait',
                'incluir_logo' => false,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => false,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => false,
                'encabezado_texto' => '¡Vuelva pronto!',
                'pie_pagina_texto' => 'Documento válido ante la DGII.',
                'color_primario' => '#ea580c',
                'color_secundario' => '#c2410c',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["productos","totales"]',
            ],
            [
                'codigo' => 'ticket_80_express',
                'nombre' => 'Ticket 80mm Express',
                'modulo' => 'ventas',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_80',
                'orientation' => 'portrait',
                'incluir_logo' => false,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => false,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => false,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => false,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => false,
                'encabezado_texto' => 'PUNTO DE VENTA',
                'pie_pagina_texto' => 'Gracias por su compra | DGII',
                'color_primario' => '#16a34a',
                'color_secundario' => '#15803d',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["productos","totales","pagos"]',
            ],
            [
                'codigo' => 'ecf_ticket_80',
                'nombre' => 'e-CF Ticket 80mm',
                'modulo' => 'ventas',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_80',
                'orientation' => 'portrait',
                'tipo_doc_target' => 2,
                'incluir_logo' => false,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => false,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => false,
                'encabezado_texto' => 'e-CF DGII',
                'pie_pagina_texto' => 'Verifique en dgii.gob.do',
                'color_primario' => '#059669',
                'color_secundario' => '#047857',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["cliente","productos","totales","pagos"]',
            ],

// ==================== HISTORIAL DE VENTAS ====================
            [
                'codigo' => 'historial_ventas_a4_default',
                'nombre' => 'Historial Ventas A4 (Default)',
                'modulo' => 'historial_ventas',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
                'orientation' => 'landscape',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Reporte de ventas del período',
                'pie_pagina_texto' => 'Documento generado automáticamente. Verifique los datos.',
                'color_primario' => '#2563eb',
                'color_secundario' => '#1d4ed8',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","filtros","productos","resumen"]',
            ],
            [
                'codigo' => 'historial_ventas_carta_ejecutivo',
                'nombre' => 'Historial Ventas Ejecutivo',
                'modulo' => 'historial_ventas',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'letter',
                'orientation' => 'landscape',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => false,
                'encabezado_texto' => 'Resumen ejecutivo de ventas',
                'pie_pagina_texto' => 'Reporte confidencial - Uso interno.',
                'color_primario' => '#059669',
                'color_secundario' => '#047857',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","filtros","productos","resumen"]',
            ],
            [
                'codigo' => 'historial_ventas_a4_compacto',
                'nombre' => 'Historial Ventas Compacto',
                'modulo' => 'historial_ventas',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => false,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => false,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => false,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => false,
                'encabezado_texto' => '',
                'pie_pagina_texto' => 'Resumen de ventas.',
                'color_primario' => '#6b7280',
                'color_secundario' => '#4b5563',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","productos","resumen"]',
            ],

            // ==================== COMPRAS ====================
            [
                'codigo' => 'compra_a4_default',
                'nombre' => 'Comprobante de Compra A4 (Default)',
                'modulo' => 'compras',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => false,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Comprobante de compra registrado',
                'pie_pagina_texto' => 'Documento de compra - Uso interno.',
                'color_primario' => '#0891b2',
                'color_secundario' => '#0e7490',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","proveedor","productos","totales"]',
            ],
            [
                'codigo' => 'compra_carta',
                'nombre' => 'Comprobante de Compra Carta',
                'modulo' => 'compras',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'letter',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => false,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Hoja de compra registrada',
                'pie_pagina_texto' => 'Registro interno de compras.',
                'color_primario' => '#6366f1',
                'color_secundario' => '#4f46e5',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","proveedor","productos","totales"]',
            ],

            // ==================== COTIZACIONES ====================
            [
                'codigo' => 'cotizacion_a4_default',
                'nombre' => 'Cotización A4 (Default)',
                'modulo' => 'cotizaciones',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => true,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Cotización válida por 15 días',
                'pie_pagina_texto' => 'Esta cotización es una propuesta comercial y no constituye factura.',
                'color_primario' => '#4f46e5',
                'color_secundario' => '#3730a3',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","notas","validez"]',
            ],
            [
                'codigo' => 'cotizacion_ticket',
                'nombre' => 'Cotización Rápida (Ticket)',
                'modulo' => 'cotizaciones',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_80',
                'orientation' => 'portrait',
                'incluir_logo' => false,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => false,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => false,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Cotización impresa',
                'pie_pagina_texto' => 'Validez: 15 días. Consulte con nuestro equipo.',
                'color_primario' => '#0284c7',
                'color_secundario' => '#0369a1',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["cliente","productos","totales"]',
            ],

            // ==================== DEVOLUCIONES ====================
            [
                'codigo' => 'devolucion_ticket_default',
                'nombre' => 'Comprobante de Devolución Ticket (Default)',
                'modulo' => 'devoluciones',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_80',
                'orientation' => 'portrait',
                'incluir_logo' => false,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => false,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'DEVOLUCIÓN',
                'pie_pagina_texto' => 'Comprobante de devolución - Verificar con cliente.',
                'color_primario' => '#e11d48',
                'color_secundario' => '#be123c',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["cliente","productos","totales","pagos"]',
            ],
            [
                'codigo' => 'devolucion_a4',
                'nombre' => 'Comprobante de Devolución A4',
                'modulo' => 'devoluciones',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Nota de devolución autorizada',
                'pie_pagina_texto' => 'Documento válido para trámites ante la DGII.',
                'color_primario' => '#be123c',
                'color_secundario' => '#9f1239',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","motivo"]',
            ],

            // ==================== ORDENES ====================
            [
                'codigo' => 'orden_a4_default',
                'nombre' => 'Orden de Servicio A4 (Default)',
                'modulo' => 'ordenes',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => true,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Orden de servicio autorizado',
                'pie_pagina_texto' => 'El cliente acepta los términos y condiciones del servicio.',
                'color_primario' => '#0d9488',
                'color_secundario' => '#0f766e',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","servicios","totales","pagos","firmas"]',
            ],
            [
                'codigo' => 'orden_ticket',
                'nombre' => 'Orden de Servicio Ticket',
                'modulo' => 'ordenes',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_80',
                'orientation' => 'portrait',
                'incluir_logo' => false,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => false,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => false,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'ORDEN DE SERVICIO',
                'pie_pagina_texto' => 'Conservar como comprobante.',
                'color_primario' => '#059669',
                'color_secundario' => '#047857',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["cliente","servicios","totales"]',
            ],

            // ==================== CONDUCE ====================
            [
                'codigo' => 'conduce_ticket_default',
                'nombre' => 'Conduce de Entrega Ticket (Default)',
                'modulo' => 'conduces',
                'tipo_formato' => 'ticket',
                'formato_papel' => 'ticket_80',
                'orientation' => 'portrait',
                'incluir_logo' => false,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => false,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => false,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => false,
                'mostrar_columna_subtotal' => false,
                'mostrar_columna_itbis' => false,
                'mostrar_garantias' => false,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'CONSTANCIA DE ENTREGA',
                'pie_pagina_texto' => 'Firma del destinatario: _______________',
                'color_primario' => '#7c3aed',
                'color_secundario' => '#6d28d9',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["cliente","productos","notas","firma"]',
            ],

            // ==================== PRESUPUESTOS ====================
            [
                'codigo' => 'presupuesto_a4_default',
                'nombre' => 'Presupuesto A4 (Default)',
                'modulo' => 'presupuestos',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
                'orientation' => 'portrait',
                'incluir_logo' => true,
                'incluir_encabezado' => true,
                'incluir_pie' => true,
                'mostrar_logo' => true,
                'mostrar_encabezado' => true,
                'mostrar_pie' => true,
                'mostrar_datos_cliente' => true,
                'mostrar_datos_fiscales' => true,
                'mostrar_columna_cantidad' => true,
                'mostrar_columna_precio' => true,
                'mostrar_columna_subtotal' => true,
                'mostrar_columna_itbis' => true,
                'mostrar_garantias' => true,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Presupuesto sin compromiso de compra',
                'pie_pagina_texto' => 'Validez: 30 días naturales. Sujeto a disponibilidad.',
                'color_primario' => '#0891b2',
                'color_secundario' => '#0e7490',
                'es_default' => true,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","condiciones","notas"]',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las business instances
        $instances = DB::table('business_instances')
            ->where('aprobado', 1)
            ->pluck('id')
            ->toArray();

        if (empty($instances)) {
            $this->command->warn('No hay business instances aprobadas. El seeder no creará plantillas.');

            return;
        }

        $baseTemplates = $this->definicionBase();
        $createdTotal = 0;
        $updatedTotal = 0;
        $skippedTotal = 0;

        foreach ($instances as $tenantId) {
            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($baseTemplates as $templateData) {
                $existing = DB::table('plantilla_impresiones')
                    ->where('codigo', $templateData['codigo'])
                    ->where('tenant_id', $tenantId)
                    ->first();

                if ($existing) {
                    // Si es la primera instancia para este codigo, mantener como default
                    // De lo contrario, desactivar default
                    $data = $templateData;
                    if ($tenantId > $existing->tenant_id) {
                        // Solo actualizar si es la primera instancia procesada con este codigo
                        // Para las demás, desactivar es_default
                        if ($existing->es_default) {
                            $data['es_default'] = false;
                        }
                    }
                    DB::table('plantilla_impresiones')
                        ->where('id', $existing->id)
                        ->update($data);
                    $updated++;
                } else {
                    DB::table('plantilla_impresiones')->insert([
                        ...$templateData,
                        'tenant_id' => $tenantId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $created++;
                }
            }

            // Para cada codigo de plantilla, solo UNA instancia debe tener es_default = true
            // Aseguramos que la primera instancia con cada codigo sea el default
            foreach (array_unique(array_column($baseTemplates, 'codigo')) as $codigo) {
                $firstDefault = DB::table('plantilla_impresiones')
                    ->where('codigo', $codigo)
                    ->where('es_default', true)
                    ->orderBy('tenant_id')
                    ->first();

                if ($firstDefault && $firstDefault->tenant_id != min($instances)) {
                    // La primera instancia (menor id) debería ser el default
                    DB::table('plantilla_impresiones')
                        ->where('codigo', $codigo)
                        ->where('es_default', true)
                        ->update(['es_default' => false]);

                    DB::table('plantilla_impresiones')
                        ->where('codigo', $codigo)
                        ->where('tenant_id', min($instances))
                        ->update(['es_default' => true]);
                }
            }

            $createdTotal += $created;
            $updatedTotal += $updated;
            $skippedTotal += $skipped;
        }

        $this->command->info("Plantillas: {$createdTotal} creadas, {$updatedTotal} actualizadas para ".count($instances).' instancias.');
    }
}
