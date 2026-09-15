<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'whatsapp' => $this->whatsapp,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'provincia' => $this->provincia,
            'codigo_postal' => $this->codigo_postal,
            'rnc_cedula' => $this->rnc_cedula,
            'rnc' => $this->rnc,
            'tipo_documento' => $this->tipo_documento,
            'tipo_cliente' => $this->tipo_cliente,
            'tipo_cliente_label' => $this->tipo_cliente_label,
            'color_badge' => $this->color_badge,
            'limite_credito' => (float) $this->limite_credito,
            'balance_pendiente' => (float) $this->balance_pendiente,
            'credito_disponible' => $this->credito_disponible,
            'utilizacion_credito' => $this->utilizacion_credito,
            'plazo_pago_dias' => $this->plazo_pago_dias,
            'tasa_descuento_pct' => (float) $this->tasa_descuento_pct,
            'moneda' => $this->moneda,
            'moneda_label' => $this->moneda_label,
            'auto_bloquear_credito' => $this->auto_bloquear_credito,
            'notas_internas' => $this->notas_internas,
            'regimen_mensual' => $this->regimen_mensual,
            'nit' => $this->nit,
            'persona_contacto' => $this->persona_contacto,
            'cargo_contacto' => $this->cargo_contacto,
            'segmento' => $this->segmento,
            'segmento_label' => $this->segmento_label,
            'origen_cliente' => $this->origen_cliente,
            'origen_label' => $this->origen_label,
            'sector_actividad' => $this->sector_actividad,
            'tenant_id' => $this->tenant_id,
            'activo' => $this->activo,
            'estado_credito' => $this->estado_credito,
            'estado_credito_label' => $this->estado_credito_label,
            'ventas_count' => $this->whenLoaded('ventas', fn () => $this->ventas->count()),
            'cotizaciones_count' => $this->whenLoaded('cotizaciones', fn () => $this->cotizaciones->count()),
            'ultima_compra' => $this->ultima_compra instanceof \Carbon\Carbon ? $this->ultima_compra->toISOString() : $this->ultima_compra,
            'total_compras' => $this->total_compras,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
