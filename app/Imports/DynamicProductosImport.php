<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Auth;

class DynamicProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading, WithCustomCsvSettings
{
    private array $mapping;
    private array $defaults;
    private string $delimiter;
    public array $failures = [];
    public int $imported = 0;

    public function __construct(array $mapping, array $defaults = [], string $delimiter = ',')
    {
        $this->mapping = $mapping;
        $this->defaults = $defaults;
        $this->delimiter = $delimiter;
    }

    public function model(array $row)
    {
        $data = [];

        foreach ($this->mapping as $field => $header) {
            $value = $row[$header] ?? $this->defaults[$field] ?? null;
            if ($value === '' || $value === null) {
                $value = $this->defaults[$field] ?? null;
            }
            $data[$field] = $value;
        }

        if (empty($data['nombre'])) {
            return null;
        }

        if (isset($data['precio'])) $data['precio'] = (float) str_replace([',', ' '], ['.', ''], $data['precio']);
        if (isset($data['precio_compra'])) $data['precio_compra'] = (float) str_replace([',', ' '], ['.', ''], $data['precio_compra']);
        if (isset($data['stock'])) $data['stock'] = (int) $data['stock'];
        if (isset($data['itbis_porcentaje'])) $data['itbis_porcentaje'] = (float) $data['itbis_porcentaje'];
        if (isset($data['categoria']) || isset($data['categoria_id'])) {
            $catValue = $data['categoria'] ?? $data['categoria_id'] ?? null;
            $data['categoria_id'] = $this->resolveCategoryId($catValue);
        }
        unset($data['categoria']);

        $data['tenant_id'] = Auth::user()->business_instance_id;

        $this->imported++;
        return new Producto($data);
    }

    private function resolveCategoryId($value): ?int
    {
        if (!$value) return null;
        if (is_numeric($value)) {
            $cat = Categoria::find((int) $value);
            return $cat?->id;
        }
        $cat = Categoria::where('nombre', $value)->first();
        return $cat?->id;
    }

    public function rules(): array
    {
        return [];
    }

    public function onFailure(Failure ...$failures): void
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->delimiter,
        ];
    }
}
