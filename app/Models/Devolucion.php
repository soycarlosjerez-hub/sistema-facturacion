<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'codigo', 'venta_id', 'cliente_id', 'user_id', 'fecha', 'motivo',
        'tipo', 'subtotal', 'itbis', 'total', 'estado', 'nota_credito_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'itbis' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleDevolucion::class);
    }

    public function notaCredito(): BelongsTo
    {
        return $this->belongsTo(EcfDocumento::class, 'nota_credito_id');
    }

    public static function generarCodigo(): string
    {
        $year = date('Y');
        $ultimo = self::where('codigo', 'like', "DEV-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        if ($ultimo) {
            $num = (int) substr($ultimo->codigo, -6) + 1;
        } else {
            $num = 1;
        }
        return sprintf('DEV-%s-%06d', $year, $num);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'borrador');
    }

    public function getTieneEcfAttribute(): bool
    {
        return $this->venta && $this->venta->tipo_comprobante === 'ecf';
    }
}
