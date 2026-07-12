<?php

namespace App\Imports;

use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProveedoresImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $nombre = trim($row['nombre'] ?? '');
        if (empty($nombre)) return null;

        $tenantId = Auth::user()->business_instance_id;

        $existing = Proveedor::where('nombre', $nombre)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->first();

        if ($existing) {
            $existing->update([
                'email'                 => $row['email'] ?? $existing->email,
                'telefono'              => $row['telefono'] ?? $existing->telefono,
                'direccion'             => $row['direccion'] ?? $existing->direccion,
                'rnc'                   => $row['rnc'] ?? $existing->rnc,
                'tipo_persona'          => $row['tipo_persona'] ?? $existing->tipo_persona,
                'sujeto_retencion_isr'   => $this->parseBool($row['sujeto_retencion_isr'] ?? $existing->sujeto_retencion_isr),
                'sujeto_retencion_itbis' => $this->parseBool($row['sujeto_retencion_itbis'] ?? $existing->sujeto_retencion_itbis),
                'activo'                => $this->parseBool($row['activo'] ?? $existing->activo),
            ]);
            return null;
        }

        return new Proveedor([
            'nombre'                => $nombre,
            'email'                 => $row['email'] ?? null,
            'telefono'              => $row['telefono'] ?? null,
            'direccion'             => $row['direccion'] ?? null,
            'rnc'                   => $row['rnc'] ?? null,
            'tipo_persona'          => $row['tipo_persona'] ?? null,
            'sujeto_retencion_isr'   => $this->parseBool($row['sujeto_retencion_isr'] ?? false),
            'sujeto_retencion_itbis' => $this->parseBool($row['sujeto_retencion_itbis'] ?? false),
            'activo'                => $this->parseBool($row['activo'] ?? true),
            'tenant_id'             => $tenantId,
        ]);
    }

    private function parseBool($value): bool
    {
        if (is_bool($value)) return $value;
        if (is_numeric($value)) return (bool) $value;
        return in_array(strtolower((string) $value), ['sí', 'si', 'yes', 'activo', 'true', '1']);
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
        ];
    }
}
