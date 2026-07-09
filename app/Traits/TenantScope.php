<?php

namespace App\Traits;

use App\Models\User;
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
     * Filters records by the current user's business_instance_id stored in the tenant_id column.
     * Models can override the column name by setting a $tenantColumn property.
     */
    protected static function bootTenantScope(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Skip for User model to prevent infinite recursion (Auth::user() triggers User query)
            if ($builder->getModel() instanceof User) {
                return;
            }
            if (Auth::check() && Auth::user()->business_instance_id !== null) {
                $model = $builder->getModel();
                $column = $model->getTenantIdColumn();
                $builder->where($model->getTable() . '.' . $column, Auth::user()->business_instance_id);
            }
        });
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
