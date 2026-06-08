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
        ]);
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
