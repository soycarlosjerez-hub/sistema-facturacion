<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class TattooDesign extends Model
{
    use Auditable, TenantScope;

    protected $fillable = [
        'tenant_id', 'artist_id', 'titulo', 'descripcion', 'estilo',
        'imagen_portada', 'galeria_imagenes', 'precio_minimo', 'precio_maximo',
        'duracion_estimada_min', 'popular', 'activo',
    ];

    protected $casts = [
        'galeria_imagenes'   => 'array',
        'precio_minimo'      => 'decimal:2',
        'precio_maximo'      => 'decimal:2',
        'duracion_estimada_min' => 'integer',
        'popular'            => 'boolean',
        'activo'             => 'boolean',
    ];

    public function artist()
    {
        return $this->belongsTo(TattooArtist::class, 'artist_id');
    }

    public function appointments()
    {
        return $this->hasMany(TattooAppointment::class, 'diseno_id');
    }

    public function scopePopulares($query)
    {
        return $query->where('popular', true);
    }

    public function scopePorEstilo($query, string $estilo)
    {
        return $query->where('estilo', $estilo);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getPrecioPromedioAttribute(): float
    {
        return ($this->precio_minimo + $this->precio_maximo) / 2;
    }
}
