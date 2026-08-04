# Plan: Activar/Desactivar Notificaciones en Configuración de Instancia

## Problema
La vista `owner/instances/config.blade.php` no tiene opciones para activar/desactivar notificaciones. Existe un modelo `NotificationPreference` a nivel de usuario, pero sin interfaz para configurarlo a nivel de instancia.

## Solución
Agregar toggles generales de notificaciones a nivel de instancia (globales para toda la empresa).

## Cambios Requeridos

### 1. Migración: `2026_08_04_002700_create_instance_notification_settings_table.php`
**Archivo:** `database/migrations/2026_08_04_002700_create_instance_notification_settings_table.php`

Ya se creó el archivo base. Hay que agregar las columnas:
- `business_instance_id` (FK a business_instances, unique)
- `enabled` (boolean, default true) — toggle maestro
- Booleanos por tipo: `sale_created`, `sale_paid`, `sale_cancelled`, `order_confirmed`, `order_ready`, `order_shipped`, `payment_received`, `credit_overdue`, `credit_abono`, `stock_critical`, `stock_restocked`, `product_created`, `shift_opened`, `shift_closed`, `cash_shortage`, `daily_report`, `ncff_expiring`, `ecf_certificate_expiring`, `backup_completed`, `backup_failed`, `user_registered`
- `timestamps()`

### 2. Modelo: `app/Models/InstanceNotificationSetting.php`
Nuevo modelo con:
- `$fillable` con todos los campos booleanos
- `$casts` con todos los campos a `boolean`
- Relación `businessInstance()` → `belongsTo(BusinessInstance::class)`
- Método estático `forInstance(BusinessInstance $instance)` → `firstOrCreate` con defaults
- Método estático `defaultSettings()` → array con valores por defecto
- Método `isEnabled(string $key)` → retorna `$this->$key ?? false`

### 3. Controlador: `app/Http/Controllers/OwnerController.php`

**Método `instancesConfig()`** (~línea 481):
- Agregar carga de `InstanceNotificationSetting::firstOrNew($instance)`
- Pasar `$instanceNotifSettings` a la vista

**Método `instancesConfigUpdate()`** (~línea 498):
- Validar campos de notificaciones: `enabled`, `sale_created`, `sale_paid`, etc. (todos los booleanos)
- Buscar o crear `InstanceNotificationSetting` para la instancia
- Actualizar con los datos validados

### 4. Vista: `resources/views/owner/instances/config.blade.php`

Agregar después del "Módulo Restaurante" (después de línea 159, antes del `<hr>` de línea 161):

```blade
<hr>
<h6 class="fw-bold text-muted mb-3"><i class="bi bi-bell me-2"></i>Notificaciones</h6>

<div class="p-3 rounded-4 border mb-3" style="background: rgba(139,92,246,0.04); border-color: rgba(139,92,246,0.2) !important;">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <label class="ui-label fw-bold small mb-0">
                <i class="bi bi-toggle2-off me-1"></i>Activar Notificaciones
            </label>
            <small class="text-muted d-block" style="font-size:.72rem;">
                Desactivar envía todas las notificaciones de esta instancia.
            </small>
        </div>
        <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" name="enabled" value="1"
                   {{ ($instanceNotifSettings->enabled ?? true) ? 'checked' : '' }}>
        </div>
    </div>
</div>

<!-- Categorías de notificaciones -->
<div class="accordion" id="notifAccordion">
    <!-- Ventas y Órdenes -->
    <div class="accordion-item mb-2 rounded-4 overflow-hidden border" style="border-color: rgba(59,130,246,0.3) !important;">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#notifVentas">
                <i class="bi bi-receipt me-2 text-primary"></i>Ventas y Órdenes
            </button>
        </h2>
        <div id="notifVentas" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @foreach([
                    'sale_created' => 'Venta creada',
                    'sale_paid' => 'Venta pagada',
                    'sale_cancelled' => 'Venta cancelada',
                    'order_confirmed' => 'Orden confirmada',
                    'order_ready' => 'Orden lista',
                    'order_shipped' => 'Orden enviada',
                ] as $key => $label)
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <small>{{ $label }}</small>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                   {{ ($instanceNotifSettings->$key ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Cobros -->
    <div class="accordion-item mb-2 rounded-4 overflow-hidden border" style="border-color: rgba(16,185,129,0.3) !important;">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#notifCobros">
                <i class="bi bi-credit-card me-2 text-success"></i>Cobros y Pagos
            </button>
        </h2>
        <div id="notifCobros" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @foreach([
                    'payment_received' => 'Pago recibido',
                    'credit_overdue' => 'Crédito vencido',
                    'credit_abono' => 'Abono crédito',
                ] as $key => $label)
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <small>{{ $label }}</small>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                   {{ ($instanceNotifSettings->$key ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Inventario -->
    <div class="accordion-item mb-2 rounded-4 overflow-hidden border" style="border-color: rgba(245,158,11,0.3) !important;">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#notifInventario">
                <i class="bi bi-box-seam me-2 text-warning"></i>Inventario
            </button>
        </h2>
        <div id="notifInventario" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @foreach([
                    'stock_critical' => 'Stock crítico',
                    'stock_restocked' => 'Producto reabastecido',
                    'product_created' => 'Producto creado',
                ] as $key => $label)
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <small>{{ $label }}</small>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                   {{ ($instanceNotifSettings->$key ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Caja y Turnos -->
    <div class="accordion-item mb-2 rounded-4 overflow-hidden border" style="border-color: rgba(239,68,68,0.3) !important;">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#notifCaja">
                <i class="bi bi-cash-stack me-2 text-danger"></i>Caja y Turnos
            </button>
        </h2>
        <div id="notifCaja" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @foreach([
                    'shift_opened' => 'Turno abierto',
                    'shift_closed' => 'Turno cerrado',
                    'cash_shortage' => 'Diferencia en caja',
                ] as $key => $label)
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <small>{{ $label }}</small>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                   {{ ($instanceNotifSettings->$key ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Reportes -->
    <div class="accordion-item mb-2 rounded-4 overflow-hidden border" style="border-color: rgba(139,92,246,0.3) !important;">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#notifReportes">
                <i class="bi bi-file-earmark-bar-graph me-2" style="color:#8b5cf6;"></i>Reportes
            </button>
        </h2>
        <div id="notifReportes" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                <div class="d-flex align-items-center justify-content-between py-2">
                    <small>Reporte diario</small>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="daily_report" value="1"
                               {{ ($instanceNotifSettings->daily_report ?? false) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fiscal -->
    <div class="accordion-item mb-2 rounded-4 overflow-hidden border" style="border-color: rgba(6,182,212,0.3) !important;">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#notifFiscal">
                <i class="bi bi-shield-check me-2 text-info"></i>Fiscal
            </button>
        </h2>
        <div id="notifFiscal" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @foreach([
                    'ncff_expiring' => 'NCF por vencer',
                    'ecf_certificate_expiring' => 'Certificado ECF por vencer',
                ] as $key => $label)
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <small>{{ $label }}</small>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                   {{ ($instanceNotifSettings->$key ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Sistema -->
    <div class="accordion-item mb-2 rounded-4 overflow-hidden border" style="border-color: rgba(107,114,128,0.3) !important;">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#notifSistema">
                <i class="bi bi-gear me-2 text-secondary"></i>Sistema
            </button>
        </h2>
        <div id="notifSistema" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @foreach([
                    'backup_completed' => 'Backup completado',
                    'backup_failed' => 'Backup fallido',
                    'user_registered' => 'Usuario registrado',
                ] as $key => $label)
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <small>{{ $label }}</small>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                   {{ ($instanceNotifSettings->$key ?? false) ? 'checked' : '' }}>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
```

### 5. NotificationService: `app/Services/NotificationService.php`
En el método `send()`, verificar antes de enviar:
```php
// Verificar configuración de instancia
$instance = $user->businessInstance;
if ($instance) {
    $settings = \App\Models\InstanceNotificationSetting::firstOrNew(['business_instance_id' => $instance->id]);
    if (!$settings->enabled) {
        return null; // Notificación desactivada para esta instancia
    }
    if (!$settings->isEnabled($category)) {
        return null; // Tipo específico desactivado
    }
}
```

## Orden de Ejecución
1. Migración
2. Modelo
3. Controlador (actualizar métodos existentes)
4. Vista
5. NotificationService

## Notas
- Las notificaciones son **globales para la instancia**, no por usuario
- El toggle maestro `enabled` desactiva TODAS las notificaciones de la instancia
- Cada tipo individual puede activarse/desactivarse independientemente
- Los valores por defecto coinciden con `NotificationPreference::defaultPreferences()`
