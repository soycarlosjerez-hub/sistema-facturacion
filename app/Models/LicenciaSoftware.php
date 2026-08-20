<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Support\Facades\Auth;

class LicenciaSoftware extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'licencias_software';

    protected $fillable = [
        'producto_id',
        'clave_licencia',
        'tipo_licencia',
        'usuario_asignado',
        'licencia_activa',
        'fecha_vencimiento',
        'plataforma',
        'notas',
        'tenant_id',
    ];

    protected $casts = [
        'licencia_activa'  => 'boolean',
        'fecha_vencimiento' => 'date',
    ];

    protected $appends = ['estado_label', 'dias_hasta_vencer'];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('licencia_activa', true);
    }

    public function scopePorVencer(Builder $query): Builder
    {
        return $query->where('licencia_activa', true)
            ->where('fecha_vencimiento', '>=', today())
            ->where('fecha_vencimiento', '<=', today()->addDays(30));
    }

    public function scopePorPlataforma(Builder $query, string $plataforma): Builder
    {
        return $query->where('plataforma', $plataforma);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo_licencia', $tipo);
    }

    public function scopePorUsuario(Builder $query, string $usuario): Builder
    {
        return $query->where('usuario_asignado', 'like', "%{$usuario}%");
    }

    public function getEstadoLabelAttribute(): string
    {
        if (!$this->licencia_activa) {
            return 'Inactiva';
        }

        if ($this->fecha_vencimiento && $this->fecha_vencimiento->lt(today())) {
            return 'Vencida';
        }

        if ($this->fecha_vencimiento && $this->fecha_vencimiento->lte(today()->addDays(30))) {
            return 'Por Vencer';
        }

        return 'Activa';
    }

    public function getDiasHastaVencerAttribute(): int
    {
        if (!$this->fecha_vencimiento) {
            return 0;
        }

        return today()->diffInDays($this->fecha_vencimiento, false);
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query->where('licencia_activa', true)
            ->where('fecha_vencimiento', '<', today());
    }
}
