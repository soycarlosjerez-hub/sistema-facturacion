<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Impresora extends Model
{
    protected $table = 'impresoras';

    protected $fillable = [
        'nombre',
        'tipo_conexion',
        'direccion_ip',
        'puerto',
        'ruta_compartida',
        'driver',
        'papel_tamano',
        'caracteres_por_linea',
        'auto_imprimir_ventas',
        'auto_imprimir_cotizaciones',
        'auto_imprimir_conduces',
        'activo',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'puerto' => 'integer',
        'caracteres_por_linea' => 'integer',
        'auto_imprimir_ventas' => 'boolean',
        'auto_imprimir_cotizaciones' => 'boolean',
        'auto_imprimir_conduces' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public const TIPOS_CONEXION = [
        'local' => 'Local (USB/LPT)',
        'red' => 'Red (IP:Puerto)',
        'compartida' => 'Compartida (Windows)',
        'pdf' => 'PDF (Archivo)',
    ];

    public const TAMANOS_PAPEL = [
        '58mm' => '58 mm (32 caracteres)',
        '80mm' => '80 mm (42 caracteres)',
        'letter' => 'Carta (8.5"x11")',
    ];

    public const DRIVERS = [
        'escpos' => 'ESC/POS (Térmica)',
        'windows' => 'Windows Driver',
        'network' => 'Raw TCP/IP',
        'pdf' => 'PDF',
    ];

    public function historial(): HasMany
    {
        return $this->hasMany(HistorialImpresion::class);
    }

    public function getCharsPerLine(): int
    {
        return $this->caracteres_por_linea;
    }

    public function getConexionResumenAttribute(): string
    {
        return match ($this->tipo_conexion) {
            'red' => "{$this->direccion_ip}:{$this->puerto}",
            'local' => $this->ruta_compartida ?? 'USB/LPT',
            'compartida' => $this->ruta_compartida ?? 'N/A',
            'pdf' => 'Genera PDF',
            default => 'N/A',
        };
    }

    public function getTamanoLabelAttribute(): string
    {
        return self::TAMANOS_PAPEL[$this->papel_tamano] ?? $this->papel_tamano;
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }
}
