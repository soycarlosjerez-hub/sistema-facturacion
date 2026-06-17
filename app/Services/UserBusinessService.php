<?php

namespace App\Services;

use App\Models\User;
use App\Models\BusinessType;
use Spatie\Permission\Models\Role;

class UserBusinessService
{
    /**
     * Get the allowed roles for a user based on their business type and admin status
     */
    public static function getAllowedRolesForUser(User $user): array
    {
        // Owner (Dueño del Sistema) has full access to all roles
        if ($user->hasRole('owner')) {
            return Role::pluck('name')->toArray();
        }

        $businessTypeSlug = self::getUserBusinessTypeSlug($user);
        
        // Get roles from config based on business type
        $configRoles = self::getRolesFromConfig($businessTypeSlug);
        
        // Root has all standard business roles
        if ($user->hasRole('root')) {
            return $configRoles['root'] ?? [];
        }

        // Admin-business has restricted roles
        if ($user->hasRole('admin-business') || 
            ($businessTypeSlug && $businessTypeSlug === 'while-pone-el-restaurante')) {
            return $configRoles['admin-business'] ?? [];
        }

        // Default: all business roles for the business type
        return $configRoles['root'] ?? [];
    }

    /**
     * Get roles from config based on business type slug
     */
    private static function getRolesFromConfig(?string $businessTypeSlug): array
    {
        $config = config('business_type_roles', []);
        
        if (!$businessTypeSlug || !isset($config[$businessTypeSlug])) {
            // Fallback to restaurante if not found
            $businessTypeSlug = 'restaurante';
        }
        
        return $config[$businessTypeSlug] ?? [];
    }

    /**
     * Get the business type slug for a user
     */
    private static function getUserBusinessTypeSlug(User $user): ?string
    {
        // For owners, they don't have a single business type
        if ($user->hasRole('owner')) {
            return null;
        }
        
        // Check user's direct business_type_id
        if ($user->business_type_id && $user->businessType) {
            return $user->businessType->slug;
        }
        
        // Check user's business_instance
        if ($user->business_instance_id && $user->businessInstance && $user->businessInstance->businessType) {
            return $user->businessInstance->businessType->slug;
        }
        
        return 'restaurante'; // default
    }

    /**
     * Synchronize user roles based on their business type
     * Only removes disallowed roles, never auto-adds.
     */
    public static function syncRolesForUser(User $user): void
    {
        $allowedRoles = self::getAllowedRolesForUser($user);
        $currentRoleNames = $user->roles->pluck('name')->toArray();

        // Remove roles that are not allowed
        $rolesToRemove = array_diff($currentRoleNames, $allowedRoles);
        foreach ($rolesToRemove as $role) {
            $user->removeRole($role);
        }
    }

    /**
     * Validate if a user can have a specific business type
     */
    public static function validateBusinessTypeAssignment(User $user, int $businessTypeId): bool
    {
        // Root can assign any business type
        if ($user->hasRole('root')) {
            return true;
        }

        // Admin-business can only have their own business type
        if ($user->hasRole('admin-business')) {
            return $user->business_type_id === $businessTypeId;
        }

        // Regular users can only have business types assigned by root
        return false;
    }

    /**
     * Get the business type for an admin-business user
     */
    public static function getBusinessTypeForAdminBusiness(User $user): ?string
    {
        if ($user->hasRole('admin-business') && $user->businessType) {
            return $user->businessType->slug;
        }
        return null;
    }
    
    /**
     * Get available roles for a specific business type (for UI)
     */
    public static function getAvailableRolesForBusinessType(string $businessTypeSlug): array
    {
        $config = config('business_type_roles', []);
        $roles = $config[$businessTypeSlug] ?? [];
        
        // Merge all role levels (owner, root, admin-business)
        $allRoles = [];
        foreach ($roles as $levelRoles) {
            $allRoles = array_merge($allRoles, $levelRoles);
        }
        
        return array_unique($allRoles);
    }
}