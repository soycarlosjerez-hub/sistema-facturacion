# Fix: Error 500 por Recursión Infinita en TenantScope

## Problema
- `TenantScope` está aplicado al modelo `User` (línea 17 de `app/Models/User.php`)
- Cuando el scope global se ejecuta, llama a `Auth::check()` y `Auth::user()` (línea 24 de `app/Traits/TenantScope.php`)
- `Auth::user()` internamente consulta la tabla `users` usando el modelo `User`
- Esto dispara el mismo scope global → `Auth::user()` → scope → `Auth::user()` → ... → **stack overflow**

## Solución
En `app/Traits/TenantScope.php`, agregar un chequeo para excluir el modelo `User` del scope:

```php
protected static function bootTenantScope(): void
{
    static::addGlobalScope('tenant', function (Builder $builder) {
        // Skip scope for User model to prevent infinite recursion
        if ($builder->getModel() instanceof User) {
            return;
        }
        
        if (Auth::check() && Auth::user()->business_instance_id !== null) {
            $model = $builder->getModel();
            $column = $model->getTenantIdColumn();
            $builder->where($model->getTable() . '.' . $column, Auth::user()->business_instance_id);
        }
    });
}
```

También agregar el import:
```php
use App\Models\User;
```

## Archivos a modificar
1. `app/Traits/TenantScope.php` — agregar `use App\Models\User;` y el chequeo `instanceof User`

## Pasos
1. Abrir `app/Traits/TenantScope.php`
2. Agregar `use App\Models\User;` después de `use Illuminate\Support\Facades\Auth;`
3. Dentro de `bootTenantScope()`, agregar el bloque `if ($builder->getModel() instanceof User) { return; }` justo después de `static::addGlobalScope('tenant', function (Builder $builder) {`
4. Limpiar cache de routes/modelos: `php artisan optimize:clear`
5. Probar la aplicación
