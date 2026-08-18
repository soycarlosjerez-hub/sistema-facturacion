<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->auditAction('created', 'Creó ' . $model->auditLabel(), [], $model->getAuditableValues());
        });

        static::updated(function ($model) {
            $changed = $model->getDirty();
            if (empty($changed)) return;
            $old = [];
            $new = [];
            foreach ($changed as $key => $val) {
                if (in_array($key, $model->getAuditableIgnored() ?? [])) continue;
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $val;
            }
            if (empty($new)) return;
            $model->auditAction('updated', 'Actualizó ' . $model->auditLabel(), $old, $new);
        });

        static::deleted(function ($model) {
            $model->auditAction('deleted', 'Eliminó ' . $model->auditLabel(), $model->getAuditableValues(), []);
        });
    }

    protected function auditAction(string $action, string $description, array $old, array $new): void
    {
        if (!Auth::check()) return;

        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => get_class($this),
            'model_id'   => $this->id,
            'description' => $description,
            'old_values'  => $old,
            'new_values'  => $new,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'tenant_id'  => Auth::user()->business_instance_id ?? null,
        ]);

        $this->pushFeedEntry($action, $description, $new);
    }

    /**
     * Modelos con eventos de dominio dedicados (o internos) que no generan
     * entrada genérica en el feed para evitar duplicados.
     */
    protected function getAuditableFeedExcluded(): array
    {
        return [
            \App\Models\Venta::class,
            \App\Models\Compra::class,
            \App\Models\Orden::class,
            \App\Models\BusinessInstance::class,
        ];
    }

    /**
     * Campos que no generan entrada en el feed (actualizaciones automáticas/contadores).
     */
    protected function getFeedIgnoredFields(): array
    {
        return ['stock', 'ventas_count', 'balance_pendiente', 'last_seen_at'];
    }

    protected function pushFeedEntry(string $action, string $description, array $new = []): void
    {
        try {
            if (in_array(get_class($this), $this->getAuditableFeedExcluded(), true)) {
                return;
            }

            if (!in_array($action, ['created', 'updated', 'deleted'], true)) {
                return;
            }

            if ($action === 'updated') {
                $ignored = $this->getFeedIgnoredFields();
                $keys = array_keys($new);
                $meaningful = array_filter($keys, fn ($k) => !in_array($k, $ignored, true));
                if (empty($meaningful)) {
                    return;
                }
            }

            app(\App\Services\NotificationService::class)->feedFromAudit($action, $description, $this);
        } catch (\Throwable $e) {
            // El feed nunca debe romper la operación principal
        }
    }

    protected function getAuditableValues(): array
    {
        $data = $this->toArray();
        foreach ($this->getAuditableIgnored() as $key) {
            unset($data[$key]);
        }
        return $data;
    }

    protected function getAuditableIgnored(): array
    {
        return ['updated_at', 'remember_token'];
    }

    protected function auditLabel(): string
    {
        return class_basename($this) . ' #' . $this->id;
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }
}
