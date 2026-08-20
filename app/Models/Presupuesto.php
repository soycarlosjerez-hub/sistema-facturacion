<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Presupuesto extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;
    use SoftDeletes;

    protected $table = 'presupuestos';

    protected $fillable = [
        'cliente_id',
        'numero',
        'estado',
        'subtotal',
        'itbis',
        'descuento',
        'total',
        'valido_hasta',
        'notas',
        'creado_por',
        'tenant_id',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'itbis'          => 'decimal:2',
        'descuento'      => 'decimal:2',
        'total'          => 'decimal:2',
        'valido_hasta'   => 'date',
    ];

    protected $appends = ['estado_label'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PresupuestoItem::class);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->whereIn('estado', ['borrador', 'enviado', 'aceptado']);
    }

    public function scopePorVencer(Builder $query): Builder
    {
        return $query->where('valido_hasta', '>=', today())
            ->where('valido_hasta', '<=', today()->addDays(7))
            ->whereIn('estado', ['enviado', 'aceptado']);
    }

    public function scopePorCliente(Builder $query, int $clienteId): Builder
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopePorEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }

    public function scopeConFechaValido(Builder $query, ?string $fecha): Builder
    {
        if ($fecha) {
            return $query->where('valido_hasta', $fecha);
        }

        return $query;
    }

    public static function generarNumero(): string
    {
        $year = date('Y');

        $ultimo = self::where('numero', 'like', "PRES-{$year}-%")
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        if ($ultimo) {
            $num = (int) substr($ultimo->numero, -6) + 1;
        } else {
            $num = 1;
        }

        return sprintf('PRES-%s-%06d', $year, $num);
    }

    public function calcularTotales(): void
    {
        $subtotal = 0;
        $itbisTotal = 0;
        $descuentoTotal = 0;

        foreach ($this->items as $item) {
            $item->calcular();
            $subtotal += (float) $item->subtotal;
            $itbisTotal += (float) $item->itbis;
            $descuentoTotal += (float) $item->descuento;
        }

        $baseImponible = $subtotal - $descuentoTotal;
        if ($baseImponible < 0) {
            $baseImponible = 0;
        }

        $this->subtotal = round($subtotal, 2);
        $this->itbis = round($itbisTotal, 2);
        $this->descuento = round($descuentoTotal, 2);
        $this->total = round($baseImponible + $this->itbis, 2);
    }

    public function getEstadoLabelAttribute(): ?string
    {
        return match ($this->estado) {
            'borrador'   => 'Borrador',
            'enviado'    => 'Enviado',
            'aceptado'   => 'Aceptado',
            'rechazado'  => 'Rechazado',
            'convertido' => 'Convertido a Venta',
            'cancelado'  => 'Cancelado',
            default      => null,
        };
    }

    public function getDiasVigenciaAttribute(): int
    {
        if (!$this->valido_hasta) {
            return 0;
        }

        return today()->diffInDays($this->valido_hasta, false);
    }

    public function isVigente(): bool
    {
        return $this->valido_hasta && $this->valido_hasta->gte(today())
            && in_array($this->estado, ['enviado', 'aceptado']);
    }

    /**
     * Convertir el presupuesto aceptado en una venta.
     * Esta funcionalidad depende de la implementación de Venta del sistema.
     *
     * @return \App\Models\Venta|null
     */
    public function convertirEnVenta(): ?Venta
    {
        if (!$this->isVigente() || $this->estado !== 'aceptado') {
            return null;
        }

        try {
            return DB::transaction(function () {
                $venta = Venta::create([
                    'cliente_id'     => $this->cliente_id,
                    'subtotal'       => $this->subtotal,
                    'impuestos'      => $this->itbis,
                    'descuento'      => $this->descuento,
                    'total'          => $this->total,
                    'notas'          => $this->notas . ' (Convertido desde presupuesto #' . $this->numero . ')',
                    'tenant_id'      => $this->tenant_id,
                ]);

                return $venta;
            });
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}
