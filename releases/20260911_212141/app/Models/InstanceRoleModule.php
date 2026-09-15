<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstanceRoleModule extends Model
{
    protected $fillable = [
        'instance_role_id', 'modulo_key', 'is_visible', 'orden',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'orden' => 'integer',
    ];

    public function instanceRole(): BelongsTo
    {
        return $this->belongsTo(InstanceRole::class);
    }
}
