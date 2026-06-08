<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';
    protected $fillable = [
        'key', 'label', 'icon', 'categoria',
        'sidebar_route', 'sidebar_is_route', 'sidebar_exact_route', 'sidebar_url', 'sidebar_permission',
        'activo', 'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public static function allActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('activo', true)->orderBy('orden')->get();
    }

    public static function getByCategoria(): array
    {
        return static::where('activo', true)
            ->orderBy('orden')
            ->get()
            ->groupBy('categoria')
            ->toArray();
    }
}
