---
name: datatable-ui
description: "Aplica DataTables client-side jQuery con busqueda, paginacion y responsive. Trigger: 'datatable', 'datatables', 'tabla con busqueda', 'tabla responsive'."
mode: skill
---

# DataTables UI Skill

## Trigger
"Agrega DataTables a [modulo]", "tabla con busqueda [modulo]"

## Service: `listAll()` retorna Collection completa (no paginado)
## Controller: pasa `$items = $service->listAll()` a view
## Vista: `<table id="mod-table">` con `@json($items)` como data

## Script Base
```javascript
const table = $('#mod-table').DataTable({
    data: @json($items),
    columns: [
        { data: null, render: (d,t,r,m) => '<span class="text-muted">'+(m.row+1)+'</span>' },
        { data: null, render: d => '<div class="fw-bold">'+escapeHtml(d.nombre)+'</div>' },
        { data: null, render: d => renderAcciones(d.id, {edit: canEdit, delete: canDelete, view: 'mod/'+d.id, csrf, nombre: d.nombre}) }
    ],
    language: { search: '', paginate: { first: '<i class="bi bi-chevron-double-left"></i>', last: '<i class="bi bi-chevron-double-right"></i>', next: '<i class="bi bi-chevron-right"></i>', previous: '<i class="bi bi-chevron-left"></i>' } },
    pageLength: 10, lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'Todos']],
    responsive: { details: { type: 'column' } },
    dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>'+'<"row"<"col-12"tr>>'+'<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
});
$('#busqueda').on('input', function() { clearTimeout(wt); const v=$(this).val(); wt=setTimeout(()=>table.search(v).draw(), 300); });
```

## Helpers JS → Ver `partials/datatable-ui.blade.php` (ya existen en el proyecto)
