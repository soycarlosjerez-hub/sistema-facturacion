<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NoConformidad extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'no_conformidades';

    protected $fillable = [
        'numero',
        'fecha_ocurrencia',
        'fecha_identificacion',
        'area_id',
        'origen',
        'descripcion',
        'evidencia',
        'gravedad',
        'estado',
        'analisis_causa_metodo',
        'analisis_causa_detalle',
        'accion_contencion',
        'fecha_limite',
        'asignado_a',
        'auditoria_id',
        'encuesta_id',
        'nc_id',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_ocurrencia'      => 'date',
        'fecha_identificacion'  => 'date',
        'fecha_limite'          => 'date',
    ];

    // -- Relaciones --

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(AuditoriaInterna::class, 'auditoria_id');
    }

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(EncuestaSatisfaccion::class, 'encuesta_id');
    }

    public function ncPadre(): BelongsTo
    {
        return $this->belongsTo(NoConformidad::class, 'nc_id');
    }

    public function ncHijas(): HasMany
    {
        return $this->hasMany(NoConformidad::class, 'nc_id');
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(DocumentoSgc::class, 'auditable');
    }

    public function analisisCausa(): HasOne
    {
        return $this->hasOne(AnalisisCausa::class);
    }

    public function accionesCorrectivas(): HasMany
    {
        return $this->hasMany(AccionCorrectiva::class);
    }

    public function verificacion(): HasOne
    {
        return $this->hasOne(VerificacionAccion::class);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // -- Scopes --

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeAbiertas($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeEnAnalisis($query)
    {
        return $query->where('estado', 'en_analisis');
    }

    public function scopeEnAccion($query)
    {
        return $query->where('estado', 'en_accion');
    }

    public function scopeEnVerificacion($query)
    {
        return $query->where('estado', 'verificando');
    }

    public function scopeCerradas($query)
    {
        return $query->where('estado', 'cerrada');
    }

    public function scopePorGravedad($query, string $gravedad)
    {
        return $query->where('gravedad', $gravedad);
    }

    public function scopeGraves($query)
    {
        return $query->where('gravedad', 'mayor');
    }

    public function scopePorOrigen($query, string $origen)
    {
        return $query->where('origen', $origen);
    }

    public function scopePorAuditoria($query, int $auditoriaId)
    {
        return $query->where('auditoria_id', $auditoriaId);
    }

    public function scopePorResponsable($query, int $responsableId)
    {
        return $query->where('asignado_a', $responsableId);
    }

    public function scopeVencidas($query)
    {
        return $query->whereNotNull('fecha_limite')
            ->where('fecha_limite', '<', now()->toDateString())
            ->whereNotIn('estado', ['cerrada']);
    }

    public function scopeProximasAVencer($query, int $dias = 7)
    {
        return $query->whereNotNull('fecha_limite')
            ->where('fecha_limite', '>=', now()->toDateString())
            ->where('fecha_limite', '<=', now()->addDays($dias)->toDateString())
            ->whereNotIn('estado', ['cerrada', 'en_accion']);
    }

    public function scopeSinNCRelacionada($query)
    {
        return $query->whereNull('nc_id');
    }

    public function scopeConNCRelacionada($query)
    {
        return $query->whereNotNull('nc_id');
    }

    public function scopeDelPeriodo($query, string $fechaInicio, string $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'abierta'     => 'Abierta',
            'en_analisis' => 'En Análisis',
            'en_accion'   => 'En Acción',
            'verificando' => 'Verificando',
            'cerrada'     => 'Cerrada',
        ];

        return $labels[$this->estado ?? 'abierta'] ?? 'Abierta';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'abierta'     => 'danger',
            'en_analisis' => 'warning',
            'en_accion'   => 'primary',
            'verificando' => 'info',
            'cerrada'     => 'success',
        ];

        return $colors[$this->estado ?? 'abierta'] ?? 'secondary';
    }

    public function getGravedadLabelAttribute(): string
    {
        $labels = [
            'mayor' => 'Mayor',
            'menor' => 'Menor',
        ];

        return $labels[$this->gravedad ?? 'menor'] ?? 'Menor';
    }

    public function getColorBadgeGravedadAttribute(): string
    {
        $colors = [
            'mayor' => 'danger',
            'menor' => 'warning',
        ];

        return $colors[$this->gravedad ?? 'menor'] ?? 'secondary';
    }

    public function getOrigenLabelAttribute(): string
    {
        $labels = [
            'auditoria'        => 'Auditoría',
            'proceso_interno'  => 'Proceso Interno',
            'reclamo_cliente'  => 'Reclamo Cliente',
            'observacion_direccion' => 'Observación Dirección',
        ];

        return $labels[$this->origen ?? 'proceso_interno'] ?? 'Proceso Interno';
    }

    public function getColorBadgeOrigenAttribute(): string
    {
        $colors = [
            'auditoria'        => 'primary',
            'proceso_interno'  => 'info',
            'reclamo_cliente'  => 'danger',
            'observacion_direccion' => 'warning',
        ];

        return $colors[$this->origen ?? 'proceso_interno'] ?? 'secondary';
    }

    public function getAnalisisCausaMetodoLabelAttribute(): string
    {
        if (!$this->analisis_causa_metodo) {
            return '—';
        }

        $labels = [
            '5_for_why' => '5 Por Qué',
            'ishikawa'  => 'Ishikawa (Espina de Pescado)',
            '8d'        => '8D',
            'otro'      => 'Otro',
        ];

        return $labels[$this->analisis_causa_metodo ?? 'otro'] ?? 'Otro';
    }

    public function getAsignadoALabelAttribute(): string
    {
        return $this->asignado_a?->name ?? 'Sin asignar';
    }

    public function getFechaLimiteLabelAttribute(): string
    {
        return $this->fecha_limite?->format('d/m/Y') ?? 'Sin límite';
    }

    public function getNumeroLabelAttribute(): string
    {
        return $this->numero ?? sprintf('NC-#%d', $this->id);
    }

    public function getEsVencidaAttribute(): bool
    {
        if (!$this->fecha_limite || $this->estado === 'cerrada') {
            return false;
        }

        return $this->fecha_limite->isPast();
    }

    public function getDiasRestantesAttribute(): int
    {
        if (!$this->fecha_limite) {
            return 0;
        }

        return now()->diffInDays($this->fecha_limite, false);
    }

    public function getNcPadreNumeroLabelAttribute(): ?string
    {
        return $this->ncPadre?->numero;
    }

    public function getAccionesCorrectivasCountAttribute(): int
    {
        return $this->accionesCorrectivas()->count();
    }

    // -- Helpers --

    /**
     * Inicia el análisis de causa.
     */
    public function iniciarAnalisis(string $metodo): static
    {
        $this->estado = 'en_analisis';
        $this->analisis_causa_metodo = $metodo;
        $this->saveQuietly();

        return $this;
    }

    /**
     * Asigna una acción contención.
     */
    public function asignarContencion(string $accion): static
    {
        $this->accion_contencion = $accion;
        $this->saveQuietly();

        return $this;
    }

    /**
     * Asigna responsable y estado a en_accion.
     */
    public function asignarResponsable(int $usuarioId): static
    {
        $this->asignado_a = $usuarioId;
        $this->estado = 'en_accion';
        $this->saveQuietly();

        return $this;
    }

    /**
     * Cierra la NC.
     */
    public function cerrar(): static
    {
        $this->estado = 'cerrada';
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "NC {$this->numero}: {$this->origen_label} - {$this->gravedad_label} ({$this->estado_label})";
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'abierta'       => ['label' => 'Abierta', 'color' => 'danger', 'value' => 'abierta'],
            'en_analisis'   => ['label' => 'En Análisis', 'color' => 'warning', 'value' => 'en_analisis'],
            'en_accion'     => ['label' => 'En Acción', 'color' => 'primary', 'value' => 'en_accion'],
            'verificando'   => ['label' => 'Verificando', 'color' => 'info', 'value' => 'verificando'],
            'cerrada'       => ['label' => 'Cerrada', 'color' => 'success', 'value' => 'cerrada'],
        ];
    }

    /**
     * Opciones para select de gravedad.
     */
    public static function getGravedadOptions(): array
    {
        return [
            'mayor' => 'Mayor',
            'menor' => 'Menor',
        ];
    }

    /**
     * Opciones para select de orígenes.
     */
    public static function getOrigenOptions(): array
    {
        return [
            'auditoria'        => 'Auditoría',
            'proceso_interno'  => 'Proceso Interno',
            'reclamo_cliente'  => 'Reclamo Cliente',
            'observacion_direccion' => 'Observación Dirección',
        ];
    }
}
