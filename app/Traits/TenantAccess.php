<?php

namespace App\Traits;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait TenantAccess
 *
 * Provides a method for controllers to verify that an Eloquent model
 * instance belongs to the current authenticated user's tenant (business_instance).
 * Supports both User (business_instance_id) and Cliente (tenant_id) authentication.
 */
trait TenantAccess
{
    /**
     * Get the tenant ID of the current authenticated user.
     *
     * @return int|null
     */
    protected function getCurrentTenantId(): ?int
    {
        // 1. Try Auth::user() (session, Sanctum, API key)
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof Cliente) {
                return $user->tenant_id;
            }
            return $user->business_instance_id ?? null;
        }

        // 2. Fallback: client token via request resolver
        $clientToken = request()->attributes->get('client_api_token');
        if ($clientToken && $clientToken->cliente) {
            return $clientToken->cliente->tenant_id;
        }

        return null;
    }

    /**
     * Verify that a model belongs to the current user's tenant and abort with 404
     * if the model does not have the tenant or does not match.
     */
    protected function requireTenantOwnership(Model $model, string $message = 'Recurso no encontrado.'): void
    {
        $tenantId = $this->getCurrentTenantId();
        if ($tenantId === null) {
            abort(404, 'No se pudo determinar la instancia del negocio.');
        }

        // Quick check: if the model already has tenant_id set and it matches
        if (isset($model->tenant_id) && (int) $model->tenant_id === $tenantId) {
            return;
        }

        // If model has a global scope applied, the model instance will be the one
        // from the scoped query, so it should already have the correct tenant_id.
        // But verify by querying the DB to be sure.
        if (!$this->isOwnedByTenant($model, $tenantId)) {
            abort(404, $message);
        }
    }

    /**
     * Check if a model instance is owned by the given tenant ID.
     */
    protected function isOwnedByTenant(Model $model, int $tenantId): bool
    {
        try {
            return (bool) (clone $model)
                ->newQuery()
                ->where($model->getKeyName(), $model->getKey())
                ->where($model->getTable() . '.tenant_id', $tenantId)
                ->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
