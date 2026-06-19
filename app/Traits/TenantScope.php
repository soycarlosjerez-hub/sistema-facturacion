<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait TenantScope
 *
 * Adds a global scope to filter queries by the authenticated user's business_instance_id.
 * Models using this trait should have a `tenant_id` column that stores the business_instance_id.
 */
trait TenantScope
{
    /**
     * Boot the tenant scope for a model.
     * This method is automatically called by Laravel when the model boots.
     * Filters records by the current user's business_instance_id stored in the tenant_id column.
     */
    protected static function bootTenantScope(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check() && Auth::user()->business_instance_id !== null) {
                // Compare model's tenant_id against user's business_instance_id
                $builder->where('tenant_id', Auth::user()->business_instance_id);
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
