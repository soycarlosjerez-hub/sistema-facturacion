<?php

namespace App\Services;

use App\Models\BusinessInstance;
use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;

class PlanLimitService
{
    public function usersUsados(BusinessInstance $instance): int
    {
        return User::where('business_instance_id', $instance->id)->count();
    }

    public function sucursalesUsadas(BusinessInstance $instance): int
    {
        return Sucursal::where('business_instance_id', $instance->id)->count();
    }

    public function puedeAgregarUsuario(BusinessInstance $instance): bool
    {
        $max = $instance->plan?->max_usuarios;

        return $max === null || $this->usersUsados($instance) < $max;
    }

    public function puedeAgregarSucursal(BusinessInstance $instance): bool
    {
        $max = $instance->plan?->max_sucursales;

        return $max === null || $this->sucursalesUsadas($instance) < $max;
    }

    public function usuariosRestantes(BusinessInstance $instance): ?int
    {
        $max = $instance->plan?->max_usuarios;

        if ($max === null) {
            return null;
        }

        return max(0, $max - $this->usersUsados($instance));
    }

    public function sucursalesRestantes(BusinessInstance $instance): ?int
    {
        $max = $instance->plan?->max_sucursales;

        if ($max === null) {
            return null;
        }

        return max(0, $max - $this->sucursalesUsadas($instance));
    }

    /**
     * @return array{ok: bool, mensaje?: string, limite?: int, usados?: int}
     */
    public function verificarUsuario(BusinessInstance $instance): array
    {
        if ($this->puedeAgregarUsuario($instance)) {
            return ['ok' => true];
        }

        $plan = $instance->plan;

        return [
            'ok' => false,
            'mensaje' => 'El plan ' . ($plan?->nombre ?? 'actual') . ' permite máximo '
                . ($plan?->max_usuarios ?? 0) . ' usuario(s). Considera migrar a un plan superior.',
            'limite' => $plan?->max_usuarios ?? 0,
            'usados' => $this->usersUsados($instance),
        ];
    }

    /**
     * @return array{ok: bool, mensaje?: string, limite?: int, usados?: int}
     */
    public function verificarSucursal(BusinessInstance $instance): array
    {
        if ($this->puedeAgregarSucursal($instance)) {
            return ['ok' => true];
        }

        $plan = $instance->plan;

        return [
            'ok' => false,
            'mensaje' => 'El plan ' . ($plan?->nombre ?? 'actual') . ' permite máximo '
                . ($plan?->max_sucursales ?? 0) . ' sucursal(es). Considera migrar a un plan superior.',
            'limite' => $plan?->max_sucursales ?? 0,
            'usados' => $this->sucursalesUsadas($instance),
        ];
    }

    /**
     * Verifica si el usuario dueño puede crear otra empresa (plan Corporativo).
     */
    public function verificarEmpresa(?Plan $plan, int $instanciasActuales): array
    {
        if ($plan === null || $plan->max_empresas === null) {
            return ['ok' => true];
        }

        if ($instanciasActuales < $plan->max_empresas) {
            return ['ok' => true];
        }

        return [
            'ok' => false,
            'mensaje' => 'El plan ' . $plan->nombre . ' permite máximo '
                . $plan->max_empresas . ' empresa(s).',
            'limite' => $plan->max_empresas,
            'usados' => $instanciasActuales,
        ];
    }
}
