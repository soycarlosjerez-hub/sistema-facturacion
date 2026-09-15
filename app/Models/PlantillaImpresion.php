<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PlantillaImpresion extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $table = 'plantilla_impresiones';

    protected $fillable = [
        'tenant_id',
        'codigo',
        'nombre',
        'modulo',
        'tipo_formato',
        'incluir_logo',
        'incluir_encabezado',
        'incluir_pie',
        'activo',
        'orden',
        'configuracion',
        'logo_path',
        'color_primario',
        'color_secundario',
        'formato_papel',
        'orientation',
        'mostrar_logo',
        'mostrar_encabezado',
        'mostrar_pie',
        'mostrar_datos_cliente',
        'mostrar_datos_fiscales',
        'mostrar_columna_cantidad',
        'mostrar_columna_precio',
        'mostrar_columna_subtotal',
        'mostrar_columna_itbis',
        'mostrar_garantias',
        'mostrar_columna_ncf',
        'mostrar_columna_cajero',
        'mostrar_columna_sucursal',
        'mostrar_pagos',
        'mostrar_notas',
        'encabezado_texto',
        'pie_pagina_texto',
        'orden_campos',
        'modificable',
        'es_default',
        'modulo_target',
        'tipo_doc_target',
    ];

    protected $casts = [
        'incluir_logo' => 'boolean',
        'incluir_encabezado' => 'boolean',
        'incluir_pie' => 'boolean',
        'activo' => 'boolean',
        'configuracion' => 'json',
        'mostrar_logo' => 'boolean',
        'mostrar_encabezado' => 'boolean',
        'mostrar_pie' => 'boolean',
        'mostrar_datos_cliente' => 'boolean',
        'mostrar_datos_fiscales' => 'boolean',
        'mostrar_columna_cantidad' => 'boolean',
        'mostrar_columna_precio' => 'boolean',
        'mostrar_columna_subtotal' => 'boolean',
        'mostrar_columna_itbis' => 'boolean',
        'mostrar_garantias' => 'boolean',
        'mostrar_columna_ncf' => 'boolean',
        'mostrar_columna_cajero' => 'boolean',
        'mostrar_columna_sucursal' => 'boolean',
        'mostrar_pagos' => 'boolean',
        'mostrar_notas' => 'boolean',
        'orden_campos' => 'json',
        'modificable' => 'boolean',
        'es_default' => 'boolean',
        'tipo_doc_target' => 'integer',
    ];

    public const FORMATOS_PAPEL = [
        'a4' => 'A4 (210 × 297 mm)',
        'letter' => 'Carta (216 × 279 mm)',
        'ticket_80' => 'Ticket 80mm',
        'ticket_58' => 'Ticket 58mm',
    ];

    public const ORIENTACIONES = [
        'portrait' => 'Vertical',
        'landscape' => 'Horizontal',
    ];

    public const MODULOS = [
        'ventas' => 'Ventas / Facturas',
        'historial_ventas' => 'Historial de Ventas',
        'compras' => 'Compras',
        'cotizaciones' => 'Cotizaciones',
        'devoluciones' => 'Devoluciones',
        'ordenes' => 'Órdenes',
        'conduces' => 'Conduces',
        'presupuestos' => 'Presupuestos',
    ];

    public const TIPOS_DOC = [
        1 => 'NCF',
        2 => 'e-CF',
        0 => 'Todos',
    ];

    public const CODIGOS_PREDETERMINADOS = [
        'ticket_venta' => [
            'nombre' => 'Ticket de Venta',
            'modulo' => 'ventas',
            'tipo_formato' => 'ticket',
        ],
        'ticket_cotizacion' => [
            'nombre' => 'Ticket de Cotizacion',
            'modulo' => 'cotizaciones',
            'tipo_formato' => 'ticket',
        ],
        'ticket_conduce' => [
            'nombre' => 'Ticket Conduce',
            'modulo' => 'conduces',
            'tipo_formato' => 'ticket',
        ],
        'pdf_compra' => [
            'nombre' => 'PDF de Compra',
            'modulo' => 'compras',
            'tipo_formato' => 'pdf',
        ],
        'ticket_devolucion' => [
            'nombre' => 'Ticket de Devolucion',
            'modulo' => 'devoluciones',
            'tipo_formato' => 'ticket',
        ],
        'pdf_factura_a4' => [
            'nombre' => 'Factura A4',
            'modulo' => 'ventas',
            'tipo_formato' => 'pdf',
        ],
        'ticket_venta_letter' => [
            'nombre' => 'Ticket Carta',
            'modulo' => 'ventas',
            'tipo_formato' => 'ticket',
        ],
    ];

    protected static function booted(): void
    {
        static::creating(function ($plantilla) {
            if (auth()->check() && ! $plantilla->tenant_id) {
                $plantilla->tenant_id = auth()->user()->business_instance_id;
            }
        });

        static::saving(function ($plantilla) {
            if ($plantilla->es_default && $plantilla->modulo) {
                static::where('modulo', $plantilla->modulo)
                    ->where('tenant_id', $plantilla->tenant_id)
                    ->where('id', '!=', $plantilla->id)
                    ->update(['es_default' => false]);
            }
        });
    }

    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'plantilla_sucursal')
            ->withPivot('es_default')
            ->withTimestamps();
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'template_id');
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'template_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_formato', $tipo);
    }

    public function scopeParaFormato($query, $formato)
    {
        return $query->where('formato_papel', $formato);
    }

    public function scopePredeterminadas($query)
    {
        return $query->where('es_default', true);
    }

    public function scopeConModuloTarget($query, $modulo)
    {
        return $query->where(function ($q) use ($modulo) {
            $q->whereNull('modulo_target')
                ->orWhere('modulo_target', $modulo);
        });
    }

    public function scopeParaSucursal($query, $sucursalId)
    {
        return $query->where(function ($q) use ($sucursalId) {
            $q->whereDoesntHave('sucursales')
                ->orWhereHas('sucursales', fn ($qs) => $qs->where('sucursales.id', $sucursalId));
        });
    }

    public function obtenerDefaultParaModulo($modulo)
    {
        return static::where('modulo', $modulo)
            ->where('tenant_id', $this->tenant_id)
            ->where('activo', true)
            ->where('es_default', true)
            ->first();
    }

    public function obtenerDefaultParaModuloYSucursal($modulo, $sucursalId)
    {
        return static::where(function ($q) use ($modulo, $sucursalId) {
            $q->where('modulo', $modulo)
                ->where(function ($q2) {
                    $q2->whereNull('modulo_target')
                        ->orWhere('modulo_target', $modulo);
                })
                ->where(function ($q3) use ($sucursalId) {
                    $q3->whereDoesntHave('sucursales')
                        ->orWhereHas('sucursales', fn ($qs) => $qs->where('sucursales.id', $sucursalId));
                });
        })
            ->where('tenant_id', $this->tenant_id)
            ->where('activo', true)
            ->orderByDesc('es_default')
            ->first();
    }

    public function duplicar(): PlantillaImpresion
    {
        return DB::transaction(function () {
            $duplicado = $this->replicate();
            $duplicado->nombre = 'Copia de '.$this->nombre;
            $duplicado->codigo = $this->codigo.'_copia_'.time();
            $duplicado->es_default = false;
            $duplicado->save();

            if ($this->sucursales->count() > 0) {
                $sucursalesIds = $this->sucursales->pluck('id')->toArray();
                $duplicado->sucursales()->sync($sucursalesIds);
            }

            return $duplicado;
        });
    }

    public function getFormatoDisplayAttribute(): string
    {
        if (in_array($this->formato_papel, self::FORMATOS_PAPEL)) {
            return self::FORMATOS_PAPEL[$this->formato_papel];
        }

        if ($this->tipo_formato === 'ticket') {
            return 'Ticket (80mm)';
        }

        return 'PDF (A4)';
    }

    public function loadDefaults(): array
    {
        return [
            'formato_papel' => $this->formato_papel ?? 'a4',
            'orientation' => $this->orientation ?? 'portrait',
            'color_primario' => $this->color_primario ?? '#1a1a2e',
            'color_secundario' => $this->color_secundario ?? '#16213e',
            'mostrar_logo' => $this->mostrar_logo ?? true,
            'mostrar_encabezado' => $this->mostrar_encabezado ?? true,
            'mostrar_pie' => $this->mostrar_pie ?? true,
            'mostrar_datos_cliente' => $this->mostrar_datos_cliente ?? true,
            'mostrar_datos_fiscales' => $this->mostrar_datos_fiscales ?? true,
            'mostrar_columna_cantidad' => $this->mostrar_columna_cantidad ?? true,
            'mostrar_columna_precio' => $this->mostrar_columna_precio ?? true,
            'mostrar_columna_subtotal' => $this->mostrar_columna_subtotal ?? true,
            'mostrar_columna_itbis' => $this->mostrar_columna_itbis ?? true,
            'mostrar_garantias' => $this->mostrar_garantias ?? true,
            'mostrar_columna_ncf' => $this->mostrar_columna_ncf ?? true,
            'mostrar_columna_cajero' => $this->mostrar_columna_cajero ?? true,
            'mostrar_columna_sucursal' => $this->mostrar_columna_sucursal ?? true,
            'mostrar_pagos' => $this->mostrar_pagos ?? true,
            'mostrar_notas' => $this->mostrar_notas ?? true,
            'logo_path' => $this->logo_path ?? null,
            'encabezado_texto' => $this->encabezado_texto ?? '',
            'pie_pagina_texto' => $this->pie_pagina_texto ?? '',
            'configuracion' => $this->configuracion ?? [],
        ];
    }
}
