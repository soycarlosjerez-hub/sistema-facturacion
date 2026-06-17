@php
$rolConfig = [
    'root' => [
        'color' => '#7c3aed',
        'gradient' => 'linear-gradient(135deg,#7c3aed,#5b21b6)',
        'icon' => 'bi-gem',
        'label' => 'Root',
        'desc' => 'Acceso total sin restricciones.'
    ],
    'owner' => [
        'color' => '#f43f5e',
        'gradient' => 'linear-gradient(135deg,#f43f5e,#e11d48)',
        'icon' => 'bi-crown-fill',
        'label' => 'Owner',
        'desc' => 'Dueño del sistema (Super Admin Multi-tenant).'
    ],
    'admin' => [
        'color' => '#ef4444',
        'gradient' => 'linear-gradient(135deg,#ef4444,#dc2626)',
        'icon' => 'bi-shield-lock-fill',
        'label' => 'Admin',
        'desc' => 'Acceso total al sistema.'
    ],
    'admin-business' => [
        'color' => '#111827',
        'gradient' => 'linear-gradient(135deg,#111827,#374151)',
        'icon' => 'bi-building',
        'label' => 'Admin Business',
        'desc' => 'Administrador por negocio.'
    ],
    'gerente' => [
        'color' => '#f59e0b',
        'gradient' => 'linear-gradient(135deg,#f59e0b,#d97706)',
        'icon' => 'bi-person-badge-fill',
        'label' => 'Gerente',
        'desc' => 'Gestión operativa.'
    ],
    'vendedor' => [
        'color' => '#38bdf8',
        'gradient' => 'linear-gradient(135deg,#38bdf8,#0ea5e9)',
        'icon' => 'bi-cart-check-fill',
        'label' => 'Vendedor',
        'desc' => 'POS y ventas.'
    ],
    'almacen' => [
        'color' => '#22c55e',
        'gradient' => 'linear-gradient(135deg,#22c55e,#16a34a)',
        'icon' => 'bi-box-seam-fill',
        'label' => 'Almacén',
        'desc' => 'Inventario.'
    ],
    'contador' => [
        'color' => '#6366f1',
        'gradient' => 'linear-gradient(135deg,#6366f1,#4f46e5)',
        'icon' => 'bi-calculator-fill',
        'label' => 'Contador',
        'desc' => 'Reportes.'
    ],
    'supervisor' => [
        'color' => '#eab308',
        'gradient' => 'linear-gradient(135deg,#eab308,#ca8a04)',
        'icon' => 'bi-eye-fill',
        'label' => 'Supervisor',
        'desc' => 'Supervisión general.'
    ],
    'administrativo' => [
        'color' => '#0ea5e9',
        'gradient' => 'linear-gradient(135deg,#0ea5e9,#0284c7)',
        'icon' => 'bi-file-earmark-text-fill',
        'label' => 'Administrativo',
        'desc' => 'Tareas administrativas.'
    ],
    'mesero' => [
        'color' => '#ec4899',
        'gradient' => 'linear-gradient(135deg,#ec4899,#db2777)',
        'icon' => 'bi-person-lines-fill',
        'label' => 'Mesero',
        'desc' => 'Atención en mesas.'
    ],
    'cocinero' => [
        'color' => '#f97316',
        'gradient' => 'linear-gradient(135deg,#f97316,#ea580c)',
        'icon' => 'bi-fire',
        'label' => 'Cocinero',
        'desc' => 'Cocina y KDS.'
    ],
    'bartender' => [
        'color' => '#8b5cf6',
        'gradient' => 'linear-gradient(135deg,#8b5cf6,#7c3aed)',
        'icon' => 'bi-cup-straw',
        'label' => 'Bartender',
        'desc' => 'Bar y bebidas.'
    ],
    'delivery' => [
        'color' => '#14b8a6',
        'gradient' => 'linear-gradient(135deg,#14b8a6,#0d9488)',
        'icon' => 'bi-bicycle',
        'label' => 'Delivery',
        'desc' => 'Repartos.'
    ],
    'lavador' => [
        'color' => '#06b6d4',
        'gradient' => 'linear-gradient(135deg,#06b6d4,#0891b2)',
        'icon' => 'bi-droplet-fill',
        'label' => 'Lavador',
        'desc' => 'Lavadero de carros.'
    ],
    'recepcionista' => [
        'color' => '#f59e0b',
        'gradient' => 'linear-gradient(135deg,#f59e0b,#d97706)',
        'icon' => 'bi-person-square',
        'label' => 'Recepcionista',
        'desc' => 'Reservaciones y recepción.'
    ],
    'inspector' => [
        'color' => '#64748b',
        'gradient' => 'linear-gradient(135deg,#64748b,#475569)',
        'icon' => 'bi-search',
        'label' => 'Inspector',
        'desc' => 'Control de calidad.'
    ],
    'cajero' => [
        'color' => '#10b981',
        'gradient' => 'linear-gradient(135deg,#10b981,#059669)',
        'icon' => 'bi-cash-stack',
        'label' => 'Cajero',
        'desc' => 'Caja y pagos.'
    ],
    'reponedor' => [
        'color' => '#84cc16',
        'gradient' => 'linear-gradient(135deg,#84cc16,#65a30d)',
        'icon' => 'bi-box-arrow-up',
        'label' => 'Reponedor',
        'desc' => 'Reposición de stock.'
    ],
    'despachador' => [
        'color' => '#f97316',
        'gradient' => 'linear-gradient(135deg,#f97316,#ea580c)',
        'icon' => 'bi-truck',
        'label' => 'Despachador',
        'desc' => 'Despacho de órdenes.'
    ],
    'vendedor-mayorista' => [
        'color' => '#a855f7',
        'gradient' => 'linear-gradient(135deg,#a855f7,#9333ea)',
        'icon' => 'bi-bag-check-fill',
        'label' => 'Vendedor Mayorista',
        'desc' => 'Ventas por mayor.'
    ],
    'consultor' => [
        'color' => '#0ea5e9',
        'gradient' => 'linear-gradient(135deg,#0ea5e9,#0284c7)',
        'icon' => 'bi-lightbulb-fill',
        'label' => 'Consultor',
        'desc' => 'Servicios profesionales.'
    ],
    'facturador' => [
        'color' => '#22c55e',
        'gradient' => 'linear-gradient(135deg,#22c55e,#16a34a)',
        'icon' => 'bi-receipt',
        'label' => 'Facturador',
        'desc' => 'Emisión de comprobantes.'
    ],
];

$defaultConfig = [
    'color' => '#64748b',
    'gradient' => 'linear-gradient(135deg,#64748b,#475569)',
    'icon' => 'bi-person',
    'label' => 'Rol',
    'desc' => 'Rol personalizado.'
];
@endphp