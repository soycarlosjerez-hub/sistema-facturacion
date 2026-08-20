<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoSgc extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'documentos_sgc';

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
        'categoria',
        'formato',
        'version',
        'fecha_emision',
        'fecha_revision',
        'fecha_vencimiento',
        'estado',
        'versiones',
        'creado_por',
        'modificado_por',
        'aprobado_por',
        'tenant_id',
        'proveedor_id',
        'auditoria_id',
        'nc_id',
        'riesgo_id',
        'capacitacion_id',
        'mejora_id',
        'revision_direccion_id',
        'archivo_path',
        'archivo_original_name',
        'archivo_mime_type',
        'archivo_size_bytes',
        'checksum_sha256',
    ];

    protected $casts = [
        'versiones' => 'json',
        'fecha_emision' => 'date',
        'fecha_revision' => 'date',
        'fecha_vencimiento' => 'date',
        'archivo_size_bytes' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (DocumentoSgc $doc) {
            if (empty($doc->codigo)) {
                $prefix = match ($doc->categoria) {
                    'politica' => 'POL',
                    'procedimiento' => 'PROC',
                    'instructivo' => 'INST',
                    'formulario' => 'FORM',
                    'registro' => 'REG',
                    'matriz' => 'MAT',
                    'reporte' => 'REP',
                    default => 'DOC',
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
                $doc->codigo = sprintf('%s-%s-%04d', $prefix, $year, $num);
            }
        });

        static::created(function ($doc) {
            try {
                $data = $doc->toArray();
                $versionData = [
                    'version' => $doc->version,
                    'estado' => $doc->estado,
                    'archivo' => $doc->archivo_path,
                    'archivo_name' => $doc->archivo_original_name ?? null,
                    'archivo_size' => $doc->archivo_size_bytes ?? null,
                    'archivo_mime' => $doc->archivo_mime_type ?? null,
                    'created_at' => $doc->created_at->toISOString(),
                ];
                $doc->versiones = [$versionData];
                $doc->saveQuietly(['versiones']);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        static::updated(function ($doc) {
            try {
                if ($doc->isDirty(['archivo_path', 'archivo_original_name', 'archivo_mime_type', 'archivo_size_bytes', 'estado', 'version'])) {
                    $versions = $doc->versiones ?? [];
                    $newVersion = [
                        'version' => $doc->version,
                        'estado' => $doc->estado,
                        'archivo' => $doc->archivo_path,
                        'archivo_name' => $doc->archivo_original_name ?? null,
                        'archivo_size' => $doc->archivo_size_bytes ?? null,
                        'archivo_mime' => $doc->archivo_mime_type ?? null,
                        'cambio' => $doc->getDirty(),
                        'created_at' => now()->toISOString(),
                    ];
                    $versions[] = $newVersion;
                    if (count($versions) > 50) {
                        $versions = array_slice($versions, -50);
                    }
                    $doc->versiones = $versions;
                    $doc->saveQuietly(['versiones']);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }

    // -- Relaciones --

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function modificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(AuditoriaInterna::class, 'auditoria_id');
    }

    public function noConformidad(): BelongsTo
    {
        return $this->belongsTo(NoConformidad::class, 'nc_id');
    }

    public function riesgo(): BelongsTo
    {
        return $this->belongsTo(Riesgo::class, 'riesgo_id');
    }

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class, 'capacitacion_id');
    }

    public function mejora(): BelongsTo
    {
        return $this->belongsTo(MejoraContinua::class, 'mejora_id');
    }

    public function revisionDireccion(): BelongsTo
    {
        return $this->belongsTo(RevisionDireccion::class, 'revision_direccion_id');
    }

    public function docProvistos(): HasMany
    {
        return $this->hasMany(DocumentoProveedor::class);
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

    public function scopeVigentes($query)
    {
        return $query->where('estado', 'vigente')
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')
                  ->orWhere('fecha_vencimiento', '>=', now()->toDateString());
            });
    }

    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeProximoRevision($query, int $dias = 30)
    {
        return $query->whereNotIn('estado', ['obsoleto', 'archivado'])
            ->where('fecha_revision', '>=', now()->toDateString())
            ->where('fecha_revision', '<=', now()->addDays($dias)->toDateString());
    }

    public function scopePendientesRevision($query)
    {
        return $query->where('fecha_revision', '<=', now()->toDateString())
            ->whereNotIn('estado', ['archivado']);
    }

    // -- Accessors --

    public function getProximaRevisionAttribute(): ?string
    {
        if (!$this->fecha_revision) return null;
        $diff = now()->diffInDays($this->fecha_revision, false);
        if ($diff < 0) return "Vencido hace " . abs($diff) . " días";
        if ($diff === 0) return "Es hoy";
        return "En " . $diff . " días";
    }

    public function getEstaVigenteAttribute(): bool
    {
        if ($this->estado !== 'vigente') return false;
        if ($this->fecha_vencimiento && $this->fecha_vencimiento < now()) return false;
        return true;
    }

    public function getUltimaVersionDataAttribute(): ?array
    {
        if (empty($this->versiones)) return null;
        return end($this->versiones);
    }

    public function getNumeroVersionesAttribute(): int
    {
        return count($this->versiones ?? []);
    }

    public function getArchivoUrlAttribute(): ?string
    {
        if (!$this->archivo_path) return null;
        return route('sgc.documentos.archivo.show', [$this, 'file' => 'main']);
    }

    // -- Helpers --

    public function auditLabel(): string
    {
        return "SGC {$this->codigo}: {$this->titulo}";
    }
}
