<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessInstanceModule extends Model
{
    use Auditable, TenantScope;

    protected $fillable = [
        'business_instance_id',
        'modulo_key',
        'visible',
        'orden',
    ];

    public $tenantColumn = 'business_instance_id';

    protected $casts = [
        'visible' => 'boolean',
        'orden' => 'integer',
    ];

    public function businessInstance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class);
    }
}
