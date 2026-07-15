<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class InstanceRole extends Model
{
    protected $fillable = [
        'business_instance_id', 'name', 'guard_name',
    ];

    public function businessInstance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(InstanceRoleModule::class);
    }

    public function visibleModules(): HasMany
    {
        return $this->hasMany(InstanceRoleModule::class)->where('is_visible', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'instance_role_id');
    }

    public function isModuloVisible(string $moduloKey): bool
    {
        $module = $this->modules()->where('modulo_key', $moduloKey)->first();
        if ($module !== null) {
            return $module->is_visible;
        }
        // Si el rol ya tiene módulos configurados, los que no están listados NO son visibles
        if ($this->modules()->exists()) {
            return false;
        }
        // Fallback al nivel BusinessType (solo si el rol nunca fue personalizado)
        return $this->businessInstance?->businessType?->isModuloVisible($moduloKey) ?? false;
    }

    public function syncModules(array $moduleKeys): void
    {
        DB::beginTransaction();
        try {
            $data = [];
            $orden = 0;
            foreach ($moduleKeys as $key) {
                $data[$key] = [
                    'modulo_key' => $key,
                    'is_visible' => true,
                    'orden' => $orden++,
                ];
            }
            $this->modules()->delete();
            foreach ($data as $item) {
                $this->modules()->create($item);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
