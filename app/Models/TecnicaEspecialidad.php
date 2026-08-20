<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class TecnicaEspecialidad extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'tecnica_especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'orden',
        'tenant_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
    ];

    protected $appends = ['activo_label'];

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    public function tecnicos(): BelongsToMany
    {
        return $this->belongsToMany(Tecnico::class, 'tecnica_especialidad_tecnico', 'tecnica_especialidad_id', 'tecnico_id')
            ->withPivot('fecha_asignacion', 'nivel_experiencia', 'activo')
            ->withTimestamps();
    }

    public function getActivoLabelAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function getTecnicosActivosCountAttribute(): int
    {
        return $this->tecnicos()
            ->wherePivot('activo', true)
            ->count();
    }
}
