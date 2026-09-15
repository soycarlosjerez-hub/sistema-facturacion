<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImpresoraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'sucursal_id' => $this->sucursal_id,
            'tipo_conexion' => $this->tipo_conexion,
            'direccion_ip' => $this->direccion_ip,
            'puerto' => $this->puerto,
            'ruta_compartida' => $this->ruta_compartida,
            'driver' => $this->driver,
            'papel_tamano' => $this->papel_tamano,
            'caracteres_por_linea' => $this->caracteres_por_linea,
            'auto_imprimir_ventas' => (bool) $this->auto_imprimir_ventas,
            'auto_imprimir_cotizaciones' => (bool) $this->auto_imprimir_cotizaciones,
            'auto_imprimir_conduces' => (bool) $this->auto_imprimir_conduces,
            'activo' => (bool) $this->activo,
            'orden' => $this->orden,
            'descripcion' => $this->descripcion,
            'configuracion' => $this->configuracion,
            'sucursal' => $this->sucursal ? [
                'id' => $this->sucursal->id,
                'nombre' => $this->sucursal->nombre,
            ] : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
