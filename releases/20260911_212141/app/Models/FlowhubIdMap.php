<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowhubIdMap extends Model
{
    protected $table = 'flowhub_id_map';

    protected $fillable = [
        'tenant_id',
        'flow_id',
        'model',
        'real_id',
    ];

    public function businessInstance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public static function resolve(string $flowId, string $model): ?int
    {
        return static::where('flow_id', $flowId)
            ->where('model', $model)
            ->value('real_id');
    }

    public static function store(string $flowId, string $model, int $realId, int $tenantId): void
    {
        static::updateOrInsert(
            ['flow_id' => $flowId, 'model' => $model],
            ['real_id' => $realId, 'tenant_id' => $tenantId]
        );
    }

    public static function toFlowId(int $realId, string $model): ?string
    {
        return static::where('real_id', $realId)
            ->where('model', $model)
            ->value('flow_id');
    }
}
