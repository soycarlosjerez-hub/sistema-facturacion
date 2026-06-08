document.addEventListener('DOMContentLoaded', () => {
    const data = window.conduceData || { id: null, items: [], descuentos: [] };
    const items = data.items.length ? data.items.map(i => ({
        producto_id: i.producto_id,
        nombre: i.nombre,
        codigo: i.codigo,
        cantidad: parseFloat(i.cantidad) || 1,
        unidad: i.unidad || 'UND',
        peso: parseFloat(i.peso) || 0,
    })) : [];

    const tbody = document.getElementById('itemsBody');
    const modalProductos = document.getElementById('modalProductos');
    const searchInput = document.getElementById('searchProducto');
    const resultados = document.getElementById('productosResultados');
    const itemsCount = document.getElementById('itemsCount');
    const totalItems = document.getElementById('totalItemsLabel');
    const totalCantidad = document.getElementById('totalCantidadLabel');
    const totalPeso = document.getElementById('totalPesoLabel');

    const totalInputs = tbody?.closest('.card')?.querySelector('#totalItemsLabel');

    function renderItems() {
        if (items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                        Aún no hay productos. Haz clic en "Agregar Productos".
                    </td>
                </tr>`;
        } else {
            tbody.innerHTML = items.map((it, idx) => `
                <tr>
                    <td class="text-muted">${String(idx + 1).padStart(3, '0')}</td>
                    <td>
                        <div class="fw-semibold">${escape(it.nombre)}</div>
                        <small class="text-muted">${escape(it.codigo || 'Sin código')}</small>
                        <input type="hidden" name="items[${idx}][producto_id]" value="${it.producto_id}">
                        <input type="hidden" name="items[${idx}][nombre]" value="${escape(it.nombre)}">
                        <input type="hidden" name="items[${idx}][codigo]" value="${escape(it.codigo || '')}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center item-cantidad"
                               name="items[${idx}][cantidad]" value="${it.cantidad}" required data-idx="${idx}"
                               aria-label="Cantidad de ${escape(it.nombre)}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm text-center"
                               name="items[${idx}][unidad]" value="${escape(it.unidad)}"
                               aria-label="Unidad de ${escape(it.nombre)}">
                    </td>
                    <td>
                        <input type="number" step="0.001" min="0" class="form-control form-control-sm text-center item-peso"
                               name="items[${idx}][peso]" value="${it.peso}" data-idx="${idx}"
                               aria-label="Peso de ${escape(it.nombre)}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item" data-idx="${idx}"
                                aria-label="Quitar ${escape(it.nombre)}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
        updateTotals();
    }

    function updateTotals() {
        itemsCount.textContent = `${items.length} items`;
        totalItems.textContent = items.length;
        const cant = items.reduce((acc, it) => acc + (parseFloat(it.cantidad) || 0), 0);
        const peso = items.reduce((acc, it) => acc + (parseFloat(it.peso) || 0), 0);
        totalCantidad.textContent = cant.toFixed(2);
        totalPeso.textContent = `${peso.toFixed(2)} kg`;
    }

    function escape(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function renderProductos(filtro = '') {
        const q = filtro.trim().toLowerCase();
        const lista = (window.productosCatalogo || []).filter(p => {
            if (!q) return false;
            return (p.nombre || '').toLowerCase().includes(q) || (p.codigo || '').toLowerCase().includes(q);
        });

        if (!q) {
            resultados.innerHTML = '<div class="text-muted text-center py-3">Escribe al menos 2 caracteres para buscar</div>';
            return;
        }
        if (lista.length === 0) {
            resultados.innerHTML = '<div class="text-muted text-center py-3">Sin resultados para <strong>' + escape(q) + '</strong></div>';
            return;
        }
        resultados.innerHTML = lista.map(p => `
            <button type="button" class="list-group-item list-group-item-action" data-id="${p.id}">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">${escape(p.nombre)}</div>
                        <small class="text-muted">${escape(p.codigo || 'Sin código')}</small>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Stock</div>
                        <strong>${p.stock ?? 0}</strong>
                    </div>
                </div>
            </button>
        `).join('');
    }

    function hideModal() {
        if (modalProductos) {
            const modal = bootstrap.Modal.getInstance(modalProductos);
            if (modal) modal.hide();
        }
    }

    function getCatalogo() {
        return window.productosCatalogo || [];
    }

    // Event delegation on resultados
    if (resultados) {
        resultados.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-id]');
            if (!btn) return;
            const id = parseInt(btn.dataset.id);
            const catalogo = getCatalogo();
            const p = catalogo.find(x => x.id === id);
            if (!p) return;

            if (items.find(i => i.producto_id === id)) {
                const idx = items.findIndex(i => i.producto_id === id);
                items[idx].cantidad = parseFloat(items[idx].cantidad) + 1;
            } else {
                items.push({
                    producto_id: p.id,
                    nombre: p.nombre,
                    codigo: p.codigo || '',
                    cantidad: 1,
                    unidad: p.unidad || 'UND',
                    peso: 0,
                });
            }
            renderItems();
            hideModal();
            window.announce && window.announce(`Producto ${p.nombre} agregado`, 'polite');
        });
    }

    // Event delegation for remove buttons
    if (tbody) {
        tbody.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-item');
            if (!btn) return;
            const idx = parseInt(btn.dataset.idx);
            if (isNaN(idx)) return;
            const removed = items.splice(idx, 1);
            renderItems();
            window.announce && window.announce(`${removed[0]?.nombre || 'Item'} eliminado`, 'polite');
        });

        // Live sync from inputs to items array
        tbody.addEventListener('input', (e) => {
            const input = e.target.closest('.item-cantidad, .item-peso');
            if (!input) return;
            const idx = parseInt(input.dataset.idx);
            if (isNaN(idx) || !items[idx]) return;
            if (input.classList.contains('item-cantidad')) {
                items[idx].cantidad = parseFloat(input.value) || 0;
            } else if (input.classList.contains('item-peso')) {
                items[idx].peso = parseFloat(input.value) || 0;
            }
            updateTotals();
        });
    }

    if (searchInput) {
        let searchTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => renderProductos(searchInput.value), 200);
        });
    }

    if (modalProductos) {
        modalProductos.addEventListener('show.bs.modal', () => {
            searchInput.value = '';
            resultados.innerHTML = '<div class="text-muted text-center py-3">Escribe al menos 2 caracteres para buscar</div>';
            setTimeout(() => searchInput.focus(), 200);
        });
    }

    renderItems();

    // Validación al enviar
    const form = document.getElementById('formConduce');
    form?.addEventListener('submit', (e) => {
        if (items.length === 0) {
            e.preventDefault();
            alert('Debes agregar al menos un producto al conduce.');
            return;
        }
        if (!document.getElementById('direccion_entrega').value.trim()) {
            e.preventDefault();
            alert('La dirección de entrega es obligatoria.');
        }
    });
});
