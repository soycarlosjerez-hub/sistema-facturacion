<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArteArtista extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'arte_artistas';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'email',
        'telefono',
        'bio',
        'nacionalidad',
        'ano_nacimiento',
        'foto',
        'activo',
        'orden',
        'notas',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'ano_nacimiento' => 'integer',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function obras(): HasMany
    {
        return $this->hasMany(ArteObra::class);
    }

    public function totalObras()
    {
        return $this->obras()->count();
    }
}