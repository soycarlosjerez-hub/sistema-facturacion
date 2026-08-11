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
