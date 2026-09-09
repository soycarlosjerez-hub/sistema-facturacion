# Fix: Modal de autorización no se cierra

## Problema

Al autorizar el precio con la clave del admin, el modal no se cierra y deja la página en background bloqueada.

**Causa:** `openModalAutorizarAdmin()` crea la instancia de Bootstrap Modal pero no la guarda en ninguna variable global. Cuando `enviarAutorizacionAdmin()` intenta cerrarlo con `bootstrap.Modal.getInstance($('modalAutorizarAdmin'))?.hide()`, no puede encontrar la instancia porque se perdió al salir de la función.

---

## Cambios necesarios

### 1. `resources/views/ventas/create.blade.php`

#### A. Agregar variable global para la instancia del modal (línea ~3303)

**Buscar:**
```javascript
    let pendingSinItbis = null;
    // Autorización admin para modificar precios
```

**Reemplazar con:**
```javascript
    let pendingSinItbis = null;
    let authModalInstance = null;
    // Autorización admin para modificar precios
```

#### B. Guardar la instancia en `openModalAutorizarAdmin()` (línea ~5130)

**Buscar el bloque donde se crea y muestra el modal (~línea 5165):**
```javascript
            const modal = new bootstrap.Modal(modalEl);
            console.log('[MODAL] Bootstrap Modal instance created');
            modal.show();
            console.log('[MODAL] Modal.show() called');
```

**Reemplazar con:**
```javascript
            const modal = new bootstrap.Modal(modalEl);
            authModalInstance = modal;
            console.log('[MODAL] Bootstrap Modal instance created and stored');
            modal.show();
            console.log('[MODAL] Modal.show() called');
```

#### C. Usar la instancia guardada en `enviarAutorizacionAdmin()` (~línea 5210)

**Buscar:**
```javascript
                bootstrap.Modal.getInstance($('modalAutorizarAdmin'))?.hide();
```

**Reemplazar con:**
```javascript
                if (authModalInstance) {
                    authModalInstance.hide();
                    authModalInstance = null;
                }
```

---

## Verificación

Después de aplicar los cambios:
1. Click en precio → modal se abre con logs en consola
2. Ingresar email + contraseña de admin → se valida
3. Al hacer clic en "Autorizar" → `authModalInstance.hide()` cierra el modal correctamente
4. Se abre el input de edición del precio
5. Modificar precio → Enter para guardar → se actualiza y recalcula totales

## Archivos modificados
- `resources/views/ventas/create.blade.php` (3 cambios: variable global, guardar instancia, usar instancia)
