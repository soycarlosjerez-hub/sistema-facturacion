<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentProcessor extends Model
{
    protected $fillable = [
        'nombre', 'tipo', 'comision_porcentaje', 'comision_fija',
        'api_key', 'api_secret', 'api_endpoint', 'api_environment',
        'config_json', 'activo',
    ];

    protected $casts = [
        'comision_porcentaje' => 'decimal:2',
        'comision_fija'       => 'decimal:2',
        'activo'              => 'boolean',
        'api_environment'     => 'string',
    ];

    public function setApiSecretAttribute($value)
    {
        $this->attributes['api_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiSecretAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDeEntorno($query, $env)
    {
        return $query->where('api_environment', $env);
    }
}
