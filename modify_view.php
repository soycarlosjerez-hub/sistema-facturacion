<?php
$file = "resources/views/ventas/create.blade.php";
$content = file_get_contents($file);
$lines = explode("\n", $content);

// Find and replace the agregarProductoDesdeModal function
$searchStart = 3913;
$searchEnd = 3934;
$replacement = '    function agregarProductoDesdeModal(id) {
        const p = productos.find(x => x.id === id);
        if (!p) { showToast("Producto no encontrado", "danger"); return; }
        // Si está en modo lavados, agregar como servicio de lavado
        if (modoLavados && p.es_lavado) {
            const existing = cart.find(x => x.es_lavado && x.id === id);
            if (existing) {
                existing.qty += (cantidadesModal[id] || 1);
            } else {
                cart.push({
                    id: p.id,
                    nombre: "Lavado: " + p.nombre,
                    precio: p.precio,
                    itbis_p: p.itbis_porcentaje,
                    qty: cantidadesModal[id] || 1,
                    stock: p.stock,
                    imagen_url: p.imagen_url,
                    descuento: 0,
                    es_lavado: true
                });
            }
            renderCart("add");
            cerrarModalProductos();
            return;
        }
        if (!modoObras && validaStock && p.stock <= 0) { showToast("Producto sin stock", "warning"); return; }
        if (modoObras) {
            const existing = cart.find(x => x.id === id);
            if (existing) { showToast(`La obra "${p.nombre}" ya está en el carrito", "warning"); return; }
            cart.push({ id: p.id, nombre: p.nombre, precio: p.precio, itbis_p: p.itbis_p, qty: 1, stock: 1, imagen_url: p.imagen_url, descuento: 0, renderCart("add");
            cerrarModalProductos();
            return;
        }
        const qty = cantidadesModal[id] || 1;
        const existing = cart.find(x => x.id === id);
        if (existing) {
            existing.qty += qty;
        } else {
            cart.push({ id: p.id, nombre: p.nombre, precio: p.precio, itbis_p: p.itbis_p, qty: qty, stock: p.stock, imagen_url: p.imagen_url, descuento: 0 });
        }
        renderCart("add");
        cerrarModalProductos();
    }';
    
// Replace the function
$lines[$searchStart-1] = $replacement;
array_splice($lines, $searchStart, $searchEnd - $searchStart + 1, [$replacement]);

$newContent = implode("\n", $lines);
file_put_contents($file, $newContent);
echo "Function replaced successfully\n";
'