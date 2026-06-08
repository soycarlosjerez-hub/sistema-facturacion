<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaImpresion extends Model
{
    protected $table = 'plantillas_impresion';

    protected $fillable = [
        'nombre',
        'codigo',
        'modulo',
        'tipo_formato',
        'incluir_logo',
        'incluir_encabezado',
        'incluir_pie',
        'encabezado_personalizado',
        'pie_personalizado',
        'configuracion',
        'activo',
    ];

    protected $casts = [
        'incluir_logo' => 'boolean',
        'incluir_encabezado' => 'boolean',
        'incluir_pie' => 'boolean',
        'activo' => 'boolean',
        'configuracion' => 'json',
    ];

    public const MODULOS = [
        'ventas' => 'Ventas',
        'cotizaciones' => 'Cotizaciones',
        'conduces' => 'Conduces',
        'ecf' => 'e-CF',
    ];

    public const FORMATOS = [
        'ticket' => 'Ticket Térmico',
        'pdf' => 'PDF',
        'html' => 'HTML',
    ];

    public const CODIGOS_PREDETERMINADOS = [
        'venta_ticket' => ['modulo' => 'ventas', 'tipo_formato' => 'ticket', 'nombre' => 'Ticket de Venta'],
        'venta_pdf' => ['modulo' => 'ventas', 'tipo_formato' => 'pdf', 'nombre' => 'Factura PDF'],
        'cotizacion_ticket' => ['modulo' => 'cotizaciones', 'tipo_formato' => 'ticket', 'nombre' => 'Ticket Cotización'],
        'cotizacion_pdf' => ['modulo' => 'cotizaciones', 'tipo_formato' => 'pdf', 'nombre' => 'Cotización PDF'],
        'conduce_ticket' => ['modulo' => 'conduces', 'tipo_formato' => 'ticket', 'nombre' => 'Ticket Conduce'],
        'conduce_pdf' => ['modulo' => 'conduces', 'tipo_formato' => 'pdf', 'nombre' => 'Conduce PDF'],
        'ecf_pdf' => ['modulo' => 'ecf', 'tipo_formato' => 'pdf', 'nombre' => 'e-CF PDF'],
    ];

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorModulo($query, string $modulo)
    {
        return $query->where('modulo', $modulo);
    }
}
