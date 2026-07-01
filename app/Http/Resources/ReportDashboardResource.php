<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ventas_hoy' => $this['ventas_hoy'] ?? 0,
            'ventas_mes' => $this['ventas_mes'] ?? 0,
            'compras_hoy' => $this['compras_hoy'] ?? 0,
            'compras_mes' => $this['compras_mes'] ?? 0,
            'clientes_totales' => $this['clientes_totales'] ?? 0,
            'productos_activos' => $this['productos_activos'] ?? 0,
            'inventario_bajo_stock' => $this['inventario_bajo_stock'] ?? 0,
            'ingresos_mes' => $this['ingresos_mes'] ?? 0,
            'gastos_mes' => $this['gastos_mes'] ?? 0,
            'ganancia_neta' => $this['ganancia_neta'] ?? 0,
        ];
    }
}
