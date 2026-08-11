<?php

namespace App\Observers;

use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Conduce;
use App\Models\Cotizacion;
use App\Models\Caja;
use App\Models\Devolucion;
use App\Models\Gasto;
use App\Models\Mesa;
use App\Models\Orden;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use App\Services\PlanLimitService;
use Illuminate\Support\Facades\Log;

class PlanLimitObserver
{
    protected PlanLimitService $planLimitService;

    /**
     * Mapeo de modelos a recursos de plan
     */
    protected array $modelResourceMap = [
        User::class => 'usuario',
        Sucursal::class => 'sucursal',
        Almacen::class => 'almacen',
        Producto::class => 'producto',
        Cliente::class => 'cliente',
        Proveedor::class => 'proveedor',
        Venta::class => 'venta',
        Compra::class => 'compra',
        Gasto::class => 'gasto',
        Caja::class => 'caja',
        Cotizacion::class => 'cotizacion',
        Conduce::class => 'conduce',
        Devolucion::class => 'devolucion',
        Orden::class => 'orden',
        Mesa::class => 'mesa',
    ];

    public function __construct(PlanLimitService $planLimitService)
    {
        $this->planLimitService = $planLimitService;
    }

    /**
     * Handle the model "creating" event.
     */
    public function creating($model): void
    {
        $resource = $this->modelResourceMap[get_class($model)] ?? null;

        if (!$resource) {
            return;
        }

        $instance = $this->getBusinessInstance($model);

        if (!$instance || !$instance->plan) {
            return;
        }

        $resultado = $this->planLimitService->verificar($instance, $resource);

        if (!$resultado['ok']) {
            $mensaje = $resultado['mensaje'] ?? "Límite de {$resource} excedido para el plan actual.";

            Log::warning('Plan limit exceeded on model creation', [
                'model' => get_class($model),
                'resource' => $resource,
                'instance_id' => $instance->id,
                'instance_slug' => $instance->slug,
                'plan' => $instance->plan->nombre,
                'limit' => $resultado['limite'] ?? null,
                'used' => $resultado['usados'] ?? null,
            ]);

            throw new \Illuminate\Validation\ValidationException(
                \Illuminate\Validation\Validator::make([], [])
                    ->after(function ($validator) use ($mensaje) {
                        $validator->errors()->add('plan_limit', $mensaje);
                    })
            );
        }
    }

    /**
     * Obtiene la BusinessInstance asociada al modelo
     */
    protected function getBusinessInstance($model): ?\App\Models\BusinessInstance
    {
        // Modelos con tenant_id
        if (isset($model->tenant_id)) {
            return \App\Models\BusinessInstance::find($model->tenant_id);
        }

        // Modelos con business_instance_id
        if (isset($model->business_instance_id)) {
            return \App\Models\BusinessInstance::find($model->business_instance_id);
        }

        // User tiene business_instance_id
        if ($model instanceof User) {
            return $model->businessInstance;
        }

        // Sucursal tiene business_instance_id
        if ($model instanceof Sucursal) {
            return \App\Models\BusinessInstance::find($model->business_instance_id);
        }

        // Almacen tiene sucursal_id -> business_instance_id
        if ($model instanceof Almacen && $model->sucursal_id) {
            $sucursal = \App\Models\Sucursal::find($model->sucursal_id);
            if ($sucursal) {
                return \App\Models\BusinessInstance::find($sucursal->business_instance_id);
            }
        }

        // Mesa tiene business_instance_id
        if ($model instanceof Mesa) {
            return \App\Models\BusinessInstance::find($model->business_instance_id);
        }

        // Caja tiene business_instance_id
        if ($model instanceof Caja) {
            return \App\Models\BusinessInstance::find($model->business_instance_id);
        }

        return null;
    }
}