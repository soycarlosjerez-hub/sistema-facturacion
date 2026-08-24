<?php

namespace App\Services;

use App\Models\LavaderoPaquete;
use App\Models\LavaderoPaqueteItem;
use App\Models\LavaderoServicio;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class LavaderoPaqueteService
{
    public function getAll(): \Illuminate\Support\Collection
    {
        return LavaderoPaquete::with(['businessType', 'items.servicio', 'items.producto'])
            ->orderBy('orden')
            ->orderBy('activo', 'desc')
            ->get();
    }

    public function findById(int $id): ?LavaderoPaquete
    {
        return LavaderoPaquete::with('items')->find($id);
    }

    public function getActivos(): \Illuminate\Support\Collection
    {
        return LavaderoPaquete::with(['items.servicio', 'items.producto'])
            ->activos()
            ->orderBy('orden')
            ->get();
    }

    public function create(array $data, array $itemsData = []): LavaderoPaquete
    {
        return DB::transaction(function () use ($data, $itemsData) {
            $paquete = LavaderoPaquete::create($data);

            if (!empty($itemsData)) {
                $this->syncItems($paquete, $itemsData);
            }

            return $paquete->load('items');
        });
    }

    public function update(int $id, array $data, array $itemsData = []): LavaderoPaquete
    {
        return DB::transaction(function () use ($id, $data, $itemsData) {
            $paquete = LavaderoPaquete::findOrFail($id);
            $paquete->update($data);

            if (array_key_exists('items', $data)) {
                // If 'items' key exists in data, it's an array, not itemsData
                $this->syncItems($paquete, $data['items']);
            } elseif (!empty($itemsData)) {
                $this->syncItems($paquete, $itemsData);
            }

            return $paquete->load('items');
        });
    }

    public function delete(int $id): bool
    {
        return LavaderoPaquete::destroy($id);
    }

    public function toggleActivo(int $id): LavaderoPaquete
    {
        $paquete = LavaderoPaquete::findOrFail($id);
        $paquete->update(['activo' => !$paquete->activo]);
        return $paquete;
    }

    public function createWithItems(array $paqueteData, array $itemsData): LavaderoPaquete
    {
        return DB::transaction(function () use ($paqueteData, $itemsData) {
            // Validate items reference valid services/products
            $this->validateItems($itemsData);

            $paquete = LavaderoPaquete::create($paqueteData);
            $this->syncItems($paquete, $itemsData);

            return $paquete->load('items');
        });
    }

    public function updateWithItems(int $id, array $paqueteData, array $itemsData): LavaderoPaquete
    {
        return DB::transaction(function () use ($id, $paqueteData, $itemsData) {
            $paquete = LavaderoPaquete::findOrFail($id);
            $paquete->update($paqueteData);

            $this->validateItems($itemsData);

            $paquete->items()->delete();
            $this->syncItems($paquete, $itemsData);

            return $paquete->load('items');
        });
    }

    public function getPaqueteCompleto(int $id): ?array
    {
        $paquete = LavaderoPaquete::with(['businessType', 'items' => function ($query) {
            $query->with(['servicio', 'producto'])->orderBy('orden')->get();
        }])->find($id);

        if (!$paquete) {
            return null;
        }

        return [
            'paquete' => $paquete->load('sucursal'),
            'items'   => $paquete->items->map(function ($item) {
                $details = [
                    'id'          => $item->id,
                    'tipo'        => $item->tipo,
                    'cantidad'    => $item->cantidad,
                    'orden'       => $item->orden,
                ];

                if ($item->tipo === 'servicio') {
                    $details['nombre']  = $item->servicio?->nombre;
                    $details['precio']  = $item->precio_individual ?? ($item->servicio?->precio ?? 0);
                    $details['eservicio'] = $item->servicio;
                } elseif ($item->tipo === 'producto') {
                    $details['nombre']  = $item->producto?->nombre;
                    $details['precio']  = $item->precio_individual ?? ($item->producto?->precio ?? 0);
                    $details['producto'] = $item->producto;
                }

                return $details;
            })->toArray(),
            'total'    => $this->calcularPrecio($id),
            'duracion' => (int) $paquete->duracion_minutos,
        ];
    }

    public function calcularPrecio(int $paqueteId): float
    {
        $paquete = LavaderoPaquete::with('items')->find($paqueteId);

        if (!$paquete) {
            return 0;
        }

        return $paquete->items->sum(function ($item) {
            $precio = $item->precio_individual ?? 0;
            return $precio * $item->cantidad;
        });
    }

    protected function syncItems(LavaderoPaquete $paquete, array $itemsData): void
    {
        $paquete->items()->delete();

        foreach ($itemsData as $index => $itemData) {
            LavaderoPaqueteItem::create(array_merge($itemData, [
                'paquete_id' => $paquete->id,
                'orden'      => $itemData['orden'] ?? ($index + 1),
            ]));
        }
    }

    protected function validateItems(array $itemsData): void
    {
        $servicioIds = [];
        $productoIds = [];

        foreach ($itemsData as $item) {
            if ($item['tipo'] === 'servicio') {
                if (!empty($item['servicio_id'])) {
                    $servicioIds[] = $item['servicio_id'];
                }
            } elseif ($item['tipo'] === 'producto') {
                if (!empty($item['producto_id'])) {
                    $productoIds[] = $item['producto_id'];
                }
            }
        }

        if (!empty($servicioIds)) {
            $validServicios = LavaderoServicio::whereIn('id', $servicioIds)->pluck('id')->toArray();
            $invalid = array_diff($servicioIds, $validServicios);
            if (!empty($invalid)) {
                throw new \Exception('Los siguientes servicios no existen: ' . implode(', ', $invalid));
            }
        }

        if (!empty($productoIds)) {
            $validProductos = Producto::whereIn('id', $productoIds)->pluck('id')->toArray();
            $invalid = array_diff($productoIds, $validProductos);
            if (!empty($invalid)) {
                throw new \Exception('Los siguientes productos no existen: ' . implode(', ', $invalid));
            }
        }
    }
}
