<?php

namespace App\Imports;

use App\Models\Producto;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;

class ClimatizacionMarcasImport implements SkipsOnFailure, ToModel, WithHeadingRow
{
    public array $failures = [];

    public function model(array $row): ?array
    {
        $nombre = trim($row['nombre'] ?? '');
        if (empty($nombre)) {
            return null;
        }

        $nombre = Str::title(strtolower($nombre));

        $exists = Producto::where('marca', $nombre)->first();
        if ($exists) {
            return null;
        }

        return null;
    }

    public function onFailure(Failure ...$failures): void
    {
        $this->failures = array_merge($this->failures, $failures);
    }
}
