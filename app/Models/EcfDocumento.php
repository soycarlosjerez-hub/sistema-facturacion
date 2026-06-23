<?php

namespace App\Models;

use App\Models\Concerns\HasEcfStateMachine;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcfDocumento extends Model
{
    use HasEcfStateMachine;
    use TenantScope;

    protected $table = 'ecf_documentos';

    protected $fillable = [
        'venta_id',
        'secuencia_ecf_id',
        'certificado_digital_id',
        'encf',
        'tipo_ecf',
        'estado',
        'fecha_emision',
        'fecha_firma',
        'fecha_envio',
        'fecha_aprobacion',
        'fecha_anulacion',
        'monto_gravado_total',
        'monto_exento_total',
        'itbis_total',
        'monto_total',
        'xml_path',
        'xml_content',
        'firma_digital',
        'codigo_seguridad',
        'track_id_dgii',
        'mensaje_dgii',
        'intentos_envio',
        'motivo_anulacion',
        'anulado_por_encf',
        'nota_credito_id',
        'usuario_id',
        'documento_original_id',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_firma' => 'datetime',
        'fecha_envio' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_anulacion' => 'datetime',
        'monto_gravado_total' => 'decimal:2',
        'monto_exento_total' => 'decimal:2',
        'itbis_total' => 'decimal:2',
        'monto_total' => 'decimal:2',
        'intentos_envio' => 'integer',
    ];

    public const ESTADOS = [
        'borrador' => ['label' => 'Borrador', 'color' => 'secondary', 'icon' => 'bi-pencil'],
        'generado' => ['label' => 'Generado', 'color' => 'info', 'icon' => 'bi-file-earmark-check'],
        'firmado' => ['label' => 'Firmado', 'color' => 'primary', 'icon' => 'bi-pen'],
        'enviado' => ['label' => 'Enviado', 'color' => 'warning', 'icon' => 'bi-cloud-upload'],
        'aprobado' => ['label' => 'Aprobado', 'color' => 'success', 'icon' => 'bi-check-circle-fill'],
        'rechazado' => ['label' => 'Rechazado', 'color' => 'danger', 'icon' => 'bi-x-circle-fill'],
        'anulado' => ['label' => 'Anulado', 'color' => 'dark', 'icon' => 'bi-slash-circle'],
        'expirado' => ['label' => 'Expirado', 'color' => 'secondary', 'icon' => 'bi-clock-history'],
    ];

    public const TIPOS = [
        'E31' => 'Crédito Fiscal',
        'E32' => 'Consumo',
        'E33' => 'Nota de Débito',
        'E34' => 'Nota de Crédito',
        'E41' => 'Compras',
        'E43' => 'Gastos Menores',
        'E44' => 'Regímenes Especiales',
        'E45' => 'Gubernamentales',
        'E46' => 'Exportaciones',
        'E47' => 'Pagos al Exterior',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function secuencia(): BelongsTo
    {
        return $this->belongsTo(SecuenciaEcf::class, 'secuencia_ecf_id');
    }

    public function certificado(): BelongsTo
    {
        return $this->belongsTo(CertificadoDigital::class, 'certificado_digital_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EcfLogEnvio::class, 'ecf_documento_id')->orderBy('created_at', 'desc');
    }

    public function notaCredito(): BelongsTo
    {
        return $this->belongsTo(self::class, 'nota_credito_id');
    }

    public function documentoOriginal(): BelongsTo
    {
        return $this->belongsTo(self::class, 'documento_original_id');
    }

    public function getEstadoInfoAttribute(): array
    {
        return self::ESTADOS[$this->estado] ?? ['label' => $this->estado, 'color' => 'secondary', 'icon' => 'bi-question'];
    }

    public function getTipoNombreAttribute(): string
    {
        return self::TIPOS[$this->tipo_ecf] ?? $this->tipo_ecf;
    }

    public function aprobado(): bool
    {
        return $this->estado === 'aprobado';
    }

    public function pendienteEnvio(): bool
    {
        return in_array($this->estado, ['generado', 'firmado', 'rechazado'], true);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['generado', 'firmado', 'enviado']);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_ecf', $tipo);
    }

    public function scopeDelPeriodo($query, int $mes, int $anio)
    {
        return $query->whereYear('fecha_emision', $anio)
            ->whereMonth('fecha_emision', $mes);
    }
}
