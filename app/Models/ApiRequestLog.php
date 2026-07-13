<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    protected $fillable = [
        'business_instance_id',
        'user_id',
        'method',
        'uri',
        'query_string',
        'ip_address',
        'user_agent',
        'request_headers',
        'request_body',
        'response_status',
        'response_time_ms',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_status' => 'integer',
        'response_time_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function businessInstance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'business_instance_id');
    }
}
