<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class WaitlistEntry extends Model
{
    use Auditable;
    protected $table = 'waitlist_entries';

    protected $fillable = ['sucursal_id', 'cliente_nombre', 'cliente_telefono', 'personas', 'notas', 'estado', 'user_id'];

    protected $casts = ['personas' => 'integer'];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDeSucursal($query)
    {
        if ($sucursalId = session('sucursal_id')) {
            return $query->where('sucursal_id', $sucursalId);
        }
        return $query;
    }

    public function scopeEsperando($query)
    {
        return $query->where('estado', 'esperando');
    }
}
