# Plan: Envío de correos automáticos para reservaciones

## Objetivo
Enviar correos automáticos al cliente cuando su reservación cambie de estado:
- Al **crear** reservación → "Recibido, pendiente de confirmación"
- Al **confirmar** → "Reservación confirmada"
- Al **cancelar** → "Reservación cancelada"

## Arquitectura de configuración SMTP
- Settings leídos desde `system_settings` vía `SystemSetting::get()`
- Aplicados por `AppServiceProvider` en runtime (sin tocar `.env`)
- Contraseña encriptada con `Crypt::encryptString/decryptString`
- Claves: `mail_host`, `mail_username`, `mail_password`, `mail_port`, `mail_encryption`, `mail_from_address`, `mail_from_name`

## Archivos a CREAR (6)

### 1. `app/Mail/ReservacionRecibidaMail.php`
Enviado al crear reservación.
```php
class ReservacionRecibidaMail extends Mailable {
    public function __construct(public Reservacion $reservacion) {}
    public function envelope(): Envelope {
        return new Envelope(subject: 'Reservación recibida - Pendiente de confirmación');
    }
    public function content(): Content {
        return new Content(
            view: 'emails.reservacion-recibida',
            text: 'emails.reservacion-recibida-text',
            with: ['reservacion' => $this->reservacion]
        );
    }
}
```

### 2. `app/Mail/ReservacionConfirmadaMail.php`
Enviado al confirmar.
```php
class ReservacionConfirmadaMail extends Mailable {
    public function __construct(public Reservacion $reservacion) {}
    public function envelope(): Envelope {
        return new Envelope(subject: '✅ Tu reservación ha sido confirmada');
    }
    public function content(): Content {
        return new Content(
            view: 'emails.reservacion-confirmada',
            text: 'emails.reservacion-confirmada-text',
            with: ['reservacion' => $this->reservacion]
        );
    }
}
```

### 3. `app/Mail/ReservacionCanceladaMail.php`
Envaido al cancelar.
```php
class ReservacionCanceladaMail extends Mailable {
    public function __construct(public Reservacion $reservacion) {}
    public function envelope(): Envelope {
        return new Envelope(subject: '❌ Tu reservación ha sido cancelada');
    }
    public function content(): Content {
        return new Content(
            view: 'emails.reservacion-cancelada',
            text: 'emails.reservacion-cancelada-text',
            with: ['reservacion' => $this->reservacion]
        );
    }
}
```

## Archivos a CREAR (templates HTML + texto plano, 6)

### 4. `resources/views/emails/reservacion-recibida.blade.php`
Template HTML para "reservación recibida/pending".
- Header azul claro (#6c757d)
- Icono ⏳ o 🕐
- Contenido: "Hemos recibido tu reservación, pendiente de confirmación"
- Datos: mesa, fecha/hora, personas, notas
- Botón CTA opcional: "Ver detalles"

### 5. `resources/views/emails/reservacion-recibida-text.blade.php`
Versión plain text del template anterior.

### 6. `resources/views/emails/reservacion-confirmada.blade.php`
Template HTML para confirmación.
- Header verde (#198754)
- Icono ✅
- Contenido: "Tu reservación ha sido confirmada exitosamente"
- Datos destacados: mesa, fecha/hora, personas, notas
- Mensaje de agradecimiento

### 7. `resources/views/emails/reservacion-confirmada-text.blade.php`
Plain text versión confirmación.

### 8. `resources/views/emails/reservacion-cancelada.blade.php`
Template HTML para cancelación.
- Header rojo (#dc3545)
- Icono ❌
- Contenido: "Tu reservación ha sido cancelada"
- Datos de la reservación
- Posible mensaje: "Contactanos para reagendar"

### 9. `resources/views/emails/reservacion-cancelada-text.blade.php`
Plain text versión cancelación.

## Archivos a MODIFICAR (2)

### 10. `app/Http/Controllers/ReservacionController.php`

#### Modificación A: Método `store()` — enviar al crear
Después de `DB::commit()` y antes del `redirect()`:
```php
// Enviar correo de confirmación al cliente si tiene email
if ($data['cliente_email']) {
    $cc = SystemSetting::get('mail_from_address');
    Mail::to($data['cliente_email'])
        ->cc($cc)
        ->queue(new ReservacionRecibidaMail(Reservacion::latest()->first()));
}
```

Nota: Como la reservación ya fue creada con `Reservacion::create()`, pasamos el modelo directamente:
```php
$reservacion = Reservacion::create($data);
// ... actualizar mesa ...
DB::commit();

if ($data['cliente_email']) {
    $cc = SystemSetting::get('mail_from_address');
    Mail::to($data['cliente_email'])
        ->cc($cc)
        ->queue(new ReservacionRecibidaMail($reservacion));
}
```

#### Modificación B: Método `estado()` — enviar al confirmar/cancelar
Después de `$reservacion->update(['estado' => $request->estado])`:
```php
// Enviar correo según el nuevo estado
if ($reservacion->cliente_email) {
    $cc = SystemSetting::get('mail_from_address');
    
    if ($request->estado === 'confirmada') {
        Mail::to($reservacion->cliente_email)
            ->cc($cc)
            ->queue(new ReservacionConfirmadaMail($reservacion));
    } elseif ($request->estado === 'cancelada') {
        Mail::to($reservacion->cliente_email)
            ->cc($cc)
            ->queue(new ReservacionCanceladaMail($reservacion));
    }
}
```

## Detalles técnicos

### CC al restaurante
- Se lee de `SystemSetting::get('mail_from_address')` (email configurado en el sistema)
- Si no hay valor configurado, no se hace CC

### Queue
- Se usa `Mail::to()->queue()` para no bloquear la respuesta HTTP
- Requiere worker de queue corriendo (`php artisan queue:work`)
- Si no hay worker, Laravel fallback a envío sincrónico automáticamente

### Condición de email
- Solo se envía si `$reservacion->cliente_email` no es null/vacío
- Si el cliente no tiene email registrado, no se envía nada

### Formato de fecha en emails
- HTML: `d/m/Y H:i` (ej: 15/07/2026 19:30)
- Texto: misma formato

### Estilo visual
- Seguir patrón de `emails/cotizacion-enviada.blade.php`
- Contenedor centrado max-width 600px
- Header con gradiente de color según tipo (verde=confirmar, rojo=cancelar, gris=recibido)
- Tabla de información con borde lateral de color
- Footer institucional con copyright

## Orden de ejecución
1. Crear 3 Mailables (`ReservacionRecibidaMail`, `ReservacionConfirmadaMail`, `ReservacionCanceladaMail`)
2. Crear 6 templates de email (3 HTML + 3 text)
3. Modificar `ReservacionController@store()` — agregar envío de correo al crear
4. Modificar `ReservacionController@estado()` — agregar envío de correo al confirmar/cancelar
5. Probar con `php artisan tinker` enviando correo de prueba

## Dependencias
- `illuminate/support` (ya incluido en Laravel)
- `illuminate/mail` (ya incluido en Laravel)
- Worker de queue opcional (si no corre, Laravel envía sincrónico)
