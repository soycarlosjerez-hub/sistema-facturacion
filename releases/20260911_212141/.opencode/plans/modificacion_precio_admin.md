# Plan: Modificación de Precio con Autorización Admin en POS

## Contexto
El sistema ya tiene un modal de autorización admin (`modalAutorizarAdmin`) para quitar ITBIS de líneas. Vamos a reusar ese mismo flujo para permitir que no-admins modifiquen precios con autorización de admin.

## Flujo de usuario
1. Usuario agrega producto al carrito del POS
2. Usuario hace click en el precio del producto (muestra ✏️)
3. Si el usuario actual NO tiene rol admin → se abre modal pidiendo email y contraseña de admin
4. Si el usuario YA es admin → se abre input editable directamente
5. Al autorizar, el token es válido por 5 min para TODOS los items del carrito
6. Al enviar la venta, el backend valida el token y acepta los precios modificados

---

## 1. Backend: `app/Http/Controllers/VentaController.php`

### Modificar `autorizarAdmin()` (línea ~129)

**Agregar validación de `context`:**
```php
$request->validate([
    'email' => 'required|email',
    'password' => 'required|string',
    'context' => 'sometimes|in:sinitbis,precio',
]);

$rolesAdmin = ['admin', 'admin-business', 'root', 'gerente'];
$context = $request->input('context', 'sinitbis'); // <-- NUEVO
```

**En el bloque de éxito, agregar `context` al token:**
```php
$token = Crypt::encryptString(json_encode([
    'email' => $user->email,
    'tenant_id' => Auth::user()->business_instance_id,
    'context' => $context,  // <-- NUEVO
    'exp' => $expira->timestamp,
]));
```

**Cambiar log message:**
```php
$contextLabels = [
    'sinitbis' => 'quitar ITBIS',
    'precio' => 'modificar precio',
];
Log::info("Autorización admin emitida para " . ($contextLabels[$context] ?? $context), [...]);
```

**En el response, agregar context:**
```php
return response()->json([
    'success' => true,
    'token' => $token,
    'admin' => $user->name,
    'expira' => $expira->toDateTimeString(),
    'context' => $context,  // <-- NUEVO
]);
```

---

## 2. Backend: `app/Services/SaleService.php`

### Modificar `createSale()` (línea ~74)

**Hacer `$puedeSobreescribirPrecio` dependiente del admin_token:**
```php
$rolesAutorizados = ['admin', 'admin-business', 'root', 'gerente'];
$puedeSobreescribirPrecio = in_array(auth()->user()->role, $rolesAutorizados)
    || auth()->user()->hasRole($rolesAutorizados);

// Si el usuario envía un token admin válido, también puede sobreescribir precio
if ($puedeSobreescribirPrecio) {
    // Ya puede, no necesita token
} else {
    // No es admin, pero si tiene token válido puede modificar precio
    // Esto se verifica al validar el token después
}
```

**Modifica `verificarTokenAdmin()` (línea ~1048) para que retorne si fue para precio:**

Actualmente la función es `void`. Cambiarla para que retorne `true` si el contexto es `precio` o si es `sinitbis` (ambos deberían habilitar la modificación de precio):

```php
private function verificarTokenAdmin(?string $token): bool
{
    if (! $token) {
        return false;
    }

    try {
        $data = json_decode(Crypt::decryptString($token), true);
        if (! $data || ! isset($data['exp'])) {
            return false;
        }

        if (time() > $data['exp']) {
            Log::warning('Token admin expirado', ['context' => $data['context'] ?? 'sinitbis']);
            return false;
        }

        if ((int) $data['tenant_id'] !== Auth::user()->business_instance_id) {
            Log::warning('Token admin: tenant mismatch', ['token_tenant' => $data['tenant_id']]);
            return false;
        }

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            Log::warning('Token admin: usuario ya no existe', ['email' => $data['email']]);
            return false;
        }

        // Verificar que aún tiene rol admin
        if (! $user->hasAnyRole(['admin', 'admin-business', 'root', 'gerente'])) {
            Log::warning('Token admin: usuario ya no tiene rol admin');
            return false;
        }

        Log::info('Token admin válido verificado', [
            'email' => $data['email'],
            'context' => $data['context'] ?? 'sinitbis',
        ]);

        return true; // <-- CAMBIO: retorna true en vez de void
    } catch (\Exception $e) {
        Log::warning('Token admin inválido', ['error' => $e->getMessage()]);
        return false;
    }
}
```

**En `createSale()`, después de `verificarTokenAdmin()`, habilitar `puedeSobreescribirPrecio`:**

```php
// Verificar token admin (ITBIS y/o precio)
$tokenValido = $this->verificarTokenAdmin($data['admin_token'] ?? null);

// Si el token es válido, habilitar modificación de precio
if ($tokenValido) {
    $puedeSobreescribirPrecio = true;
}
```

---

## 3. Frontend: `resources/views/ventas/create.blade.php`

### 3a. Variables JS (línea ~3268)

**Agregar variables de control de autorización de precio:**
```javascript
// Autorización admin para precio (nueva)
let adminPrecioToken = '';
let adminPrecioTokenExp = 0;

function adminPrecioTokenValid() {
    return adminPrecioToken !== '' && Date.now() < adminPrecioTokenExp;
}
```

### 3b. Hacer precio clickeable en `renderCart()` (línea ~4330)

**En la parte del render del item, hacer el precio editable al hacer click:**

Buscar la línea que dice `<span>× ${fmt(item.precio)}</span>` dentro del `.ci-meta` y reemplazarla con:

```javascript
${modoObras ? '' : `<div class="ci-qty">
    <button type="button" data-action="dec" data-index="${index}" aria-label="Disminuir cantidad">−</button>
    <span class="qty-val" aria-label="Cantidad">${item.qty}</span>
    <button type="button" data-action="inc" data-index="${index}" aria-label="Aumentar cantidad">+</button>
</span>`}
<span class="ci-price" data-action="edit-price" data-index="${index}" title="Click para editar precio">
    ${fmt(item.precio)} <i class="bi bi-pencil-square" style="font-size:0.75rem;opacity:0.6;"></i>
</span>
```

### 3c. CSS para el precio clickeable (agregar en los estilos del POS, ~línea 330)

```css
.ci-price {
    cursor: pointer;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: opacity 0.15s;
}
.ci-price:hover {
    opacity: 0.8;
}
.ci-price.editing {
    cursor: default;
}
.ci-price-input {
    width: 90px;
    background: rgba(59, 130, 246, 0.1);
    border: 2px solid var(--pos-accent);
    border-radius: 6px;
    color: var(--pos-text);
    padding: 2px 6px;
    font-size: inherit;
    font-weight: 600;
    text-align: right;
}
.ci-price-input:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
```

### 3d. Función de edición inline de precio

**Agregar después de `agregarNotaProducto()` (~línea 4461):**

```javascript
function editarPrecioProducto(index) {
    const item = cart[index];
    if (!item) return;
    
    // Si ya es admin o tiene token, editar directamente
    if (puedeModificarPrecio || adminPrecioTokenValid()) {
        const priceEl = document.querySelector(`.ci-price[data-index="${index}"]`);
        if (!priceEl) return;
        
        const currentPrice = item.precio;
        priceEl.classList.add('editing');
        priceEl.innerHTML = `<input type="number" class="ci-price-input" value="${currentPrice.toFixed(2)}" 
            min="0.01" step="0.01" data-index="${index}" 
            onblur="confirmarPrecio(${index}, this)" onkeydown="handlePrecioKey(event, ${index}, this)">`;
        const input = priceEl.querySelector('input');
        input.focus();
        input.select();
    } else {
        // Necesita autorización admin
        pendingPriceEdit = index;
        openModalAutorizarAdmin('precio');
    }
}

let pendingPriceEdit = null;

function confirmarPrecio(index, input) {
    const item = cart[index];
    if (!item) return;
    
    const newPrice = parseFloat(input.value);
    if (isNaN(newPrice) || newPrice <= 0) {
        showToast('Precio inválido', 'warning');
        renderCart();
        return;
    }
    
    item.precio = newPrice;
    showToast('Precio actualizado a ' + fmt(newPrice), 'success');
    playBeep('success');
    renderCart();
}

function handlePrecioKey(event, index, input) {
    if (event.key === 'Enter') {
        event.preventDefault();
        confirmarPrecio(index, input);
    } else if (event.key === 'Escape') {
        event.preventDefault();
        renderCart();
    }
}

// Event delegation para el botón de editar precio
document.addEventListener('click', function(e) {
    const priceEl = e.target.closest('[data-action="edit-price"]');
    if (priceEl) {
        e.preventDefault();
        editarPrecioProducto(parseInt(priceEl.dataset.index));
    }
});
```

### 3e. Adaptar `openModalAutorizarAdmin()` para contexto de precio

**Modificar la función para aceptar `context`:**

```javascript
function openModalAutorizarAdmin(context = 'sinitbis') {
    const errorBox = $('auth-admin-error');
    if (errorBox) errorBox.style.display = 'none';
    $('auth-admin-email').value = currentUserEmail;
    $('auth-admin-password').value = '';
    
    // Actualizar el título y descripción del modal según el contexto
    const titleEl = document.querySelector('#modalAutorizarAdmin .admin-header h5');
    const subtitleEl = document.querySelector('#modalAutorizarAdmin .admin-header small');
    const warningEl = document.querySelector('#modalAutorizarAdmin .admin-warning span');
    
    if (context === 'precio') {
        if (titleEl) titleEl.textContent = 'Autorización para Modificar Precio';
        if (subtitleEl) subtitleEl.textContent = 'Acción sensible · Modificar precio';
        if (warningEl) warningEl.innerHTML = 'Para <strong style="color:var(--pos-text);">modificar el precio</strong> de este producto se requiere autorización de un usuario con rol de administrador.';
    } else {
        if (titleEl) titleEl.textContent = 'Autorización de Administrador';
        if (subtitleEl) subtitleEl.textContent = 'Acción sensible · Quitar ITBIS';
        if (warningEl) warningEl.innerHTML = 'Para quitar el <strong style="color:var(--pos-text);">ITBIS</strong> de esta línea se requiere autorización de un usuario con rol de administrador. Solo aplica a ventas <strong style="color:var(--pos-text);">Sin Comprobante</strong>.';
    }
    
    new bootstrap.Modal($('modalAutorizarAdmin')).show();
    setTimeout(() => $('auth-admin-password').focus(), 400);
}
```

### 3f. Adaptar `enviarAutorizacionAdmin()` para contexto

```javascript
function enviarAutorizacionAdmin() {
    const email = $('auth-admin-email').value.trim();
    const password = $('auth-admin-password').value;
    const errorBox = $('auth-admin-error');
    const btn = $('btn-auth-admin-submit');
    const context = pendingPriceEdit !== null ? 'precio' : 'sinitbis';
    
    if (errorBox) errorBox.style.display = 'none';
    if (!email || !password) {
        showToast('Ingresa el email y la contraseña del administrador.', 'warning');
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Validando...';
    
    fetch('{{ route('ventas.autorizarAdmin') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ email, password, context }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Guardar token según el contexto
            if (context === 'precio') {
                adminPrecioToken = data.token;
                adminPrecioTokenExp = Date.now() + (5 * 60 * 1000);
            }
            $('admin-token').value = data.token; // también guardar para ITBIS
            
            bootstrap.Modal.getInstance($('modalAutorizarAdmin'))?.hide();
            showToast(`Autorizado por ${data.admin}`, 'success');
            playBeep('success');
            
            // Ejecutar la acción pendiente
            if (context === 'precio' && pendingPriceEdit !== null) {
                editarPrecioProducto(pendingPriceEdit);
                pendingPriceEdit = null;
            } else if (pendingSinItbis !== null) {
                const idx = pendingSinItbis;
                pendingSinItbis = null;
                if (cart[idx]) {
                    cart[idx].sin_itbis = true;
                    renderCart();
                }
            }
        } else {
            if (errorBox) {
                errorBox.textContent = data.error || 'Autorización rechazada.';
                errorBox.style.display = 'block';
            }
            showToast(data.error || 'Autorización rechazada.', 'danger');
            playBeep('error');
        }
    })
    .catch(() => {
        if (errorBox) {
            errorBox.textContent = 'Error al conectar con el servidor. Intenta de nuevo.';
            errorBox.style.display = 'block';
        }
        showToast('Error al autorizar. Intenta de nuevo.', 'danger');
        playBeep('error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Autorizar';
    });
}
```

### 3g. Form hidden input para adminPrecioToken

**Agregar antes del `</form>` en el POS:**

```html
<input type="hidden" name="admin_token" id="admin-token" value="">
```

Ya debería existir, pero verificar que esté ahí. Si no, agregarlo.

### 3h. Adaptar el modal HTML para mostrar contexto dinámico

El modal ya está bien, solo necesitamos que el JS cambie el contenido según el contexto (ya cubierto en 3e).

---

## Resumen de cambios

| Archivo | Cambio |
|---------|--------|
| `VentaController.php` | Agregar `context` al validate, token y response de `autorizarAdmin()` |
| `SaleService.php` | `verificarTokenAdmin()` retorna `bool`, `createSale()` habilita `puedeSobreescribirPrecio` si token es válido |
| `create.blade.php` (JS) | Variables `adminPrecioToken`, función `editarPrecioProducto()`, `confirmarPrecio()`, adaptar `openModalAutorizarAdmin()` y `enviarAutorizacionAdmin()` |
| `create.blade.php` (HTML) | Hacer precio clickeable con `data-action="edit-price"` |
| `create.blade.php` (CSS) | Estilos para `.ci-price` y `.ci-price-input` |

---

## Test manual

1. Login con usuario NO admin
2. Ir a `/ventas/create`
3. Agregar un producto al carrito
4. Click en el precio del producto → debería abrir modal de auth admin
5. Ingresar email+pass de un usuario admin → verificar que el precio se edita
6. Modificar el precio → verificar que se muestra el nuevo precio
7. Enviar la venta → verificar que se crea con el precio modificado
8. Probar que un usuario admin puede editar precio sin modal
