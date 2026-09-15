<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class TattooPayment extends Model
{
    use Auditable;

    protected $fillable = [
        'appointment_id', 'monto', 'metodo_pago', 'referencia', 'tipo', 'user_id', 'notas',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function appointment()
    {
        return $this->belongsTo(TattooAppointment::class, 'appointment_id');
    }

    public function scopeDepositos($query)
    {
        return $query->where('tipo', 'deposito');
    }

    public function scopeSaldo($query)
    {
        return $query->where('tipo', 'saldo');
    }
}
