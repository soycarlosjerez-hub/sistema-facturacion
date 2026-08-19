<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tool_name',
        'tool_arguments',
        'tool_result',
    ];

    protected $casts = [
        'tool_arguments' => 'array',
        'tool_result' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }
}
