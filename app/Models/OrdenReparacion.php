<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class OrdenReparacion extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'ordenes_reparacion';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'numero_orden',
        'cliente_id',
        'equipo_id',
        'tecnico_id',
        'tipo_servicio',
        'problema_reportado',
        'diagnostico',
        'solucion_aplicada',
        'costo_piezas',
        'mano_obra',
        'subtotal',
        'itbis',
        'descuento',
        'total',
        'estado',
        'fecha_recibo',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'metodo_pago',
        'notas',
        'garantia_extendida',
        'creado_por',
    ];

    protected $casts = [
        'costo_piezas' => 'decimal:2',
        'mano_obra' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'itbis' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_recibo' => 'datetime',
        'fecha_entrega_estimada' => 'datetime',
        'fecha_entrega_real' => 'datetime',
        'garantia_extendida' => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Tecnico::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function detallesPiezas(): HasMany
    {
        return $this->hasMany(DetallePiezaReparacion::class);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['recibido', 'pendiente']);
    }

    public function scopeEnReparacion($query)
    {
        return $query->where('estado', 'en_reparacion');
    }

    public function scopeEntregadas($query)
    {
        return $query->where('estado', 'entregado');
    }

    public function scopePorTecnico($query, $tecnicoId)
    {
        return $query->where('tecnico_id', $tecnicoId);
    }

    public function getEstadoLabelAttribute(): ?string
    {
        return match ($this->estado) {
            'recibido' => 'Recibido',
            'pendiente' => 'Pendiente',
            'en_reparacion' => 'En Reparación',
            'esperando_piezas' => 'Esperando Piezas',
            'terminado' => 'Terminado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
            default => null,
        };
    }

    public function getTipoServicioLabelAttribute(): ?string
    {
        return match ($this->tipo_servicio) {
            'hardware' => 'Hardware',
            'software' => 'Software',
            'desbloqueo' => 'Desbloqueo',
            'recuperacion_datos' => 'Recuperación de Datos',
            'mantenimiento' => 'Mantenimiento',
            'personalizacion' => 'Personalización',
            'otro' => 'Otro',
            default => null,
        };
    }

    public function getTiempoReparacionAttribute(): int
    {
        $inicio = $this->fecha_recibo;
        $fin = $this->fecha_entrega_real ?: now();

        if (!$inicio) {
            return 0;
        }

        return round($inicio->diffInHours($fin));
    }

    public function calcularTotales(): void
    {
        $this->subtotal = $this->costo_piezas + $this->mano_obra;
        $this->itbis = $this->subtotal * 0.18;
        $this->total = $this->subtotal + $this->itbis - $this->descuento;
        $this->save();
    }

    public static function generarNumeroOrden(): string
    {
        $year = date('Y');
        $ultimo = self::where('numero_orden', 'like', "OR-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimo) {
            $num = (int) substr($ultimo->numero_orden, -6) + 1;
        } else {
            $num = 1;
        }

        return sprintf('OR-%s-%06d', $year, $num);
    }
}
