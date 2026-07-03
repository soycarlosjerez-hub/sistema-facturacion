<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Intervention\Image\Laravel\Facades\Image;

class ProductoService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Producto::with('categoria');

        if ($termino = $filters['nombre'] ?? null) {
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('codigo_barras', 'like', "%{$termino}%");
            });
        }

        if ($min = $filters['precio_min'] ?? null) {
            $query->where('precio', '>=', (float) $min);
        }

        if ($max = $filters['precio_max'] ?? null) {
            $query->where('precio', '<=', (float) $max);
        }

        if ($stockStatus = $filters['stock_status'] ?? null) {
            match ($stockStatus) {
                'critical' => $query->where('stock', '<=', 5),
                'low'      => $query->whereBetween('stock', [6, 15]),
                'ok'       => $query->where('stock', '>', 15),
                default    => null,
            };
        }

        $activo = $filters['activo'] ?? null;
        if ($activo !== null && $activo !== '') {
            $query->where('activo', $activo);
        }

        // Apply tenant filter for multi‑tenant isolation
        if (auth()->check() && auth()->user()->business_instance_id !== null) {
            $query->where('tenant_id', auth()->user()->business_instance_id);
        }
        // Paginate and return results
        return $query->latest()->paginate(10)->appends($filters);
    }

    public function listAll(array $filters = []): Collection
    {
        $query = Producto::with('categoria');

        if ($termino = $filters['nombre'] ?? null) {
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('codigo_barras', 'like', "%{$termino}%");
            });
        }

        if ($min = $filters['precio_min'] ?? null) {
            $query->where('precio', '>=', (float) $min);
        }

        if ($max = $filters['precio_max'] ?? null) {
            $query->where('precio', '<=', (float) $max);
        }

        if ($stockStatus = $filters['stock_status'] ?? null) {
            match ($stockStatus) {
                'critical' => $query->where('stock', '<=', 5),
                'low'      => $query->whereBetween('stock', [6, 15]),
                'ok'       => $query->where('stock', '>', 15),
                default    => null,
            };
        }

        $activo = $filters['activo'] ?? null;
        if ($activo !== null && $activo !== '') {
            $query->where('activo', $activo);
        }

        if (auth()->check() && auth()->user()->business_instance_id !== null) {
            $query->where('tenant_id', auth()->user()->business_instance_id);
        }

        return $query->latest()->get();
    }

    public function create(array $data, ?UploadedFile $imagen = null): Producto
    {
        // Ensure product is scoped to the current tenant (business_instance_id)
        $data['tenant_id'] = auth()->user()->business_instance_id;
    if ($imagen) {
        $data['imagen'] = $this->saveImage($imagen);
    }

        $data['itbis_porcentaje'] = $data['itbis_porcentaje'] ?? 18.00;

        return Producto::create($data);
    }

    public function update(Producto $producto, array $data, ?UploadedFile $imagen = null): Producto
    {
        if ($imagen) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $this->saveImage($imagen);
        }

        $data['itbis_porcentaje'] = $data['itbis_porcentaje'] ?? 18.00;
        $producto->update($data);

        return $producto;
    }

    public function delete(Producto $producto): array
    {
        if ($producto->ventaDetalles()->exists()) {
            return [
                'success' => false,
                'message' => "No se puede eliminar '{$producto->nombre}' porque tiene ventas asociadas.",
            ];
        }

        $compraIds = DetalleCompra::where('producto_id', $producto->id)
            ->pluck('compra_id')->unique();

        DetalleCompra::where('producto_id', $producto->id)->delete();

        foreach ($compraIds as $compraId) {
            if (DetalleCompra::where('compra_id', $compraId)->count() === 0) {
                Compra::where('id', $compraId)->delete();
            }
        }

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return ['success' => true, 'message' => 'Producto eliminado correctamente.'];
    }

    public function toggleActivo(Producto $producto): Producto
    {
        $producto->update(['activo' => !$producto->activo]);
        return $producto->fresh();
    }

    public function deleteImage(Producto $producto): void
    {
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
            $producto->update(['imagen' => null]);
        }
    }

    public function search(string $term, int $limit = 20)
    {
        return Producto::where(function ($q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
              ->orWhere('codigo_barras', 'like', "%{$term}%");
        })->orderBy('nombre')->limit($limit)->get();
    }

    public function saveImage(UploadedFile $file): string
    {
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = $name . '-' . uniqid() . '.webp';

        $image = Image::read($file);
        $image->resize(width: 800);
        $image->toWebp(quality: 70)
              ->save(storage_path('app/public/productos/' . $filename));

        return 'productos/' . $filename;
    }
}
