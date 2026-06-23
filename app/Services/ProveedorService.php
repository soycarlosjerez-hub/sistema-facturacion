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

        $proveedores = Proveedor::when($buscar, fn($q) => $q->where(function ($sub) use ($buscar) {
            $sub->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('email', 'like', "%{$buscar}%")
                ->orWhere('telefono', 'like', "%{$buscar}%")
                ->orWhere('rnc', 'like', "%{$buscar}%");
        }))->latest()->paginate(10);

        $totalProveedores = Proveedor::count();

        return compact('proveedores', 'totalProveedores');
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
        $proveedor->update($data);
        return $proveedor;
    }

    public function delete(Proveedor $proveedor): void
    {
        $proveedor->delete();
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
