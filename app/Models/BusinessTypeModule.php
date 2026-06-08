<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessTypeModule extends Model
{
    protected $table = 'business_type_modules';
    protected $fillable = ['business_type_id', 'modulo_key', 'visible', 'orden'];

    protected $casts = [
        'visible' => 'boolean',
        'orden' => 'integer',
    ];

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }
}