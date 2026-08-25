<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessTypeMixtoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tipo de negocio "Lavadero + Tienda" con modo mixto
        $tipo = DB::table('business_types')->where('slug', 'lavadero-tienda')->first();
        if ($tipo) {
            $tipoId = $tipo->id;
            // Actualizar config si cambió
            DB::table('business_types')->where('id', $tipoId)->update([
                'config'      => json_encode(['facturacion_modo' => 'productos_y_servicios']),
                'updated_at'  => now(),
            ]);
        } else {
            $tipoId = DB::table('business_types')->insertGetId([
                'nombre'        => 'Lavadero + Tienda',
                'slug'        => 'lavadero-tienda',
                'descripcion' => 'Negocio mixto: venta de productos y servicios de lavado de vehículos',
                'color'       => '#3b82f6',
                'icon'        => 'bi-droplet-fill',
                'activo'      => true,
                'orden'       => 10,
                'config'      => json_encode(['facturacion_modo' => 'productos_y_servicios']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Crear instancia de negocio para testing
        $instance = DB::table('business_instances')->where('slug', 'lavadero-tienda-demo')->first();
        if ($instance) {
            $instanceId = $instance->id;
            // Actualizar business_type_id si cambió
            DB::table('business_instances')->where('id', $instanceId)->update([
                'business_type_id' => $tipoId,
                'updated_at' => now(),
            ]);
        } else {
            $instanceId = DB::table('business_instances')->insertGetId([
                'nombre'           => 'Lavadero & Tienda Demo',
                'slug'            => 'lavadero-tienda-demo',
                'rnc'             => 'J-123456789',
                'email'           => 'demo@lavaderotienda.do',
                'telefono'        => '+1-809-555-0100',
                'direccion'       => 'Av. Principal #123, Santiago, RD',
                'business_type_id' => $tipoId,
                'activo'          => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Crear categoría para servicios de lavado
        $categoriaLavadoId = DB::table('categorias')->insertGetId([
            'nombre'       => 'Servicios de Lavado',
            'descripcion'  => 'Servicios de lavado y detallado de vehículos',
            'activa'       => true,
            'tenant_id'    => $instanceId,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Crear servicios de lavado de ejemplo
        $servicios = [
            [
                'nombre'            => 'Lavado Básico Exterior',
                'descripcion'       => 'Lavado exterior con champú neutro, enjuague y secado a mano',
                'precio'            => 350.00,
                'precio_compra'     => 100.00,
                'duracion_minutos'  => 30,
                'categoria'         => 'Lavado Exterior',
                'activo'            => true,
                'orden'             => 1,
                'tenant_id'         => $instanceId,
                'itbis_porcentaje'  => 18.00,
            ],
            [
                'nombre'            => 'Lavado Completo (Exterior + Interior)',
                'descripcion'       => 'Lavado exterior + aspirado interior, limpieza de tablero y vidrios',
                'precio'            => 650.00,
                'precio_compra'     => 200.00,
                'duracion_minutos'  => 60,
                'categoria'         => 'Lavado Completo',
                'activo'            => true,
                'orden'             => 2,
                'tenant_id'         => $instanceId,
                'itbis_porcentaje'  => 18.00,
            ],
            [
                'nombre'            => 'Lavado Premium (Full Detail)',
                'descripcion'       => 'Lavado completo + encerado, limpieza de motor, tratamiento de llantas y neumáticos',
                'precio'            => 1200.00,
                'precio_compra'     => 400.00,
                'duracion_minutos'  => 120,
                'categoria'         => 'Premium',
                'activo'            => true,
                'orden'             => 3,
                'tenant_id'         => $instanceId,
                'itbis_porcentaje'  => 18.00,
            ],
            [
                'nombre'            => 'Encerado Protector',
                'descripcion'       => 'Aplicación de cera protectora de alta duración',
                'precio'            => 800.00,
                'precio_compra'     => 250.00,
                'duracion_minutos'  => 45,
                'categoria'         => 'Protección',
                'activo'            => true,
                'orden'             => 4,
                'tenant_id'         => $instanceId,
                'itbis_porcentaje'  => 18.00,
            ],
            [
                'nombre'            => 'Limpieza de Tapicería',
                'descripcion'       => 'Limpieza profunda de asientos y tapetes con extractor',
                'precio'            => 500.00,
                'precio_compra'     => 150.00,
                'duracion_minutos'  => 60,
                'categoria'         => 'Interior',
                'activo'            => true,
                'orden'             => 5,
                'tenant_id'         => $instanceId,
                'itbis_porcentaje'  => 18.00,
            ],
            [
                'nombre'            => 'Pulido de Faros',
                'descripcion'       => 'Restauración de faros opacos con pulido profesional',
                'precio'            => 400.00,
                'precio_compra'     => 100.00,
                'duracion_minutos'  => 30,
                'categoria'         => 'Restauración',
                'activo'            => true,
                'orden'             => 6,
                'tenant_id'         => $instanceId,
                'itbis_porcentaje'  => 18.00,
            ],
        ];

        foreach ($servicios as $index => $servicio) {
            $servicio['created_at'] = now();
            $servicio['updated_at'] = now();
            DB::table('lavadero_servicios')->insert($servicio);
        }

        // Crear algunos productos de ejemplo (para vender junto con servicios)
        $productos = [
            [
                'nombre'           => 'Ambientador Auto - Pino',
                'descripcion'      => 'Ambientador para auto aroma pino, duración 30 días',
                'precio'           => 150.00,
                'precio_compra'    => 60.00,
                'unidad_medida'    => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock'            => 50,
                'categoria_id'     => $categoriaLavadoId,
                'activo'           => true,
                'tenant_id'        => $instanceId,
            ],
            [
                'nombre'           => 'Limpiavidrios Automotriz 500ml',
                'descripcion'      => 'Limpiavidrios sin rayas para automóviles',
                'precio'           => 220.00,
                'precio_compra'    => 90.00,
                'unidad_medida'    => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock'            => 30,
                'categoria_id'     => $categoriaLavadoId,
                'activo'           => true,
                'tenant_id'        => $instanceId,
            ],
            [
                'nombre'           => 'Cera Spray Rápida 300ml',
                'descripcion'      => 'Cera en spray para retoque rápido entre lavados',
                'precio'           => 380.00,
                'precio_compra'    => 160.00,
                'unidad_medida'    => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock'            => 25,
                'categoria_id'     => $categoriaLavadoId,
                'activo'           => true,
                'tenant_id'        => $instanceId,
            ],
        ];

        foreach ($productos as $index => $producto) {
            $producto['created_at'] = now();
            $producto['updated_at'] = now();
            DB::table('productos')->insert($producto);
        }

        // Crear caja para la instancia
        // Verificar columnas de cajas
        $cajaColumns = DB::getSchemaBuilder()->getColumnListing('cajas');
        $cajaData = [
            'nombre'     => 'Caja Principal',
            'codigo'     => 'CAJA-01',
            'activo'     => true,
            'tenant_id'  => $instanceId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (in_array('descripcion', $cajaColumns)) {
            $cajaData['descripcion'] = 'Caja principal para ventas y servicios';
        }
        DB::table('cajas')->insert($cajaData);

        // Crear terminal POS
        DB::table('terminales')->insert([
            'nombre'     => 'Terminal Principal',
            'codigo'     => 'POS-01',
            'ubicacion'  => 'Recepción Principal',
            'caja_id'    => 1,
            'activo'     => true,
            'tenant_id'  => $instanceId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Tipo de negocio mixto creado:');
        $this->command->info('  - Tipo: Lavadero + Tienda (facturacion_modo: productos_y_servicios)');
        $this->command->info('  - Instancia: Lavadero & Tienda Demo (ID: ' . $instanceId . ')');
        $this->command->info('  - Servicios de lavado: ' . count($servicios) . ' creados');
        $this->command->info('  - Productos: ' . count($productos) . ' creados');
    }
}