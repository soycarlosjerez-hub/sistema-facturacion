<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Conduce extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero', 'fecha', 'fecha_entrega', 'fecha_recibido',
        'cliente_id', 'user_id', 'sucursal_id', 'venta_id',
        'transportista', 'vehiculo', 'placa', 'chofer', 'chofer_cedula',
        'direccion_origen', 'direccion_entrega', 'contacto_entrega',
        'telefono_entrega', 'referencia',
        'estado', 'recibido_por', 'recibido_cedula',
        'observaciones', 'total_items', 'peso_total',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_entrega' => 'date',
        'fecha_recibido' => 'datetime',
        'total_items' => 'integer',
        'peso_total' => 'decimal:2',
    ];

    public const ESTADOS = [
        'borrador' => [
            'label' => 'Borrador',
            'color' => 'secondary',
            'icon' => 'pencil-square',
            'descripcion' => 'En preparación, aún no se ha enviado',
        ],
        'en_transito' => [
            'label' => 'En tránsito',
            'color' => 'info',
            'icon' => 'truck',
            'descripcion' => 'La mercancía está en camino',
        ],
        'entregado' => [
            'label' => 'Entregado',
            'color' => 'success',
            'icon' => 'check-circle-fill',
            'descripcion' => 'Entregado al cliente',
        ],
        'devuelto' => [
            'label' => 'Devuelto',
            'color' => 'warning',
            'icon' => 'arrow-return-left',
            'descripcion' => 'Devuelto por el cliente o transportista',
        ],
        'cancelado' => [
            'label' => 'Cancelado',
            'color' => 'dark',
            'icon' => 'x-circle',
            'descripcion' => 'Cancelado antes de envío',
        ],
    ];

    public static function generarNumero(): string
    {
        $year = date('Y');
        $ultimo = self::where('numero', 'like', "COND-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        
        $numero = $ultimo ? ((int) substr($ultimo->numero, -6)) + 1 : 1;
        
        return sprintf('COND-%s-%06d', $year, $numero);
    }

    // ==================== RELACIONES ====================

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

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConduceItem::class)->orderBy('orden');
    }

    // ==================== ACCESSORS ====================

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado]['label'] ?? $this->estado;
    }

    public function getEstadoColorAttribute(): string
    {
        return self::ESTADOS[$this->estado]['color'] ?? 'secondary';
    }

    public function getEstadoIconAttribute(): string
    {
        return self::ESTADOS[$this->estado]['icon'] ?? 'circle';
    }

    public function getCantidadItemsAttribute(): int
    {
        return $this->items->sum('cantidad');
    }

    public function getEstaVencidoAttribute(): bool
    {
        if (!$this->fecha_entrega) return false;
        return $this->fecha_entrega->isPast() 
            && !in_array($this->estado, ['entregado', 'cancelado', 'devuelto']);
    }

    public function getPuedeEntregarseAttribute(): bool
    {
        return in_array($this->estado, ['en_transito', 'borrador']);
    }

    public function getDireccionCompletaAttribute(): string
    {
        $partes = array_filter([
            $this->direccion_entrega,
            $this->referencia,
        ]);
        return implode(' - ', $partes);
    }

    // ==================== SCOPES ====================

    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', ['cancelado']);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['borrador', 'en_transito']);
    }

    public function scopeEntregados($query)
    {
        return $query->where('estado', 'entregado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_entrega', '<', now())
            ->whereNotIn('estado', ['entregado', 'cancelado', 'devuelto']);
    }

    public function scopePorCliente($query, int $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopePorFecha($query, $desde, $hasta = null)
    {
        $query->where('fecha', '>=', $desde);
        if ($hasta) $query->where('fecha', '<=', $hasta);
        return $query;
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('numero', 'like', "%{$termino}%")
              ->orWhere('transportista', 'like', "%{$termino}%")
              ->orWhere('chofer', 'like', "%{$termino}%")
              ->orWhere('placa', 'like', "%{$termino}%")
              ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$termino}%"));
        });
    }

    // ==================== MÉTODOS DE NEGOCIO ====================

    /**
     * Calcular totales del conduce
     */
    public function calcularTotales(): void
    {
        $this->total_items = $this->items->sum('cantidad');
        $this->peso_total = $this->items->sum('peso');
        $this->save();
    }

    /**
     * Marcar como entregado
     */
    public function marcarEntregado(string $recibidoPor, ?string $cedula = null, array $itemsRecibidos = []): bool
    {
        if (!$this->puede_entregarse) {
            return false;
        }

        $this->update([
            'estado' => 'entregado',
            'recibido_por' => $recibidoPor,
            'recibido_cedula' => $cedula,
            'fecha_recibido' => now(),
        ]);

        if (!empty($itemsRecibidos)) {
            foreach ($this->items()->get() as $item) {
                if (isset($itemsRecibidos[$item->id])) {
                    $item->update([
                        'cantidad_recibida' => (float) $itemsRecibidos[$item->id],
                    ]);
                } else {
                    $item->update(['cantidad_recibida' => $item->cantidad]);
                }
            }
        } else {
            $this->items()->whereNull('cantidad_recibida')->update(['cantidad_recibida' => DB::raw('cantidad')]);
        }

        return true;
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado(string $nuevoEstado): bool
    {
        if (!array_key_exists($nuevoEstado, self::ESTADOS)) {
            return false;
        }

        $this->update(['estado' => $nuevoEstado]);
        return true;
    }
}
