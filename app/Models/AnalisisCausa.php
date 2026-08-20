<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AnalisisCausa extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'analisis_causas';

    protected $fillable = [
        'no_conformidad_id',
        'metodologia',
        'causa_raiz',
        'evidencia',
        'diagrama',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    // -- Relaciones --

    public function noConformidad(): BelongsTo
    {
        return $this->belongsTo(NoConformidad::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function modificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // -- Scopes --

    public function scopePorMetodologia($query, string $metodologia)
    {
        return $query->where('metodologia', $metodologia);
    }

    public function scopeConCausaRaiz($query)
    {
        return $query->whereNotNull('causa_raiz')
            ->where('causa_raiz', '!=', '');
    }

    public function scopePorNoConformidad($query, int $noConformidadId)
    {
        return $query->where('no_conformidad_id', $noConformidadId);
    }

    public function scopeConDiagrama($query)
    {
        return $query->whereNotNull('diagrama');
    }

    // -- Accessors --

    public function getMetodologiaLabelAttribute(): string
    {
        $labels = [
            '5_for_why' => '5 Por Qué',
            'ishikawa'  => 'Ishikawa (Espina de Pescado)',
            '8d'        => '8D',
            'otro'      => 'Otro',
        ];

        return $labels[$this->metodologia ?? 'otro'] ?? 'Otro';
    }

    public function getColorBadgeMetodologiaAttribute(): string
    {
        $colors = [
            '5_for_why' => 'info',
            'ishikawa'  => 'primary',
            '8d'        => 'warning',
            'otro'      => 'secondary',
        ];

        return $colors[$this->metodologia ?? 'otro'] ?? 'secondary';
    }

    public function getDiagramaUrlAttribute(): ?string
    {
        if (!$this->diagrama) {
            return null;
        }

        return asset('storage/' . $this->diagrama);
    }

    public function getCausaRaizTruncadaAttribute(): string
    {
        if (!$this->causa_raiz) {
            return '—';
        }

        return strlen($this->causa_raiz) > 100
            ? substr($this->causa_raiz, 0, 100) . '...'
            : $this->causa_raiz;
    }

    public function auditLabel(): string
    {
        return "Análisis Causa NC #{$this->id}: {$this->metodologia_label}";
    }

    /**
     * Opciones para select de metodologías.
     */
    public static function getMetodologiaOptions(): array
    {
        return [
            '5_for_why' => '5 Por Qué',
            'ishikawa'  => 'Ishikawa (Espina de Pescado)',
            '8d'        => '8D',
            'otro'      => 'Otro',
        ];
    }
}
