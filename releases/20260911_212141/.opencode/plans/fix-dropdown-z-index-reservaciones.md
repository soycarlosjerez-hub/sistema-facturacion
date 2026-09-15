# Fix: Dropdown z-index en tabla de reservaciones

## Problema
El dropdown de "Cambiar estado" en la tabla de reservaciones aparece detrás de otras filas de la tabla.

## Causa
Las celdas de la tabla (`td`) y el contexto de posicionamiento de Bootstrap no dan suficiente z-index al dropdown-menu, causando que quede oculto detrás de otras filas.

## Solución
Agregar CSS al archivo `resources/views/restaurante/reservaciones.blade.php` en la sección `<style>` (después de la línea 23):

```css
/* Dropdown z-index fix for table */
.table .dropdown-menu {
    z-index: 1050 !important;
    position: absolute !important;
}
.table tbody td .dropdown {
    position: relative;
}
```

## Archivos a modificar
1. `resources/views/restaurante/reservaciones.blade.php` — agregar CSS después de línea 23

## Pasos
1. Abrir `resources/views/restaurante/reservaciones.blade.php`
2. En la sección `<style>` (líneas 7-24), agregar las reglas CSS después de `.table thead th {...}`
3. Guardar y probar en el navegador
