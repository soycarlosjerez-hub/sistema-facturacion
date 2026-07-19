<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientApiToken extends Model
{
    protected $table = 'client_api_tokens';

    protected $fillable = [
        'cliente_id',
        'name',
        'token',
        'abilities',
        'expires_at',
    ];

    protected $casts = [
        'abilities'    => 'json',
        'last_used_at'  => 'datetime',
        'expires_at'    => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tokenCan(string $ability): bool
    {
        $abilities = $this->abilities ?? ['*'];
        if (in_array('*', $abilities)) {
            return true;
        }
        return in_array($ability, $abilities);
    }
}
