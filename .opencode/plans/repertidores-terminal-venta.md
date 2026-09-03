# Implementar Repartidores en Terminal de Venta

## Contexto
La terminal de venta tiene selector de delivery (empresa de delivery) pero NO tiene:
- Selector de repartidor específico
- Asignación manual del repartidor al momento de la venta
- Seeder para datos de prueba
- Asociación entre venta y repartidor en la BD

## Estado Actual
- `Venta` model tiene `driver_id` en `fillable` pero NO tiene la relación `driver()`
- `Venta` tiene `delivery_fee` y `delivery_company_id` en la BD
- La tabla `ventas` tiene columna `driver_id` (migración `2026_08_04_130006`)
- La tabla `delivery_drivers` existe con columna `tenant_id`
- POS no envía `driver_id` al backend
- No existen repartidores de prueba

## Plan de Implementación

### 1. Backend - Modelo Venta
**Archivo**: `app/Models/Venta.php`

**Cambios**:
```php
// Agregar al final del archivo (línea 128):
public function driver(): BelongsTo
{
    return $this->belongsTo(DeliveryDriver::class);
}
```

### 2. Backend - SaleCreateService
**Archivo**: `app/Services/SaleCreateService.php`

**Cambios**:

A. En `getCreationData()`, agregar repartidores a los datos:
- Después de línea 699 (`$deliveryCompanies = ...`), agregar:
  ```php
  $deliveryDrivers = \App\Models\DeliveryDriver::where('tenant_id', $tenantId)
      ->where('activo', true)
      ->orderBy('nombre')
      ->get();
  ```
- En el return array (línea ~708), agregar:
  ```php
  'deliveryDrivers' => $deliveryDrivers,
  ```

B. En `procesarVenta()`, agregar `driver_id` al create de Venta:
- Después de `'delivery_fee' => $data['delivery_fee'] ?? 0,` (línea 486), agregar:
  ```php
  'driver_id' => $data['driver_id'] ?? null,
  ```

C. Agregar relación con DeliveryTracking después de guardar la venta:
- Después de `$this->procesarPago()`, agregar lógica para crear tracking si hay driver:
  ```php
  if ($venta->driver_id && $venta->delivery_company_id) {
      \App\Models\DeliveryTracking::create([
          'tenant_id' => $venta->tenant_id,
          'orden_id'  => $venta->id,
          'driver_id' => $venta->driver_id,
          'status'    => \App\Models\DeliveryTracking::STATUS_CREADO,
          'creado_por' => Auth::id(),
      ]);
  }
  ```

### 3. Frontend - create.blade.php (Terminal de Venta)

**A. Selector de Repartidor (línea 2314, después del select de empresa de delivery)**:
```blade
<!-- Delivery driver selector (hidden by default) -->
<select id="delivery-driver-select" class="form-select form-select-sm d-inline-block w-auto" style="background:var(--pos-card);border:2px solid #f59e0b;color:var(--pos-text);font-size:0.78rem;padding:4px 10px;border-radius:8px;max-width:220px;display:none;font-weight:700;" title="Repartidor asignado">
    <option value="">Seleccionar repartidor...</option>
    @if(isset($deliveryDrivers) && count($deliveryDrivers) > 0)
        @foreach($deliveryDrivers as $driver)
            <option value="{{ $driver->id }}">
                {{ $driver->nombre }} {{ $driver->apellido }}
                @if($driver->telefono) - {{ $driver->telefono }}@endif
            </option>
        @endforeach
    @else
        <option value="" disabled>No hay repartidores disponibles</option>
    @endif
</select>
```

**B. Campo hidden para driver_id**:
- Después de `<input type="hidden" name="delivery_fee" id="delivery-fee-field" value="0">`, agregar:
  ```html
  <input type="hidden" name="driver_id" id="driver-id-field" value="">
  ```

**C. JavaScript - Agregar `deliveryDrivers` data**:
- Al inicio del `<script>`, agregar después de las variables de productos/clientes:
  ```javascript
  const deliveryDrivers = {!! json_encode($deliveryDrivers ?? collect([])) !!};
  ```

**D. JavaScript - Función `toggleDriver()`**:
- Agregar al objeto POS (después de `toggleDelivery()`):
  ```javascript
  toggleDriver(driverId) {
      const driverSelect = $('delivery-driver-select');
      if (driverId) {
          driverSelect.style.display = 'block';
      } else {
          driverSelect.style.display = 'none';
          $('driver-id-field').value = '';
      }
  },
  ```

**E. JavaScript - Submit con driver_id**:
- En la función `procesarPago()` (línea ~3600), después de agregar delivery fields, agregar:
  ```javascript
  // Add driver_id if selected
  formData.set('driver_id', $('driver-id-field').value);
  ```

**F. JavaScript - Seleccionar repartidor desde select**:
- Agregar event listener al select de repartidor (al final del script):
  ```javascript
  $('delivery-driver-select').addEventListener('change', function() {
      $('driver-id-field').value = this.value;
  });
  ```

### 4. Seeder para Repartidores de Prueba
**Archivo**: `database/seeders/DeliveryDriversSeeder.php`

**Contenido**:
```php
<?php

namespace Database\Seeders;

use App\Models\DeliveryDriver;
use Illuminate\Database\Seeder;

class DeliveryDriversSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = \App\Models\BusinessInstance::whereNotNull('id')->take(3)->get();
        
        $drivers = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'cedula' => '401-0000000-1',
                'telefono' => '+1 (829) 555-0101',
                'whatsapp' => '+1 (829) 555-0101',
                'licencia_conducir' => 'A-12345678',
                'activo' => true,
            ],
            [
                'nombre' => 'María',
                'apellido' => 'Díaz',
                'cedula' => '001-0000000-2',
                'telefono' => '+1 (809) 555-0202',
                'whatsapp' => '+1 (809) 555-0202',
                'licencia_conducir' => 'B-87654321',
                'activo' => true,
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Ramírez',
                'cedula' => '001-0000000-3',
                'telefono' => '+1 (829) 555-0303',
                'whatsapp' => '+1 (829) 555-0303',
                'licencia_conducir' => 'A-11223344',
                'activo' => true,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'cedula' => '001-0000000-4',
                'telefono' => '+1 (809) 555-0404',
                'whatsapp' => '+1 (809) 555-0404',
                'licencia_conducir' => 'C-55667788',
                'activo' => true,
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Santos',
                'cedula' => '001-0000000-5',
                'telefono' => '+1 (829) 555-0505',
                'whatsapp' => '+1 (829) 555-0505',
                'licencia_conducir' => 'A-99887766',
                'activo' => true,
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Gómez',
                'cedula' => '001-0000000-6',
                'telefono' => '+1 (809) 555-0606',
                'whatsapp' => '+1 (809) 555-0606',
                'licencia_conducir' => 'B-22334455',
                'activo' => true,
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($drivers as $driverData) {
                $driverData['tenant_id'] = $tenant->id;
                DeliveryDriver::create($driverData);
            }
        }
    }
}
```

### 5. Ejecución del Seeder
```bash
php artisan db:seed --class=DeliveryDriversSeeder
```

## Resumen de Archivos a Modificar
1. `app/Models/Venta.php` - Agregar relación `driver()`
2. `app/Services/SaleCreateService.php` - Cargar drivers + asignar al crear venta + crear tracking
3. `resources/views/ventas/create.blade.php` - Selector de repartidor + JS
4. `database/seeders/DeliveryDriversSeeder.php` - Nuevo archivo para datos de prueba

## Flujo de Usuario
1. Vendedor selecciona "🛵 Delivery" en el topbar
2. Se muestran: empresa de delivery + repartidor
3. Vendedor selecciona empresa y repartidor
4. Al cobrar, se guarda la venta con `driver_id`
5. Se crea automáticamente `DeliveryTracking` con estado `creado`
6. El repartidor queda asignado a esa venta
