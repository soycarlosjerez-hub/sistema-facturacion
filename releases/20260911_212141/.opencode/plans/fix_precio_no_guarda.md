# Fix: Precio editable no se guarda

## Problema

El precio se hace editable al hacer click, pero al intentar confirmar (blur o Enter) no se guarda porque:

1. El input se crea DENTRO del `<span class="ci-price" data-action="edit-price">`
2. El listener global `document.addEventListener('click', ...)` captura clicks en el input
3. `e.target.closest('[data-action="edit-price"]')` encuentra el padre `<span>`
4. Se vuelve a llamar `editarPrecioProducto()` en vez de guardar
5. El ciclo se repite infinitamente

## Solución

Cuando se crea el `<input>` de edición, agregar `event.stopPropagation()` en su evento click para que no propague al padre. Esto se hace añadiendo `onclick="event.stopPropagation()"` al input.

### Archivo: `resources/views/ventas/create.blade.php`

**En la función `editarPrecioProducto()` (~línea 4519), cambiar:**

```javascript
priceEl.innerHTML = `<input type="number" class="ci-price-input" value="${currentPrice.toFixed(2)}" 
    min="0.01" step="0.01" data-index="${index}" 
    onblur="confirmarPrecio(${index}, this)" onkeydown="handlePrecioKey(event, ${index}, this)">`;
```

**Por:**

```javascript
priceEl.innerHTML = `<input type="number" class="ci-price-input" value="${currentPrice.toFixed(2)}" 
    min="0.01" step="0.01" data-index="${index}" 
    onclick="event.stopPropagation()"
    onblur="confirmarPrecio(${index}, this)" onkeydown="handlePrecioKey(event, ${index}, this)">`;
```

El `onclick="event.stopPropagation()"` hace que los clicks en el input NO propoguen al padre `.ci-price`, así el listener global no los captura.

## Verificación

Después del fix:
1. Click en precio → input aparece
2. Click en el input → NO reabre la edición (stopPropagation)
3. Cambiar valor y perder foco → `onblur` dispara `confirmarPrecio()` → guarda y re-renderiza
4. O presionar Enter → `handlePrecioKey()` dispara `confirmarPrecio()` → guarda y re-renderiza
