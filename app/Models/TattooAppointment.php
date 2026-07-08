<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class TattooAppointment extends Model
{
    use Auditable, TenantScope;

    protected $fillable = [
        'tenant_id', 'cliente_id', 'artista_id', 'diseno_id', 'user_id',
        'fecha_hora_inicio', 'fecha_hora_fin', 'duracion_min',
        'estado', 'deposito_monto', 'deposito_pct', 'deposito_pagado',
        'metodo_deposito', 'total_servicio', 'descuento_aplicado', 'total_final',
        'notas_cliente', 'notas_internas', 'lugar_tatuaje', 'tamanio_approx',
        'revision_previa', 'revision_completada', 'revision_fecha',
    ];

    protected $casts = [
        'fecha_hora_inicio'  => 'datetime',
        'fecha_hora_fin'     => 'datetime',
        'revision_fecha'     => 'datetime',
        'deposito_monto'     => 'decimal:2',
        'deposito_pct'       => 'decimal:2',
        'deposito_pagado'    => 'boolean',
        'total_servicio'     => 'decimal:2',
        'descuento_aplicado' => 'decimal:2',
        'total_final'        => 'decimal:2',
        'duracion_min'       => 'integer',
        'revision_previa'    => 'boolean',
        'revision_completada' => 'boolean',
    ];

    public const ESTADOS = [
        'pendiente', 'confirmada', 'en_progreso', 'completada', 'cancelada', 'no_show',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function artista()
    {
        return $this->belongsTo(TattooArtist::class, 'artista_id');
    }

    public function diseno()
    {
        return $this->belongsTo(TattooDesign::class, 'diseno_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(TattooPayment::class, 'appointment_id');
    }

    public function getSaldoPendienteAttribute(): float
    {
        $pagado = $this->payments()->sum('monto');
        return max(0, $this->total_final - $pagado);
    }

    public function getGananciaArtistaAttribute(): float
    {
        if (!$this->artista) return 0;
        return $this->total_final * ($this->artista->comision_pct / 100);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora_inicio', today());
    }

    public function scopeProximas($query, int $dias = 7)
    {
        return $query->whereBetween('fecha_hora_inicio', [now(), now()->addDays($dias)]);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmada']);
    }
}
