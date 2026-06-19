<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait TenantScope
 *
 * Adds a global scope to filter queries by the authenticated user's tenant_id.
 * Models using this trait should have a `tenant_id` column.
 */
trait TenantScope
{
    /**
     * Boot the tenant scope for a model.
     * This method is automatically called by Laravel when the model boots.
     */
    protected static function bootTenantScope(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                // Assumes the user model has a tenant_id attribute
                $builder->where('tenant_id', Auth::user()->tenant_id);
            }
        });
    }

    /**
     * Helper to allow other code to discover the tenant id column name.
     */
    public function getTenantIdColumn(): string
    {
        return 'tenant_id';
    }
}
