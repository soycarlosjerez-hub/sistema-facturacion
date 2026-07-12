<?php

namespace App\Imports;

use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoriaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $nombre = trim($row['nombre'] ?? '');
        if (empty($nombre)) return null;

        $tenantId = Auth::user()->business_instance_id;

        $existing = Categoria::where('nombre', $nombre)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->first();

        if ($existing) {
            $existing->update([
                'descripcion' => $row['descripcion'] ?? $existing->descripcion,
                'activa'      => $this->parseActivo($row['activa'] ?? $existing->activa),
            ]);
            return null;
        }

        return new Categoria([
            'nombre'      => $nombre,
            'descripcion' => $row['descripcion'] ?? null,
            'activa'      => $this->parseActivo($row['activa'] ?? true),
            'tenant_id'   => $tenantId,
        ]);
    }

    private function parseActivo($value): bool
    {
        if (is_bool($value)) return $value;
        if (is_numeric($value)) return (bool) $value;
        return strtolower((string) $value) === 'sí' || strtolower((string) $value) === 'si' || strtolower((string) $value) === 'yes' || strtolower((string) $value) === 'activo';
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
        ];
    }
}
