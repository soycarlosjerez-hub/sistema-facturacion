<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillaPresupuestosSeeder extends Seeder
{
    private function definicionPresupuestos(): array
    {
        return [
            // A4 Landscape - para presupuestos extensos
            [
                'codigo' => 'presupuesto_a4_landscape',
                'nombre' => 'Presupuesto A4 Horizontal',
                'modulo' => 'presupuestos',
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
                'mostrar_garantias' => true,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Presupuesto con detalles completos',
                'pie_pagina_texto' => 'Validez: 30 días. Precio sujeto a cambios.',
                'color_primario' => '#0891b2',
                'color_secundario' => '#06b6d4',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","condiciones","notas","validez"]',
            ],

            // Letter - formato carta americano
            [
                'codigo' => 'presupuesto_letter',
                'nombre' => 'Presupuesto Carta',
                'modulo' => 'presupuestos',
                'tipo_formato' => 'pdf',
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
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Cotización formal',
                'pie_pagina_texto' => 'Documento confidencial. Válida por 15 días.',
                'color_primario' => '#4338ca',
                'color_secundario' => '#3730a3',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","notas"]',
            ],

            // Ejecutivo - formato minimalista
            [
                'codigo' => 'presupuesto_ejecutivo',
                'nombre' => 'Presupuesto Ejecutivo',
                'modulo' => 'presupuestos',
                'tipo_formato' => 'pdf',
                'formato_papel' => 'a4',
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
                'mostrar_columna_itbis' => false,
                'mostrar_garantias' => false,
                'mostrar_pagos' => false,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Propuesta comercial',
                'pie_pagina_texto' => 'Para consultas, contacte a nuestro equipo.',
                'color_primario' => '#1e293b',
                'color_secundario' => '#334155',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","condiciones"]',
            ],

            // Comercial - con condiciones de pago detalladas
            [
                'codigo' => 'presupuesto_comercial',
                'nombre' => 'Presupuesto Comercial',
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
                'mostrar_garantias' => false,
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Presupuesto con opciones de pago',
                'pie_pagina_texto' => 'Consulte nuestras condiciones de financiamiento.',
                'color_primario' => '#059669',
                'color_secundario' => '#047857',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","pagos","condiciones","notas"]',
            ],

            // Proforma - formato tipo factura proforma
            [
                'codigo' => 'presupuesto_proforma',
                'nombre' => 'Presupuesto Proforma',
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
                'mostrar_notas' => false,
                'encabezado_texto' => 'Presupuesto Proforma - No constituye factura',
                'pie_pagina_texto' => 'Este documento es una propuesta formal de venta.',
                'color_primario' => '#7c3aed',
                'color_secundario' => '#6d28d9',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","validez"]',
            ],

            // Premium - diseño corporativo completo
            [
                'codigo' => 'presupuesto_premium',
                'nombre' => 'Presupuesto Premium Corporativo',
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
                'mostrar_pagos' => true,
                'mostrar_notas' => true,
                'encabezado_texto' => 'Presupuesto Premium - Documento oficial',
                'pie_pagina_texto' => 'Este presupuesto tiene validez legal según normativa vigente.',
                'color_primario' => '#b45309',
                'color_secundario' => '#d97706',
                'es_default' => false,
                'activo' => true,
                'modificable' => true,
                'orden_campos' => '["empresa","cliente","productos","totales","pagos","condiciones","garantias","notas","validez","firmas"]',
            ],
        ];
    }

    public function run(): void
    {
        $instances = DB::table('business_instances')
            ->where('aprobado', 1)
            ->pluck('id')
            ->toArray();

        if (empty($instances)) {
            $this->command->warn('No hay business instances aprobadas.');

            return;
        }

        $baseTemplates = $this->definicionPresupuestos();
        $createdTotal = 0;
        $updatedTotal = 0;

        foreach ($instances as $tenantId) {
            $created = 0;
            $updated = 0;

            foreach ($baseTemplates as $templateData) {
                $existing = DB::table('plantilla_impresiones')
                    ->where('codigo', $templateData['codigo'])
                    ->where('tenant_id', $tenantId)
                    ->first();

                if ($existing) {
                    $data = $templateData;
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

            $createdTotal += $created;
            $updatedTotal += $updated;
        }

        $this->command->info("Presupuestos: {$createdTotal} creadas, {$updatedTotal} actualizadas para ".count($instances).' instancias.');
    }
}
