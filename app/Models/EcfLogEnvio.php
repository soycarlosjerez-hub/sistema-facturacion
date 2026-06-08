<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcfLogEnvio extends Model
{
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
