<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class TattooArtist extends Model
{
    use Auditable, TenantScope;

    protected $fillable = [
        'tenant_id', 'user_id', 'nombre_completo', 'especialidad',
        'foto_perfil', 'experiencia_anos', 'telefono', 'whatsapp',
        'instagram', 'comision_pct', 'biografia', 'activo', 'tipo', 'notas',
    ];

    protected $casts = [
        'experiencia_anos' => 'integer',
        'comision_pct'     => 'decimal:2',
        'activo'           => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(TattooAppointment::class, 'artista_id');
    }

    public function designs()
    {
        return $this->hasMany(TattooDesign::class, 'artist_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getGananciaTotalAttribute(): float
    {
        return (float) $this->appointments()
            ->where('estado', 'completada')
            ->get()
            ->sum(fn($a) => $a->total_final * ($this->comision_pct / 100));
    }

    public function getCitasCompletadasAttribute(): int
    {
        return $this->appointments()->where('estado', 'completada')->count();
    }
}
