<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait LogsOwnerAction
{
    /**
     * Registrar una acción de auditoría del owner.
     */
    protected function logOwnerAction(
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Model $model = null
    ): void {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model?->id,
                'description' => $description,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'tenant_id' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log owner action: ' . $e->getMessage());
        }
    }
}
