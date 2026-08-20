<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReclamoCliente extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'reclamos_clientes';

    protected $fillable = [
        'codigo',
        'cliente_id',
        'canal',
        'tipo',
        'descripcion',
        'estado',
        'fecha_resolucion',
        'resolucion',
        'tiempo_respuesta_horas',
        'satisfaccion_resolucion',
        'asignado_a',
        'creado_por',
        'modificado_por',
        'tenant_id',
        'encuesta_id',
    ];

    protected $casts = [
        'fecha_resolucion'        => 'date',
        'tiempo_respuesta_horas'  => 'integer',
        'satisfaccion_resolucion' => 'integer',
    ];

    // -- Relaciones --

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(EncuestaSatisfaccion::class, 'encuesta_id');
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

    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierto');
    }

    public function scopeEnTramite($query)
    {
        return $query->where('estado', 'en_tramite');
    }

    public function scopeResueltos($query)
    {
        return $query->where('estado', 'resuelto');
    }

    public function scopeCerrados($query)
    {
        return $query->where('estado', 'cerrado');
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorCanal($query, string $canal)
    {
        return $query->where('canal', $canal);
    }

    public function scopeAsignados($query)
    {
        return $query->whereNotNull('asignado_a');
    }

    public function scopeSinAsignar($query)
    {
        return $query->whereNull('asignado_a');
    }

    public function scopeDelPeriodo($query, string $fechaInicio, string $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('tiempo_respuesta_horas', '>', 24)
            ->whereNotIn('estado', ['resuelto', 'cerrado', 'rechazado']);
    }

    // -- Accessors --

    public function getCanalLabelAttribute(): string
    {
        $labels = [
            'web'               => 'Sitio Web',
            'telefono'          => 'Teléfono',
            'presencial'        => 'Presencial',
            'email'             => 'Email',
            'redes_sociales'    => 'Redes Sociales',
        ];

        return $labels[$this->canal ?? 'web'] ?? 'Sitio Web';
    }

    public function getColorBadgeCanalAttribute(): string
    {
        $colors = [
            'web'               => 'primary',
            'telefono'          => 'success',
            'presencial'        => 'info',
            'email'             => 'warning',
            'redes_sociales'    => 'danger',
        ];

        return $colors[$this->canal ?? 'web'] ?? 'secondary';
    }

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'reclamo'    => 'Reclamo',
            'queja'      => 'Queja',
            'sugerencia' => 'Sugerencia',
            'cumpliment' => 'Cumplido',
        ];

        return $labels[$this->tipo ?? 'reclamo'] ?? 'Reclamo';
    }

    public function getColorBadgeTipoAttribute(): string
    {
        $colors = [
            'reclamo'    => 'danger',
            'queja'      => 'warning',
            'sugerencia' => 'info',
            'cumpliment' => 'success',
        ];

        return $colors[$this->tipo ?? 'reclamo'] ?? 'secondary';
    }

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'abierto'     => 'Abierto',
            'en_tramite'  => 'En Trámite',
            'resuelto'    => 'Resuelto',
            'rechazado'   => 'Rechazado',
            'cerrado'     => 'Cerrado',
        ];

        return $labels[$this->estado ?? 'abierto'] ?? 'Abierto';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'abierto'     => 'danger',
            'en_tramite'  => 'warning',
            'resuelto'    => 'success',
            'rechazado'   => 'secondary',
            'cerrado'     => 'info',
        ];

        return $colors[$this->estado ?? 'abierto'] ?? 'secondary';
    }

    public function getSatisfaccionLabelAttribute(): string
    {
        if ($this->satisfaccion_resolucion === null) {
            return 'Sin evaluar';
        }

        $estrellas = str_repeat('★', $this->satisfaccion_resolucion) . str_repeat('☆', 5 - $this->satisfaccion_resolucion);
        return "{$estrellas} ({$this->satisfaccion_resolucion}/5)";
    }

    public function getFechaCreacionLabelAttribute(): string
    {
        return $this->created_at?->format('d/m/Y H:i') ?? '—';
    }

    public function getEstadoUltimoTiempo(): string
    {
        if ($this->fecha_resolucion) {
            return $this->fecha_resolucion->diffForHumans();
        }

        return $this->created_at->diffForHumans();
    }

    public function getAsignadoALabelAttribute(): string
    {
        return $this->asignado_a?->name ?? 'Sin asignar';
    }

    // -- Helpers --

    /**
     * Asigna un reclamo a un responsable.
     */
    public function asignarResponsable(int $usuarioId): static
    {
        $this->asignado_a = $usuarioId;
        $this->estado = 'en_tramite';
        $this->saveQuietly();
        return $this;
    }

    /**
     * Marca el reclamo como resuelto.
     */
    public function resolver(string $resolucion, int $tiempoRespuestaHoras = null): static
    {
        $this->resolucion = $resolucion;
        $this->fecha_resolucion = now()->toDateString();
        $this->tiempo_respuesta_horas = $tiempo_respuesta_horas ?? $this->tiempo_respuesta_horas;
        $this->estado = 'resuelto';
        $this->saveQuietly();
        return $this;
    }

    /**
     * Establece la satisfacción con la resolución.
     */
    public function setSatisfaccion(int $valor): static
    {
        $this->satisfaccion_resolucion = max(1, min(5, $valor));
        $this->saveQuietly();
        return $this;
    }

    /**
     * Calcula el tiempo de respuesta en horas desde la creación.
     */
    public function getTiempoRespuestaCalculadoAttribute(): int
    {
        if (!$this->created_at) {
            return 0;
        }

        return (int) ceil(now()->diffInHours($this->created_at));
    }

    /**
     * Genera un código único para el reclamo.
     */
    protected static function booted(): void
    {
        static::creating(function (ReclamoCliente $reclamo) {
            if (empty($reclamo->codigo)) {
                $prefix = match ($reclamo->tipo) {
                    'reclamo'    => 'REC',
                    'queja'      => 'QUE',
                    'sugerencia' => 'SUG',
                    'cumpliment' => 'CUM',
                    default      => 'RCL',
                };

                $year = date('Y');
                $lastNum = static::whereYear('created_at', $year)
                    ->where('codigo', 'like', "{$prefix}-{$year}-%")
                    ->orderBy('id', 'desc')
                    ->value('codigo');

                $num = 1;
                if ($lastNum) {
                    $parts = explode('-', $lastNum);
                    $num = (int) $parts[count($parts) - 1] + 1;
                }

                $reclamo->codigo = sprintf('%s-%s-%04d', $prefix, $year, $num);
            }
        });

        static::creating(function (ReclamoCliente $reclamo) {
            if (empty($reclamo->tiempo_respuesta_horas) && !empty($reclamo->created_at)) {
                $reclamo->tiempo_respuesta_horas = now()->diffInHours($reclamo->created_at);
            }
        });
    }

    public function auditLabel(): string
    {
        return "Reclamo {$this->codigo}: {$this->cliente_id} ({$this->estado_label})";
    }
}
