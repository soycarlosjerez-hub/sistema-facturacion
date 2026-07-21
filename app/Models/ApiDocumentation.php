<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiDocumentation extends Model
{
    protected $fillable = [
        'module',
        'filename',
        'content',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('module');
    }
}
