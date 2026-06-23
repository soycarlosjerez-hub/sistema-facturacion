<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class NcfSequence extends Model
{
    use TenantScope;

    protected $fillable = [
        'nombre',
        'prefijo',
        'desde',
        'hasta',
        'actual',
        'fecha_vencimiento',
        'activo',
        'tenant_id',
    ];
}
