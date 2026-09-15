<?php

namespace App\Traits;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait TenantScope
 *
 * Adds a global scope to filter queries by the authenticated user's business_instance_id.
 * Models using this trait should have a `tenant_id` column that stores the business_instance_id.
 * Supports both User (business_instance_id) and Cliente (tenant_id) authentication.
 */
trait TenantScope
{
    /**
     * Boot the tenant scope for a model.
     * Filters records by the current user's business_instance_id stored in the tenant_id column.
     * Models can override the column name by setting a $tenantColumn property.
     */
    protected static function bootTenantScope(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($builder->getModel() instanceof User) {
                return;
            }

            $tenantId = static::resolveTenantId();

            if ($tenantId !== null) {
                $model = $builder->getModel();
                $column = $model->getTenantIdColumn();
                $table = $model->getTable().'.'.$column;
                $builder->where($table, $tenantId);

                return;
            }

            // Fail-closed para peticiones HTTP sin tenant resuelto:
            // no devolver datos de ningún tenant (evita fuga cross-tenant).
            // En consola/seeders/colas se permite sin filtro (sin request HTTP).
            try {
                if (app()->runningInConsole()) {
                    return;
                }
                if (function_exists('request') && request()) {
                    $model = $builder->getModel();
                    $builder->whereRaw('1 = 0');
                }
            } catch (\Throwable $e) {
                // Si no hay contexto de request, no filtrar (comandos, jobs boot).
            }
        });

        // Asignación server-side del tenant en creación: nunca desde input.
        static::creating(function ($model) {
            try {
                if (app()->runningInConsole()) {
                    return;
                }
                $column = $model->getTenantIdColumn();
                $current = $model->getAttribute($column);
                if (! empty($current)) {
                    return;
                }
                $tenantId = static::resolveTenantId();
                if ($tenantId !== null) {
                    $model->setAttribute($column, $tenantId);
                }
            } catch (\Throwable $e) {
                // No bloquear creación en contextos sin auth (seeders/tests la fijan explícito).
            }
        });
    }

    /**
     * Resolve the tenant ID from the authenticated user (User or Cliente).
     * Handles both session auth (Auth::user()) and client token auth (request resolver).
     * NUNCA acepta tenant_id desde header/query/body (IDOR).
     */
    private static function resolveTenantId(): ?int
    {
        // 0. Check explicit resolved_tenant_id set by TenantMiddleware
        $resolved = request()->attributes->get('resolved_tenant_id');
        if ($resolved) {
            return (int) $resolved;
        }

        // 1. Try Auth::user() first (session, Sanctum, API key auth)
        if (Auth::check()) {
            $user = Auth::user();
            if ($user instanceof Cliente) {
                return $user->tenant_id;
            }

            return $user->business_instance_id ?: null;
        }

        // 2. Fallback: client token via request resolver (AuthenticateApiKey middleware)
        $clientToken = request()->attributes->get('client_api_token');
        if ($clientToken && $clientToken->cliente) {
            return $clientToken->cliente->tenant_id;
        }

        // Sin tenant autenticado: null (el global scope hace fail-closed en HTTP).
        return null;
    }

    /**
     * Get the column name used for tenant filtering.
     * Override by setting a public $tenantColumn property on the model.
     */
    public function getTenantIdColumn(): string
    {
        return property_exists($this, 'tenantColumn') ? $this->tenantColumn : 'tenant_id';
    }
}
