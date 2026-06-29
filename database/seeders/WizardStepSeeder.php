<?php

namespace Database\Seeders;

use App\Models\WizardStep;
use App\Models\Sucursal;
use App\Models\Caja;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\NcfSequence;
use App\Models\MesaUbicacion;
use App\Models\MesaCategoria;
use App\Models\Mesa;
use App\Models\LavaderoServicio;
use App\Models\Lavador;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class WizardStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'key'          => 'parametros',
                'module_key'   => 'configuracion-general',
                'label'        => 'Parámetros del Sistema',
                'icon'         => 'bi-gear',
                'required'     => true,
                'skipable'     => false,
                'entity_class' => SystemSetting::class,
                'orden'        => 5,
            ],
            [
                'key' => 'sucursal',
                'module_key' => 'sucursales',
                'label' => 'Sucursal',
                'icon' => 'bi-building',
                'required' => true,
                'skipable' => false,
                'entity_class' => Sucursal::class,
                'orden' => 10,
            ],
            [
                'key' => 'caja',
                'module_key' => 'cajas',
                'label' => 'Caja',
                'icon' => 'bi-cash-stack',
                'required' => true,
                'skipable' => false,
                'entity_class' => Caja::class,
                'orden' => 20,
            ],
            [
                'key' => 'almacen',
                'module_key' => 'almacenes',
                'label' => 'Almacén',
                'icon' => 'bi-buildings',
                'required' => true,
                'skipable' => false,
                'entity_class' => Almacen::class,
                'orden' => 30,
            ],
            [
                'key'          => 'categoria-producto',
                'module_key'   => 'inventario',
                'label'        => 'Categoría de Productos',
                'icon'         => 'bi-tags',
                'required'     => false,
                'skipable'     => true,
                'entity_class' => Categoria::class,
                'orden'        => 35,
            ],
            [
                'key'          => 'producto',
                'module_key'   => 'inventario',
                'label'        => 'Productos',
                'icon'         => 'bi-box-seam',
                'required'     => true,
                'skipable'     => true,
                'entity_class' => Producto::class,
                'orden'        => 40,
            ],
            [
                'key'          => 'proveedor',
                'module_key'   => 'proveedores',
                'label'        => 'Proveedores',
                'icon'         => 'bi-truck',
                'required'     => false,
                'skipable'     => true,
                'entity_class' => Proveedor::class,
                'orden'        => 45,
            ],
            [
                'key'          => 'cliente',
                'module_key'   => 'clientes',
                'label'        => 'Clientes',
                'icon'         => 'bi-people',
                'required'     => false,
                'skipable'     => true,
                'entity_class' => Cliente::class,
                'orden'        => 48,
            ],
            [
                'key' => 'ncf',
                'module_key' => 'ncf',
                'label' => 'Secuencias NCF',
                'icon' => 'bi-receipt-cutoff',
                'required' => false,
                'skipable' => true,
                'entity_class' => NcfSequence::class,
                'orden' => 50,
            ],
            [
                'key' => 'ubicacion-mesa',
                'module_key' => 'restaurante',
                'label' => 'Ubicación de Mesas',
                'icon' => 'bi-geo-alt',
                'required' => true,
                'skipable' => false,
                'entity_class' => MesaUbicacion::class,
                'orden' => 60,
            ],
            [
                'key' => 'categoria-mesa',
                'module_key' => 'restaurante',
                'label' => 'Categoría de Mesa',
                'icon' => 'bi-tags',
                'required' => true,
                'skipable' => false,
                'entity_class' => MesaCategoria::class,
                'orden' => 70,
            ],
            [
                'key' => 'mesa',
                'module_key' => 'restaurante',
                'label' => 'Mesas',
                'icon' => 'bi-grid-3x3-gap',
                'required' => false,
                'skipable' => true,
                'entity_class' => Mesa::class,
                'orden' => 80,
            ],
            [
                'key' => 'servicio-lavado',
                'module_key' => 'lavadero',
                'label' => 'Servicio de Lavado',
                'icon' => 'bi-card-checklist',
                'required' => true,
                'skipable' => false,
                'entity_class' => LavaderoServicio::class,
                'orden' => 90,
            ],
            [
                'key' => 'lavador',
                'module_key' => 'lavadero',
                'label' => 'Lavadores',
                'icon' => 'bi-people',
                'required' => false,
                'skipable' => true,
                'entity_class' => Lavador::class,
                'orden' => 100,
            ],
        ];

        foreach ($steps as $step) {
            WizardStep::updateOrCreate(['key' => $step['key']], $step);
        }
    }
}
