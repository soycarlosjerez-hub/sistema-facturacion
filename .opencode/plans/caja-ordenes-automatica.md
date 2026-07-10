# Crear caja "órdenes" automáticamente

## Objetivo
Crear una caja dedicada llamada "órdenes" que se cree automáticamente si no existe, y que maneje todas las órdenes de esa caja. Si la caja existe, se deben enviar todas las órdenes a esa caja.

## Problema Actual
Las órdenes dependen de una sesión de caja abierta del usuario para:
1. Crearse (`caja_id` y `sesion_caja_id` se obtienen de la sesión abierta del usuario)
2. Cobrarse (`OrdenPaymentService` exige sesión abierta → error 422 si no hay)

Si el usuario no abrió caja manualmente, las órdenes quedan sin `caja_id` y no se pueden cobrar.

## Solución
Crear una caja dedicada llamada "órdenes" que sea automática y permanente.

---

## Archivos a modificar

### 1. `app/Services/OrdenService.php`

#### Agregar importación:
```php
use App\Models\Caja;
```

#### Agregar método privado:
```php
private function obtenerCajaOrdenes(int $sucursalId, int $tenantId): Caja
{
    $caja = Caja::where('nombre', 'órdenes')
        ->where('sucursal_id', $sucursalId)
        ->where('tenant_id', $tenantId)
        ->first();

    if (!$caja) {
        $caja = Caja::create([
            'nombre'   => 'órdenes',
            'codigo'   => 'ORD',
            'sucursal_id' => $sucursalId,
            'tenant_id'    => $tenantId,
            'activo'   => true,
            'estado'   => 'cerrada',
        ]);
    }

    return $caja;
}
```

#### Modificar `createOrden()`:
Reemplazar la lógica que busca sesión del usuario por:
```php
// Eliminar estas líneas:
$sesion = SesionCaja::where('user_id', $user->id)
    ->where('estado', 'abierta')
    ->latest('fecha_apertura')
    ->first();

if ($sesion) {
    $data['caja_id'] = $sesion->caja_id;
    $data['sesion_caja_id'] = $sesion->id;
}

// Reemplazar por:
$caja = $this->obtenerCajaOrdenes($data['sucursal_id'], $data['tenant_id']);

$sesion = SesionCaja::where('caja_id', $caja->id)
    ->where('estado', 'abierta')
    ->latest('fecha_apertura')
    ->first();

if (!$sesion) {
    $sesion = SesionCaja::create([
        'tenant_id'      => $data['tenant_id'],
        'caja_id'        => $caja->id,
        'user_id'        => $user->id,
        'fecha_apertura' => now(),
        'monto_inicial'  => 0,
        'estado'         => 'abierta',
    ]);
    $caja->update(['estado' => 'abierta']);
}

$data['caja_id'] = $caja->id;
$data['sesion_caja_id'] = $sesion->id;
```

---

### 2. `app/Services/OrdenPaymentService.php`

#### Agregar importación:
```php
use App\Models\Caja;
```

#### Modificar `procesarPago()`:
Reemplazar la búsqueda de sesión del usuario por búsqueda de la caja "órdenes":

```php
// Eliminar estas líneas:
$sesion = SesionCaja::where('user_id', Auth::id())
    ->where('estado', 'abierta')
    ->latest('fecha_apertura')
    ->first();

if (!$sesion) {
    return ['error' => 'No tienes una sesión de caja abierta', 'code' => 422];
}

// Reemplazar por:
$caja = Caja::where('nombre', 'órdenes')
    ->where('activo', true)
    ->first();

if (!$caja) {
    $caja = Caja::create([
        'nombre'   => 'órdenes',
        'codigo'   => 'ORD',
        'sucursal_id' => Auth::user()->sucursal_id,
        'tenant_id'    => Auth::user()->business_instance_id,
        'activo'   => true,
        'estado'   => 'cerrada',
    ]);
}

$sesion = SesionCaja::where('caja_id', $caja->id)
    ->where('estado', 'abierta')
    ->latest('fecha_apertura')
    ->first();

if (!$sesion) {
    $sesion = SesionCaja::create([
        'tenant_id'      => Auth::user()->business_instance_id,
        'caja_id'        => $caja->id,
        'user_id'        => Auth::id(),
        'fecha_apertura' => now(),
        'monto_inicial'  => 0,
        'estado'         => 'abierta',
    ]);
    $caja->update(['estado' => 'abierta']);
}
```

---

## Flujo Resultante

| Acción | Antes | Después |
|--------|-------|---------|
| Crear orden | Necesita sesión abierta del usuario | Usa caja "órdenes" automáticamente |
| Cobrar orden | Error si no hay sesión abierta | Usa caja "órdenes" automáticamente |
| API | Mismo problema | Funciona igual (usa `OrdenService`) |
| Multi-sucursal | Sin caja dedicada | Cada sucursal tiene su propia caja "órdenes" |

## Puntos Clave

1. **Una caja "órdenes" por sucursal** — Se filtra por `sucursal_id` y `tenant_id`
2. **Session automática** — Si no hay sesión abierta, se crea al momento
3. **Compatible con API** — `Api\OrdenController` usa `OrdenService`, así que hereda la funcionalidad
4. **Compatible con web** — `OrdenPosController` también usa `OrdenService`
5. **Persistente** — La caja se crea una vez y persiste; no se recrea en cada orden
