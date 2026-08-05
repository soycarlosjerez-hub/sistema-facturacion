<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Exhibicion extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'exhibiciones';

    protected $fillable = [
        'titulo',
        'slug',
        'lugar',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'tipo',
        'activo',
        'featured_image',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public const TIPOS = [
        'individual' => 'Individual',
        'colectiva' => 'Colectiva',
    ];

    public function obras(): BelongsToMany
    {
        return $this->belongsToMany(Obra::class, 'exhibicion_obras');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exhibicion) {
            if (empty($exhibicion->slug)) {
                $exhibicion->slug = static::generateSlug($exhibicion->titulo);
            }
        });

        static::updating(function ($exhibicion) {
            if ($exhibicion->isDirty('titulo')) {
                $exhibicion->slug = static::generateSlug($exhibicion->titulo);
            }
        });
    }

    public static function generateSlug(string $titulo): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo), '-'));
        $base = $slug;
        $counter = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }
        return $slug;
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getEstaActivaAttribute(): bool
    {
        if (!$this->activo) {
            return false;
        }
        $now = now()->startOfDay();
        $inicio = $this->fecha_inicio ? $this->fecha_inicio->startOfDay() : null;
        $fin = $this->fecha_fin ? $this->fecha_fin->startOfDay() : null;

        if ($inicio && $now->lt($inicio)) {
            return false;
        }
        if ($fin && $now->gt($fin)) {
            return false;
        }
        return true;
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true)
            ->where(function ($q) {
                $q->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now());
            });
    }

    public function scopePasadas($query)
    {
        return $query->where('fecha_fin', '<', now());
    }

    public function scopeByTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('titulo', 'like', "%{$search}%")
                ->orWhere('lugar', 'like', "%{$search}%");
        });
    }
}
