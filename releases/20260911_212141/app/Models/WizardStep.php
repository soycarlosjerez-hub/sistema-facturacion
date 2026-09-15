<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WizardStep extends Model
{
    protected $table = 'wizard_steps';

    protected $fillable = [
        'key', 'module_key', 'label', 'icon',
        'required', 'skipable', 'entity_class', 'orden',
    ];

    protected $casts = [
        'required' => 'boolean',
        'skipable' => 'boolean',
        'orden' => 'integer',
    ];
}
