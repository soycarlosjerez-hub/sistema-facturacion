<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class RedConfig extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'redes_config';

    protected $fillable = [
        'cliente_id',
        'nombre_red',
        'direccion_red',
        'vlan_id',
        'ssid_wifi',
        'canal_wifi',
        'cobertura',
        'dhcp_activado',
        'dhcp_rango',
        'notas',
        'activo',
        'tenant_id',
    ];

    protected $casts = [
        'dhcp_activado' => 'boolean',
        'activo'        => 'boolean',
        'vlan_id'       => 'integer',
        'canal_wifi'    => 'integer',
        'direccion_red' => 'string',
        'dhcp_rango'    => 'array',
    ];

    protected $appends = ['activo_label', 'estado_dhcp_label'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeByCliente(Builder $query, int $clienteId): Builder
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeByVlan(Builder $query, int $vlanId): Builder
    {
        return $query->where('vlan_id', $vlanId);
    }

    public function scopeWithSsid(Builder $query, string $ssid): Builder
    {
        return $query->where('ssid_wifi', $ssid);
    }

    public function getActivoLabelAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function getEstadoDhcpLabelAttribute(): string
    {
        return $this->dhcp_activado ? 'Activado' : 'Desactivado';
    }

    public function getVlanLabelAttribute(): ?string
    {
        return $this->vlan_id ? "VLAN {$this->vlan_id}" : null;
    }
}
