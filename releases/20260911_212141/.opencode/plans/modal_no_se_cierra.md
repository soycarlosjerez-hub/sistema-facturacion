# Fix: Modal no se cierra después de autorizar

## Problema

Cuando el usuario ingresa la clave del admin y hace clic en "Autorizar", el modal se queda abierto.

Causa: `openModalAutorizarAdmin()` crea la instancia de Bootstrap Modal pero no la guarda. Luego `enviarAutorizacionAdmin()` intenta usar `bootstrap.Modal.getInstance($('modalAutorizarAdmin'))?.hide()` pero no encuentra la instancia.

## Solución

Crear una variable global `let authModalInstance = null;` y asignar la instancia de Bootstrap Modal cuando se abre el modal. Luego en `enviarAutorizacionAdmin()` usar esa variable para cerrar el modal.

### Cambios en `create.blade.php`

1. Agregar variable global para la instancia del modal (junto a las otras variables de autorizacion):
```javascript
let authModalInstance = null;
```

2. En `openModalAutorizarAdmin()`, guardar la instancia:
```javascript
const modal = new bootstrap.Modal(modalEl);
authModalInstance = modal;
modal.show();
```

3. En `enviarAutorizacionAdmin()`, reemplazar:
```javascript
bootstrap.Modal.getInstance($('modalAutorizarAdmin'))?.hide();
```
por:
```javascript
if (authModalInstance) {
    authModalInstance.hide();
    authModalInstance = null;
}
```

## Archivo: `resources/views/ventas/create.blade.php`

- Línea ~3308: agregar `let authModalInstance = null;`
- Línea ~5165 (en `openModalAutorizarAdmin()`): agregar `authModalInstance = modal;`
- Línea ~5210 (en `enviarAutorizacionAdmin()`): reemplazar el cierre del modal
