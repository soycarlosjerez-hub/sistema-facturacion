<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'precio_mensual',
        'precio_implementacion',
        'precio_lanzamiento',
        'max_usuarios',
        'max_sucursales',
        'max_empresas',
        'max_almacenes',
        'max_productos',
        'max_clientes',
        'max_proveedores',
        'max_ventas_mensuales',
        'max_compras_mensuales',
        'max_gastos_mensuales',
        'max_cajas',
        'max_cotizaciones_mensuales',
        'max_conduces_mensuales',
        'max_devoluciones_mensuales',
        'max_ordenes_mensuales',
        'max_mesas',
        'features',
        'modulos',
        'activo',
        'recomendado',
        'orden',
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'precio_implementacion' => 'decimal:2',
        'precio_lanzamiento' => 'decimal:2',
        'max_usuarios' => 'integer',
        'max_sucursales' => 'integer',
        'max_empresas' => 'integer',
        'max_almacenes' => 'integer',
        'max_productos' => 'integer',
        'max_clientes' => 'integer',
        'max_proveedores' => 'integer',
        'max_ventas_mensuales' => 'integer',
        'max_compras_mensuales' => 'integer',
        'max_gastos_mensuales' => 'integer',
        'max_cajas' => 'integer',
        'max_cotizaciones_mensuales' => 'integer',
        'max_conduces_mensuales' => 'integer',
        'max_devoluciones_mensuales' => 'integer',
        'max_ordenes_mensuales' => 'integer',
        'max_mesas' => 'integer',
        'features' => 'array',
        'modulos' => 'array',
        'activo' => 'boolean',
        'recomendado' => 'boolean',
        'orden' => 'integer',
    ];

    public const CACHE_KEY = 'plans_all';

    public function businessInstances(): HasMany
    {
        return $this->hasMany(BusinessInstance::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoInstancia::class);
    }

    public function limiteUsuarios(): ?int
    {
        return $this->max_usuarios;
    }

    public function limiteSucursales(): ?int
    {
        return $this->max_sucursales;
    }

    public function limiteEmpresas(): ?int
    {
        return $this->max_empresas;
    }

    /**
     * Módulos permitidos por el plan. null o [] = sin restricción (todos).
     */
    public function modulosPermitidos(): array
    {
        $modulos = $this->modulos ?? [];

        return is_array($modulos) ? $modulos : [];
    }

    public function permiteModulo(string $moduloKey): bool
    {
        $permitidos = $this->modulosPermitidos();

        if ($permitidos === []) {
            return true;
        }

        return in_array($moduloKey, $permitidos, true);
    }

    public function costoImplementacionEfectivo(): float
    {
        return (float) ($this->precio_lanzamiento ?? $this->precio_implementacion ?? 0);
    }

    /**
     * Obtiene todos los límites del plan como array
     */
    public function getLimites(): array
    {
        return [
            'usuarios' => $this->max_usuarios,
            'sucursales' => $this->max_sucursales,
            'empresas' => $this->max_empresas,
            'almacenes' => $this->max_almacenes,
            'productos' => $this->max_productos,
            'clientes' => $this->max_clientes,
            'proveedores' => $this->max_proveedores,
            'ventas_mensuales' => $this->max_ventas_mensuales,
            'compras_mensuales' => $this->max_compras_mensuales,
            'gastos_mensuales' => $this->max_gastos_mensuales,
            'cajas' => $this->max_cajas,
            'cotizaciones_mensuales' => $this->max_cotizaciones_mensuales,
            'conduces_mensuales' => $this->max_conduces_mensuales,
            'devoluciones_mensuales' => $this->max_devoluciones_mensuales,
            'ordenes_mensuales' => $this->max_ordenes_mensuales,
            'mesas' => $this->max_mesas,
        ];
    }

    /**
     * Verifica si un límite es ilimitado (null)
     */
    public function esIlimitado(string $limite): bool
    {
        $limites = $this->getLimites();
        return $limites[$limite] ?? true;
    }

    /**
     * Obtiene el límite formateado para mostrar (número o 'Ilimitado')
     */
    public function getLimiteFormateado(string $limite): string
    {
        $valor = $this->getLimites()[$limite] ?? null;
        return $valor === null ? 'Ilimitado' : (string) $valor;
    }

    public static function active(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('activo', true)->orderBy('orden')->get();
    }

    public static function defaultPlan(): ?self
    {
        return static::where('slug', 'profesional')->first();
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
