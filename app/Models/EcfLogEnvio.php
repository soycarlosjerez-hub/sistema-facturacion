<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class EcfLogEnvio extends Model
{
    use TenantScope;

    protected $table = 'ecf_log_envios';

    public $timestamps = false;

    protected $fillable = [
        'ecf_documento_id',
        'accion',
        'estado_resultado',
        'codigo_http',
        'request_payload',
        'response_payload',
        'mensaje',
        'duracion_ms',
        'created_at',
        'tenant_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'codigo_http' => 'integer',
        'duracion_ms' => 'integer',
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(EcfDocumento::class, 'ecf_documento_id');
    }
}
