<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoriaImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection($rows)
    {
        $tenantId = Auth::user()->business_instance_id;

        foreach ($rows as $row) {
            $nombre = trim($row['nombre'] ?? '');
            if (empty($nombre)) {
                continue;
            }

            $validator = Validator::make([$row], [
                'nombre' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                continue;
            }

            $existing = DB::table('categorias')
                ->where('tenant_id', $tenantId)
                ->where('nombre', $nombre)
                ->first();

            if ($existing) {
                DB::table('categorias')
                    ->where('id', $existing->id)
                    ->update([
                        'descripcion' => $row['descripcion'] ?? $existing->descripcion,
                        'activa' => $this->parseActivo($row['activa'] ?? $existing->activa),
                    ]);

                continue;
            }

            DB::table('categorias')->insert([
                'nombre' => $nombre,
                'descripcion' => $row['descripcion'] ?? null,
                'activa' => $this->parseActivo($row['activa'] ?? true),
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function parseActivo($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (bool) $value;
        }

        return strtolower((string) $value) === 'sí' || strtolower((string) $value) === 'si' || strtolower((string) $value) === 'yes' || strtolower((string) $value) === 'activo';
    }

    public function rules(): array
    {
        return [
            '*.nombre' => 'required|string|max:100',
        ];
    }
}
