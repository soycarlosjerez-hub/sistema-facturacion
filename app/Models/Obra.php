<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Obra extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'obras';

    protected $fillable = [
        'categoria_id',
        'titulo',
        'codigo_unico',
        'slug',
        'dimensiones',
        'peso_kg',
        'medium',
        'technique',
        'year_created',
        'edition_number',
        'edition_total',
        'certificate_number',
        'photos',
        'condition_status',
        'creation_date',
        'exhibition_history',
        'is_original',
        'status',
        'cost_materials',
        'tenant_id',
    ];

    protected $casts = [
        'photos' => 'array',
        'exhibition_history' => 'array',
        'is_original' => 'boolean',
        'year_created' => 'integer',
        'edition_number' => 'integer',
        'edition_total' => 'integer',
        'peso_kg' => 'decimal:2',
        'cost_materials' => 'decimal:2',
        'creation_date' => 'date',
    ];

    public const STATUSES = [
        'disponible' => ['label' => 'Disponible', 'color' => 'success', 'icon' => 'check-circle'],
        'vendido' => ['label' => 'Vendido', 'color' => 'danger', 'icon' => 'x-circle'],
        'reservado' => ['label' => 'Reservado', 'color' => 'warning', 'icon' => 'clock'],
        'en_consulta' => ['label' => 'En Consulta', 'color' => 'info', 'icon' => 'eye'],
        'en_exposicion' => ['label' => 'En Exposicion', 'color' => 'primary', 'icon' => 'palette'],
        'en_consignacion' => ['label' => 'En Consignacion', 'color' => 'secondary', 'icon' => 'building'],
    ];

    public const CONDITIONS = [
        'excelente' => 'Excelente',
        'bueno' => 'Bueno',
        'regular' => 'Regular',
        'necesita_restauracion' => 'Necesita Restauracion',
    ];

    public const MEDIUMS = [
        'bronce' => 'Bronce',
        'marmol' => 'Marmol',
        'madera' => 'Madera',
        'hierro' => 'Hierro',
        'mixed_media' => 'Mixed Media',
        'arcilla' => 'Arcilla',
        'yeso' => 'Yeso',
        'otros' => 'Otros',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($obra) {
            if (empty($obra->slug)) {
                $obra->slug = static::generateSlug($obra->titulo);
            }
            if (empty($obra->codigo_unico)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $obra->codigo_unico = sprintf('OBRA-%s-%04d', $year, $count);
            }
        });

        static::updating(function ($obra) {
            if ($obra->isDirty('titulo')) {
                $obra->slug = static::generateSlug($obra->titulo);
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

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function certificado(): HasOne
    {
        return $this->hasOne(CertificadoAutenticidad::class);
    }

    public function consignaciones()
    {
        return $this->hasMany(Consignacion::class);
    }

    public function exhibiciones()
    {
        return $this->belongsToMany(Exhibicion::class, 'exhibicion_obras');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status && isset(self::STATUSES[$this->status])
            ? self::STATUSES[$this->status]['label']
            : 'Desconocido';
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'secondary';
    }

    public function getStatusIconAttribute(): string
    {
        return self::STATUSES[$this->status]['icon'] ?? 'circle';
    }

    public function getConditionStatusLabelAttribute(): string
    {
        return self::CONDITIONS[$this->condition_status] ?? $this->condition_status;
    }

    public function getMediumLabelAttribute(): string
    {
        return self::MEDIUMS[$this->medium] ?? $this->medium;
    }

    public function getHasCertificateAttribute(): bool
    {
        return $this->certificate !== null;
    }

    public function getPrimaryPhotos(): array
    {
        $photos = $this->photos ?? [];
        if (!is_array($photos)) {
            return [];
        }
        return array_map(function ($photo) {
            return $this->formatImageUrl($photo);
        }, array_slice($photos, 0, 3));
    }

    public function getAllPhotos(): array
    {
        $photos = $this->photos ?? [];
        if (!is_array($photos)) {
            return [];
        }
        return array_map([$this, 'formatImageUrl'], $photos);
    }

    protected function formatImageUrl(string $photo): string
    {
        if (str_starts_with($photo, 'http')) {
            return $photo;
        }
        return asset('storage/' . $photo);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('status', 'disponible');
    }

    public function scopeEnExposicion($query)
    {
        return $query->where('status', 'en_exposicion');
    }

    public function scopeByMedium($query, $medium)
    {
        return $query->where('medium', $medium);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOriginals($query)
    {
        return $query->where('is_original', true);
    }

    public function scopeEditions($query)
    {
        return $query->where('is_original', false);
    }

    public function scopeWithCertificate($query)
    {
        return $query->whereNotNull('certificate_number');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('titulo', 'like', "%{$search}%")
                ->orWhere('codigo_unico', 'like', "%{$search}%")
                ->orWhere('medium', 'like', "%{$search}%");
        });
    }

    public function scopeFilterByYearRange($query, $from = null, $to = null)
    {
        if ($from) {
            $query->where('year_created', '>=', $from);
        }
        if ($to) {
            $query->where('year_created', '<=', $to);
        }
        return $query;
    }

    public function scopeOrderByField($query, $field, $order = 'desc')
    {
        $allowed = ['titulo', 'year_created', 'created_at'];
        $field = in_array($field, $allowed) ? $field : 'created_at';
        $order = in_array(strtolower($order), ['asc', 'desc']) ? strtolower($order) : 'desc';
        return $query->orderBy($field, $order);
    }
}
