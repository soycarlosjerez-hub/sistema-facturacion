<?php

namespace App\Services;

use App\Models\Proveedor;
use App\Support\RncValidator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProveedorService
{
    public function list(array $filters = []): array
    {
        $buscar = $filters['buscar'] ?? null;
        $incluirInactivos = ($filters['incluir_inactivos'] ?? '') === '1';

        $query = Proveedor::query();
        if (!$incluirInactivos) {
            $query->activo();
        }
        $query->when($buscar, fn($q) => $q->where(function ($sub) use ($buscar) {
            $sub->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('email', 'like', "%{$buscar}%")
                ->orWhere('telefono', 'like', "%{$buscar}%")
                ->orWhere('rnc', 'like', "%{$buscar}%");
        }))->latest();

        $proveedores = $query->paginate(10)->appends($filters);
        $totalProveedores = Proveedor::count();
        $totalActivos = Proveedor::activo()->count();
        $totalInactivos = Proveedor::inactivo()->count();

        return compact('proveedores', 'totalProveedores', 'totalActivos', 'totalInactivos');
    }

    public function toggleActivo(Proveedor $proveedor): Proveedor
    {
        $proveedor->update(['activo' => !$proveedor->activo]);
        return $proveedor->fresh();
    }

    public function create(array $data): Proveedor
    {
        $this->validarRnc($data);
        $data['sujeto_retencion_isr'] = $data['sujeto_retencion_isr'] ?? false;
        $data['sujeto_retencion_itbis'] = $data['sujeto_retencion_itbis'] ?? false;
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;
        return Proveedor::create($data);
    }

    public function update(Proveedor $proveedor, array $data): Proveedor
    {
        $this->validarRnc($data);
        $data['sujeto_retencion_isr'] = $data['sujeto_retencion_isr'] ?? false;
        $data['sujeto_retencion_itbis'] = $data['sujeto_retencion_itbis'] ?? false;
        $data['activo'] = $data['activo'] ?? false;
        $proveedor->update($data);
        return $proveedor;
    }

    public function delete(Proveedor $proveedor): array
    {
        $comprasCount = $proveedor->compras()->count();

        if ($comprasCount > 0) {
            $proveedor->update(['activo' => false]);
            return [
                'success' => true,
                'deactivated' => true,
                'message' => "No se puede eliminar: tiene {$comprasCount} compra(s) asociada(s). Se ha desactivado."
            ];
        }

        $proveedor->delete();
        return ['success' => true, 'deactivated' => false, 'message' => 'Proveedor eliminado correctamente'];
    }

    public function pdf(array $filters = [])
    {
        $query = Proveedor::query();
        if ($busqueda = $filters['busqueda'] ?? null) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('email', 'like', "%{$busqueda}%")
                    ->orWhere('telefono', 'like', "%{$busqueda}%")
                    ->orWhere('direccion', 'like', "%{$busqueda}%");
            });
        }
        $proveedores = $query->latest()->get();
        $pdf = Pdf::loadView('proveedores.pdf', compact('proveedores'));
        return $pdf->stream('proveedores.pdf');
    }

    protected function validarRnc(array &$data): void
    {
        if (!empty($data['rnc']) && !RncValidator::validar($data['rnc'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'rnc' => 'El RNC ingresado no es válido.',
            ]);
        }
    }
}
