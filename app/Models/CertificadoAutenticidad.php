<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class CertificadoAutenticidad extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'certificados_autenticidad';

    protected $fillable = [
        'obra_id',
        'numero_certificado',
        'qr_code',
        'firmado_en',
        'expirado',
        'tenant_id',
    ];

    protected $casts = [
        'firmado_en' => 'date',
        'expirado' => 'boolean',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function getQrCodeUrlAttribute(): string
    {
        if (empty($this->qr_code)) {
            return '';
        }
        if (str_starts_with($this->qr_code, 'http')) {
            return $this->qr_code;
        }
        return asset('storage/' . $this->qr_code);
    }

    public function scopeByObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    public function scopeByNumber($query, $number)
    {
        return $query->where('numero_certificado', $number);
    }
}
