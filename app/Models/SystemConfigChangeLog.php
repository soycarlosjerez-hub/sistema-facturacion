<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemConfigChangeLog extends Model
{
    protected $table = 'system_config_change_logs';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'action',
        'group',
        'variable_name',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_value' => 'string',
        'new_value' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}