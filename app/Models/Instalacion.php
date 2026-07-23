<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Instalacion extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'instalaciones';

    public $tenantColumn = 'business_instance_id';

    protected $fillable = [
        'numero',
        'business_instance_id',
        'cliente_id',
        'sucursal_id',
        'instalador_id',
        'estado',
        'direccion_instalacion',
        'tipo_inmueble',
        'programada_para',
        'completada_en',
        'nota_interna',
        'total',
        'created_by',
    ];

    protected $casts = [
        'programada_para'   => 'datetime',
        'completada_en'     => 'datetime',
        'total'             => 'decimal:2',
    ];

    const ESTADOS = [
        'pendiente'   => 'Pendiente',
        'programada'  => 'Programada',
        'en_progreso' => 'En Progreso',
        'completada'  => 'Completada',
        'cancelada'   => 'Cancelada',
    ];

    const TIPOS_INMUEBLE = [
        'casa'       => 'Casa',
        'apartamento' => 'Apartamento',
        'local'      => 'Local Comercial',
        'industrial' => 'Industrial',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function instalador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instalador_id');
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'instalacion_productos')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'programada']);
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    public function calcularTotal(): void
    {
        $total = $this->productos()->sum(function ($producto) {
            return $producto->pivot->cantidad * $producto->pivot->precio_unitario;
        });
        $this->total = $total;
        $this->save();
    }

    public function generarNumero(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('INST-%s-%05d', $year, $count);
    }
}
