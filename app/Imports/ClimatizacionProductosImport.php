<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Auth;

class ClimatizacionProductosImport implements ToModel, WithHeadingRow, SkipsOnFailure, WithChunkReading, WithCustomCsvSettings
{
    private array $mapping;
    private array $defaults;
    private string $delimiter;
    public array $failures = [];
    public int $imported = 0;

    const MARCAS_VALIDAS = [
        'carrier', 'daikin', 'lg', 'samsung', 'mitsubishi', 'gree',
        'toshiba', 'york', 'trane', 'lennox', 'hitachi', 'panasonic',
        'sharp', 'haier', 'whirlpool', 'electrolux', 'funai', 'sanyo',
        'consul', 'brastemp', 'century', 'springer', 'komeco', 'other',
    ];

    const TIPOS_EQUIPO_VALIDOS = [
        'split', 'multi-split', 'cassette', 'paquete', 'central',
        'vrf', 'vr', 'ventana', 'portable', 'tower',
    ];

    const CAPACIDADES_TONELADAS = [0.5, 0.75, 1, 1.25, 1.5, 2, 2.5, 3, 3.5, 4, 5, 6, 8, 10];

    const GAS_REFRIGERANTE_VALIDOS = [
        'R-22', 'R-410A', 'R-32', 'R-454B', 'R-290', 'R-134A',
    ];

    const CATEGORIA_CLIMA_VALIDOS = [
        'residencial', 'comercial', 'industrial',
    ];

    public function __construct(array $mapping, array $defaults = [], string $delimiter = ',')
    {
        $this->mapping = $mapping;
        $this->defaults = $defaults;
        $this->delimiter = $delimiter;
    }

    public function model(array $row): ?Producto
    {
        $data = [];

        foreach ($this->mapping as $field => $header) {
            $value = trim($row[$header] ?? '');
            $data[$field] = $value === '' ? ($this->defaults[$field] ?? null) : $value;
        }

        if (empty($data['nombre']) || empty($data['marca']) || empty($data['modelo']) || empty($data['tipo_equipo'])) {
            return null;
        }

        if (!empty($data['precio'])) {
            $data['precio'] = (float) str_replace([',', ' ', '$'], ['', '', ''], $data['precio']);
        } else {
            $data['precio'] = $this->defaults['precio'] ?? 0;
        }

        if (!empty($data['precio_compra'])) {
            $data['precio_compra'] = (float) str_replace([',', ' ', '$'], ['', '', ''], $data['precio_compra']);
        } else {
            $data['precio_compra'] = $this->defaults['precio_compra'] ?? 0;
        }

        $data['stock'] = !empty($data['stock']) ? (int) filter_var($data['stock'], FILTER_SANITIZE_NUMBER_INT) : ($this->defaults['stock'] ?? 0);
        $data['stock_minimo'] = !empty($data['stock_minimo']) ? (int) filter_var($data['stock_minimo'], FILTER_SANITIZE_NUMBER_INT) : ($this->defaults['stock_minimo'] ?? 0);

        $data['itbis_porcentaje'] = !empty($data['itbis_porcentaje']) ? (float) str_replace(',', '.', (string) $data['itbis_porcentaje']) : ($this->defaults['itbis_porcentaje'] ?? 18);

        if (!empty($data['capacidad_toneladas'])) {
            $ton = (float) str_replace(',', '.', (string) $data['capacidad_toneladas']);
            $data['capacidad_toneladas'] = $ton;
            $data['capacidad_btu'] = (int) round($ton * 12000);
        }

        if (!empty($data['peso_kg'])) {
            $data['peso_kg'] = (float) str_replace(',', '.', (string) $data['peso_kg']);
        }

        if (!empty($data['eficiencia_seer'])) {
            $data['eficiencia_seer'] = (float) str_replace(',', '.', (string) $data['eficiencia_seer']);
        }

        $data['marca'] = trim($data['marca'] ?? '');
        $data['modelo'] = trim($data['modelo'] ?? '');
        $data['tipo_equipo'] = strtolower(trim($data['tipo_equipo'] ?? ''));
        $data['categoria_clima'] = strtolower(trim($data['categoria_clima'] ?? ''));
        $data['gas_refrigerante'] = strtoupper(trim($data['gas_refrigerante'] ?? ''));
        $data['voltaje'] = trim($data['voltaje'] ?? '');
        $data['unidad_medida'] = trim($data['unidad_medida'] ?? ($this->defaults['unidad_medida'] ?? 'Unidad'));

        if (isset($data['categoria']) || isset($data['categoria_id'])) {
            $catValue = $data['categoria'] ?? $data['categoria_id'] ?? null;
            $data['categoria_id'] = $this->resolveCategoryId($catValue);
        }
        unset($data['categoria']);

        $data['activo'] = true;
        $data['tenant_id'] = Auth::user()->business_instance_id;

        $existing = null;
        if (!empty($data['codigo_barras'])) {
            $existing = Producto::where('codigo_barras', $data['codigo_barras'])
                ->where('tenant_id', $data['tenant_id'])
                ->first();
        }
        if (!$existing && !empty($data['nombre']) && !empty($data['marca'])) {
            $existing = Producto::where('nombre', $data['nombre'])
                ->where('marca', $data['marca'])
                ->where('tenant_id', $data['tenant_id'])
                ->first();
        }

        if ($existing) {
            $existing->update($data);
            $this->imported++;
            return null;
        }

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
        return ['delimiter' => $this->delimiter];
    }
}
