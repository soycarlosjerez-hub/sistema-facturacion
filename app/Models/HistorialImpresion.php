<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HistorialImpresion extends Model
{
    protected $table = 'historial_impresion';

    protected $fillable = [
        'tenant_id',
        'imprimible_type',
        'imprimible_id',
        'impresora_id',
        'user_id',
        'tipo_documento',
        'documento_numero',
        'formato',
        'copias',
        'papel_tamano',
        'exitoso',
        'error_mensaje',
        'tamanio_bytes',
    ];

    protected $casts = [
        'copias' => 'integer',
        'exitoso' => 'boolean',
        'tamanio_bytes' => 'integer',
    ];

    public function imprimible(): MorphTo
    {
        return $this->morphTo();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function impresora(): BelongsTo
    {
        return $this->belongsTo(Impresora::class);
    }

    public function scopeTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check() && auth()->user()->business_instance_id) {
                $model->tenant_id = auth()->user()->business_instance_id;
            }
        });
    }

    public function scopeExitosos($query)
    {
        return $query->where('exitoso', true);
    }

    public function scopeFallidos($query)
    {
        return $query->where('exitoso', false);
    }

    public function getTamanioHumanoAttribute(): string
    {
        if (!$this->tamanio_bytes) return '-';
        $bytes = $this->tamanio_bytes;
        $units = ['B', 'KB', 'MB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 2) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
