<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Producto([
            'nombre'           => $row['nombre'] ?? null,
            'codigo_barras'    => $row['codigo_barras'] ?? null,
            'descripcion'      => $row['descripcion'] ?? null,
            'precio'           => $row['precio'] ?? 0,
            'precio_compra'    => $row['precio_compra'] ?? 0,
            'unidad_medida'    => $row['unidad_medida'] ?? 'Unidad',
            'itbis_porcentaje' => $row['itbis_porcentaje'] ?? 18,
            'stock'            => $row['stock'] ?? 0,
            'imagen'           => $row['imagen'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock'  => 'required|integer|min:0',
        ];
    }
}
