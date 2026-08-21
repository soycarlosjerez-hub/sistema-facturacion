<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $facturacion_modo = $this->getFacturacionModo();

        if ($facturacion_modo === 'equipos') {
            return $this->rulesParaEquipos();
        }

        return $this->rulesParaProductos();
    }

    /**
     * Obtiene el modo de facturación del negocio actual.
     */
    private function getFacturacionModo(): string
    {
        $user = auth()->user();
        if (!$user?->business_instance_id) {
            return 'productos';
        }

        $businessInstance = \App\Models\BusinessInstance::find($user->business_instance_id);
        if (!$businessInstance) {
            return 'productos';
        }

        return $businessInstance->getDefaultConfig()['facturacion_modo'] ?? 'productos';
    }

    private function rulesParaProductos(): array
    {
        return [
            'proveedor_id'           => 'required|exists:proveedores,id',
            'almacen_id'             => 'nullable|exists:almacenes,id',
            'tipo_compra_id'         => 'required|exists:tipos_compras,id',
            'fecha'                  => 'nullable|date',
            'observaciones'          => 'nullable|string|max:1000',
            'aplica_retencion_isr'   => 'nullable|boolean',
            'aplica_retencion_itbis' => 'nullable|boolean',
            'productos'              => 'required|array|min:1',
            'productos.*.producto_id'      => 'nullable|integer|exists:productos,id',
            'productos.*.nombre'           => 'required_with:productos|string|max:255',
            'productos.*.codigo_barras'    => 'nullable|string|max:100',
            'productos.*.cantidad'         => 'required_with:productos|numeric|min:0.01',
            'productos.*.precio'           => 'required_with:productos|numeric|min:0',
            'productos.*.precio_venta'     => 'nullable|numeric|min:0',
            'productos.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
        ];
    }

    private function rulesParaEquipos(): array
    {
        return [
            'proveedor_id'           => 'required|exists:proveedores,id',
            'almacen_id'             => 'nullable|exists:almacenes,id',
            'tipo_compra_id'         => 'required|exists:tipos_compras,id',
            'fecha'                  => 'nullable|date',
            'observaciones'          => 'nullable|string|max:1000',
            'aplica_retencion_isr'   => 'nullable|boolean',
            'aplica_retencion_itbis' => 'nullable|boolean',
            'productos'              => 'required|array|min:1',
            'productos.*.producto_id'      => 'nullable|integer|exists:productos,id',
            'productos.*.nombre'           => 'required_with:productos|string|max:255',
            'productos.*.serial_imei'        => 'required_with:productos|string|max:50',
            'productos.*.marca'            => 'nullable|string|max:100',
            'productos.*.modelo'           => 'nullable|string|max:200',
            'productos.*.almacenamiento_gb' => 'nullable|string|max:20',
            'productos.*.color'            => 'nullable|string|max:50',
            'productos.*.precio'           => 'required_with:productos|numeric|min:0',
            'productos.*.precio_venta'     => 'nullable|numeric|min:0',
            'productos.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'productos.*.garantia_desde'   => 'nullable|date',
            'productos.*.garantia_hasta'   => 'nullable|date|after_or_equal:productos.*.garantia_desde',
            'productos.*.tipo_dispositivo' => 'nullable|in:celular,laptop,desktop,tablet,servidor,impresora,monitor,router,switch,camara,ups,otro',
        ];
    }

    public function messages(): array
    {
        return [
            'proveedor_id.required'            => 'Selecciona un proveedor.',
            'tipo_compra_id.required'          => 'Selecciona un tipo de compra.',
            'productos.required'               => 'Agrega al menos un producto.',
            'productos.*.nombre.required_with' => 'El nombre del producto es obligatorio.',
            'productos.*.cantidad.required_with' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.min'         => 'La cantidad debe ser mayor a 0.',
            'productos.*.precio.required_with' => 'El precio es obligatorio.',
            'productos.*.precio.min'           => 'El precio no puede ser negativo.',
            'productos.*.serial_imei.required_with' => 'El IMEI/Serial es obligatorio para equipos.',
        ];
    }
}
