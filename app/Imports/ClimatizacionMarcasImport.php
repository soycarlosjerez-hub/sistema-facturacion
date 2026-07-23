<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClimatizacionMarcasImport implements ToModel, WithHeadingRow, SkipsOnFailure
{
    public array $failures = [];

    public function model(array $row): ?array
    {
        $nombre = trim($row['nombre'] ?? '');
        if (empty($nombre)) return null;

        $nombre = Str::title(strtolower($nombre));

        $exists = Producto::where('marca', $nombre)->first();
        if ($exists) return null;

        return null;
    }

    public function onFailure(Failure ...$failures): void
    {
        $this->failures = array_merge($this->failures, $failures);
    }
}
