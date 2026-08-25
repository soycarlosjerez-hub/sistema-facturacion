<?php
$file = "resources/views/ventas/create.blade.php";
$content = file_get_contents($file);
$lines = explode("\n", $content);

// Find the agregarProductoDesdeModal function and replace it with a simpler version
$replacement = '    function agregarProductoDesdeModal(id) {
        const p = productos.find(x => x.id === id);
        if (!p) { showToast("Producto no encontrado", "danger"); return; }
        // Agregar al carrito - si el usuario es lavador y el producto tiene es_lavado marcado,
        // o si estamos en modo lavados, lo agregamos con la bandera es_lavado
        const esLavador = "@if(Auth::check() && Auth::user().role === \"vendedor\")@ else false @";
        const isLavadoProduct = "@php echo isset($producto->categoria_id && $producto->categoria_id == 9) ? "true" : "false" @"; // Categoria 9 = Lavados (example)
        
        if (modoLavados || (" . $esLavador . " && " . $isLavadoProduct . @")) {
            // Modo lavados o usuario lavador con producto de lavado: agregar con bandera es_lavado
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
            } else {
                // Producto normal
                const qty = cantidadesModal[id] || 1;
                const existing2 = cart.find(x => x.id === id && !x.es_lavado);
                if (existing2) {
                    existing2.qty += qty;
                } else {
                    cart.push({
                        id: p.id,
                        nombre: p.nombre,
                        precio: p.precio,
                        itbis_p: p.itbis_porcentaje,
                        qty: qty,
                        stock: p.stock,
                        imagen_url: p.imagen_url,
                        descuento: 0
                    });
                }
            }
            renderCart("add");
            cerrarModalProductos();
        } else {
            // Producto normal (modo regular)
            if (!modoObras && validaStock && p.stock <= 0) { showToast("Producto sin stock", "warning"); return; }
            const qty = cantidadesModal[id] || 1;
            const existing = cart.find(x => x.id === id);
            if (existing) {
                existing.qty += qty;
            } else {
                cart.push({ id: p.id, nombre: p.nombre, precio: p.precio, itbis_p: p.itbis_p, qty: qty, stock: p.stock, imagen_url: p.imagen_url, descuento: 0 });
            }
            renderCart("add");
            cerrarModalProductos();
        }
    }';
    
// Encontrar la línea de la función y reemplazarla
// La función empieza en la línea 3913
$lines = $content;
$functionStart = 3912; // línea donde empieza el patrón "function agregarProductoDesdeModal"
$functionEnd = 3934; // línea donde termina

// Reemplazar desde la función anterior hasta aquí
// Buscar la línea anterior a la función
$prevLine = $lines[$functionStart - 2];
echo "Línea anterior: " . substr($prevLine, 0, 80) . "\n";

// Reemplazar las líneas de la función
array_splice($lines, $functionStart - 1, $functionEnd - $functionStart + 1, [$replacement]);
$newContent = implode("\n", $lines);
file_put_contents($file, $newContent);
echo "Reemplazado successfully\n";
'