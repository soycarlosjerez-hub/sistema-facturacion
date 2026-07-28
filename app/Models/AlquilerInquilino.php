<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class AlquilerInquilino extends Model
{
    use Auditable, TenantScope;

    protected $table = 'alquileres_inquilinos';

    protected $fillable = [
        'business_instance_id', 'nombre', 'cedula', 'telefono',
        'email', 'direccion', 'notas', 'activo',
    ];

    public $tenantColumn = 'business_instance_id';

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function businessInstance()
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public function contratos()
    {
        return $this->hasMany(AlquilerContrato::class, 'inquilino_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorInstancia($query, $instanceId)
    {
        return $query->where('business_instance_id', $instanceId);
    }
}
