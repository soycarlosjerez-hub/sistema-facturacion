<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessInstanceModule extends Model
{
    protected $fillable = [
        'business_instance_id',
        'modulo_key',
        'visible',
        'orden',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'orden' => 'integer',
    ];

    public function businessInstance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class);
    }
}
