<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservacion extends Model
{
    protected $table = 'reservaciones';

    protected $fillable = [
        'mesa_id', 'cliente_id', 'cliente_nombre', 'cliente_telefono',
        'cliente_email', 'personas', 'fecha_hora', 'notas', 'estado', 'user_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'personas'   => 'integer',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDeSucursal($query)
    {
        if ($sucursalId = session('sucursal_id')) {
            return $query->whereHas('mesa', fn($q) => $q->where('sucursal_id', $sucursalId));
        }
        return $query;
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('fecha_hora', '>=', now()->subHours(2));
    }
}
