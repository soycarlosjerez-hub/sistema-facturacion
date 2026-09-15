---
description: "Aplica UI premium glassmorphism: header animado, cards glass, stat cards, sticky bar, badges, detail cards, empty state, dark mode. Trigger: 'ui premium', 'premium ui', 'aplica premium', 'interfaz premium'."
mode: skill
---

# Premium UI System — V2

## Trigger
"Aplica UI premium a [modulo]", "ui premium [modulo]", "haz que [modulo] se vea premium"

## Incluir en CADA Vista
```blade
@push('styles')
@include('partials.premium-ui')
@endpush
```

## Colores por Modulo
Gastos/Clientes: #10b981 | Productos/Ventas: #3b82f6 | Compras: #f59e0b | Categorias/Usuarios/Roles: #8b5cf6 | Almacenes/Proveedores: #3b82f6 | Devoluciones: #ef4444

## Page
```html
<div class="ui-page" style="--accent:#HEX;--accent-rgb:R,G,B;--accent-hover:#HOVER"></div>
```

## Header
```html
<div class="ui-header mb-4">
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle"><i class="bi bi-ICON"></i></div>
            <div><h4 class="ui-header-title">TITULO</h4><div class="ui-header-meta">Subtitulo</div></div>
        </div>
        <div class="ui-header-actions">
            <a href="{{ route('mod.create') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill"><i class="bi bi-plus-lg me-1"></i>Nuevo</a>
        </div>
    </div>
</div>
```

## Card / Stat / Sticky / Form / Badge / Empty / Detail / Error / Edit Banner / Section Header → Ver AGENTS.md + partials/

## JS Global
```javascript
UI.confirm.delete(url, label)
UI.confirm.action({ title, text, icon, color, url })
UI.toast.success('msg') / .error('msg') / .warning('msg') / .info('msg')
```

## Checklist
- [ ] `@include('partials.premium-ui')` en `@push('styles')`
- [ ] `.ui-page` con `--accent`, `--accent-rgb`, `--accent-hover`
- [ ] Header con bubbles + avatar | Cards `.ui-card` + `.ui-card-accent`
- [ ] Botones `.ui-btn-*` (no inline gradient) | Badges `.ui-badge-*`
- [ ] Sticky bar `.ui-sticky-bar` | `UI.confirm.delete()` reemplaza `confirm()`
- [ ] Dark mode (centralizado en partial)
