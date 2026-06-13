<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Cotizacion extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'numero',
        'cliente_id',
        'user_id',
        'sucursal_id',
        'fecha',
        'fecha_validez',
        'estado',
        'subtotal',
        'descuento',
        'itbis',
        'total',
        'notas',
        'condiciones',
        'venta_id',
        'convertida_en',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_validez' => 'date',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbis' => 'decimal:2',
        'total' => 'decimal:2',
        'convertida_en' => 'datetime',
    ];

    // Estados disponibles
    public const ESTADOS = [
        'borrador' => ['label' => 'Borrador', 'color' => 'secondary', 'icon' => 'pencil-square'],
        'enviada' => ['label' => 'Enviada', 'color' => 'info', 'icon' => 'send'],
        'aprobada' => ['label' => 'Aprobada', 'color' => 'success', 'icon' => 'check-circle'],
        'rechazada' => ['label' => 'Rechazada', 'color' => 'danger', 'icon' => 'x-circle'],
        'vencida' => ['label' => 'Vencida', 'color' => 'warning', 'icon' => 'clock-history'],
        'convertida' => ['label' => 'Convertida', 'color' => 'primary', 'icon' => 'arrow-right-circle'],
        'anulada' => ['label' => 'Anulada', 'color' => 'dark', 'icon' => 'slash-circle'],
    ];

    // Generar número automático
    public static function generarNumero(): string
    {
        $year = date('Y');
        $ultimo = self::where('numero', 'like', "COT-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($ultimo) {
            $numero = (int) substr($ultimo->numero, -6);
            $numero++;
        } else {
            $numero = 1;
        }
        
        return sprintf('COT-%s-%06d', $year, $numero);
    }

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CotizacionItem::class)->orderBy('orden');
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    // Helpers
    public function getEstadoLabelAttribute(): string
    {
        return $this->estado && isset(self::ESTADOS[$this->estado])
            ? self::ESTADOS[$this->estado]['label']
            : 'Desconocido';
    }

    public function getEstadoColorAttribute(): string
    {
        return self::ESTADOS[$this->estado]['color'] ?? 'secondary';
    }

    public function getEstadoIconAttribute(): string
    {
        return self::ESTADOS[$this->estado]['icon'] ?? 'circle';
    }

    public function getDiasValidezAttribute(): int
    {
        return $this->fecha_validez ? (int) $this->created_at->diffInDays($this->fecha_validez) : 0;
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_validez && $this->fecha_validez->isPast() && !in_array($this->estado, ['convertida', 'anulada', 'aprobada']);
    }

    public function getPuedeConvertirseAttribute(): bool
    {
        return in_array($this->estado, ['aprobada', 'enviada', 'borrador']) && !$this->esta_vencida;
    }

    public function getCantidadItemsAttribute(): int
    {
        return $this->items()->sum('cantidad');
    }

    // Scopes
    public function scopeVencidas($query)
    {
        return $query->where('fecha_validez', '<', now())
            ->whereNotIn('estado', ['convertida', 'anulada', 'aprobada']);
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['borrador', 'enviada', 'aprobada']);
    }

    // Calcular totales automáticamente
    public function calcularTotales(): void
    {
        $items = $this->items;
        $subtotal = 0;
        $itbis = 0;

        foreach ($items as $item) {
            $itemSubtotal = ($item->cantidad * $item->precio_unitario) - $item->descuento;
            $itemItbis = $itemSubtotal * ($item->itbis_porcentaje / 100);
            
            $item->subtotal = round($itemSubtotal, 2);
            $item->itbis = round($itemItbis, 2);
            $item->total = round($itemSubtotal + $itemItbis, 2);
            $item->save();
            
            $subtotal += $itemSubtotal;
            $itbis += $itemItbis;
        }

        $this->subtotal = round($subtotal, 2);
        $this->itbis = round($itbis, 2);
        $this->total = round($subtotal + $itbis - $this->descuento, 2);
        $this->save();
    }
}
