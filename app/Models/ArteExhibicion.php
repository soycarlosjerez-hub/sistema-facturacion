<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArteExhibicion extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'descripcion',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function obras(): BelongsToMany
    {
        return $this->belongsToMany(ArteObra::class, 'arte_exhibicion_obras')
            ->withPivot('ubicacion_en_sala', 'fecha_asignacion');
    }

    public function getRangoFechasAttribute(): string
    {
        if (!$this->fecha_inicio) return '';
        if (!$this->fecha_fin) return $this->fecha_inicio->format('d/m/Y');
        return $this->fecha_inicio->format('d/m/Y') . ' — ' . $this->fecha_fin->format('d/m/Y');
    }
}