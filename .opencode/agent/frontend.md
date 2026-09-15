---
description: "Especialista frontend Blade/CSS/JavaScript/Vue. Vistas blade, partials, DataTables, UI premium, Bootstrap 5.3, responsive, dark mode. Trigger: vista, blade, frontend, UI, CSS, Bootstrap, DataTables, premium, responsive, dark mode, formulario."
mode: subagent
---

## Stack
Blade + Bootstrap 5.3 + jQuery 3.7.1 + DataTables 1.13.7 + ES6+ + Vue 3 (opcional) + Dark mode

## Reglas (hereda AGENTS.md root)
- `@include('partials.premium-ui')` en CADA vista `@push('styles')`
- DataTables: `@include('partials.datatable-ui')` en index, service `listAll()`, `@json($items)`
- `hasAnyRole()` permisos, `@can()` en vistas, flash español
- NUNCA `migrate:refresh`/`migrate:fresh` sin seed
- UI: `.ui-page`, `.ui-header`, `.ui-card`, `.ui-stat`, `.ui-sticky-bar`, `.ui-btn-*`, `.ui-badge-*`
- Sticky bar: `.ui-sticky-bar` con Cancelar + Guardar
- JS helpers: `escapeHtml()`, `renderMoneda()`, `renderEstado()`, `renderFecha()`, `renderAvatar()`, `renderAcciones()`
- Acciones: `UI.confirm.delete(url, label)`, `UI.toast.success/error/warning/info()`
- Dark mode: centralizado en partial, overrides solo en `<style>` del modulo
- Para specs de negocio → `business-analyst`
