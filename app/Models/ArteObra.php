<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArteObra extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $fillable = [
        'tenant_id',
        'titulo',
        'descripcion',
        'artista_id',
        'coleccion_id',
        'tecnica',
        'ano_creacion',
        'dimensiones',
        'material',
        'precio_compra',
        'precio_venta',
        'estado',
        'fecha_adquisicion',
        'imagen',
        'activo',
        'orden',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'activo' => 'boolean',
        'orden' => 'integer',
        'ano_creacion' => 'integer',
        'fecha_adquisicion' => 'date',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeVendidas($query)
    {
        return $query->where('estado', 'vendida');
    }

    public function scopeEnExhibicion($query)
    {
        return $query->where('estado', 'en_exhibicion');
    }

    public function artista(): BelongsTo
    {
        return $this->belongsTo(ArteArtista::class);
    }

    public function coleccion(): BelongsTo
    {
        return $this->belongsTo(ArteColeccion::class);
    }

    public function consignacion(): HasOne
    {
        return $this->hasOne(ArteConsignment::class)->latestOfMany();
    }

    public function exhibiciones(): BelongsToMany
    {
        return $this->belongsToMany(ArteExhibicion::class, 'arte_exhibicion_obras', 'obra_id', 'exhibicion_id')
            ->withPivot('ubicacion_en_sala', 'fecha_asignacion');
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'vendida' => 'Vendida',
            'disponible' => 'Disponible',
            'en_exhibicion' => 'En Exhibición',
            'en_consulta' => 'En Consulta',
            default => ucfirst($this->estado),
        };
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match($this->estado) {
            'vendida' => 'danger',
            'disponible' => 'success',
            'en_exhibicion' => 'info',
            'en_consulta' => 'warning',
            default => 'secondary',
        };
    }
}