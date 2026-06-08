<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class CertificadoDigital extends Model
{
    protected $table = 'certificados_digitales';

    protected $fillable = [
        'nombre',
        'rnc_emisor',
        'rnc_titular',
        'archivo_path',
        'password_encrypted',
        'serial_number',
        'emisor_cert',
        'fecha_emision',
        'fecha_vencimiento',
        'activo',
        'notas',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
    ];

    protected $hidden = ['password_encrypted'];

    public function documentos(): HasMany
    {
        return $this->hasMany(EcfDocumento::class, 'certificado_digital_id');
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password_encrypted'] = Crypt::encryptString($value);
    }

    public function getPasswordAttribute(): ?string
    {
        $enc = $this->attributes['password_encrypted'] ?? null;
        if (!$enc) return null;
        try {
            return Crypt::decryptString($enc);
        } catch (\Throwable) {
            return null;
        }
    }

    public function vigente(): bool
    {
        return $this->activo && $this->fecha_vencimiento->isFuture();
    }

    public function diasParaVencer(): int
    {
        return (int) now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('activo', true)
            ->where('fecha_vencimiento', '>', now());
    }
}
