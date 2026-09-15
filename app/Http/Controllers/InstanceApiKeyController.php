<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInstanceApiKeyRequest;
use App\Models\AuditLog;
use App\Models\BusinessInstance;
use App\Models\InstanceApiKey;
use App\Policies\InstanceApiKeyPolicy;
use App\Services\OwnerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InstanceApiKeyController extends Controller
{
    private InstanceApiKeyPolicy $policy;

    public function __construct()
    {
        $this->policy = app(InstanceApiKeyPolicy::class);
    }

    /**
     * Display a listing of the API keys for the instance.
     */
    public function index(BusinessInstance $instance, Request $request): View
    {
        // Policy explícita: $this->authorize('viewAny', $instance) resolvería
        // BusinessInstancePolicy por el modelo; la intención es InstanceApiKeyPolicy.
        abort_unless($this->policy->viewAny($request->user(), $instance), 403);

        $query = InstanceApiKey::with(['creator'])
            ->where('business_instance_id', $instance->id)
            ->search($request->input('search'))
            ->byStatus($request->input('status'))
            ->withTrashed()
            ->when($request->input('trashed') === 'with', fn ($q) => $q->withTrashed())
            ->when($request->input('trashed') === 'only', fn ($q) => $q->onlyTrashed())
            ->latest();

        $apiKeys = $query->paginate(20)->withQueryString();

        return view('owner.instances.api-keys', compact('instance', 'apiKeys'));
    }

    /**
     * Store a newly created API key.
     */
    public function store(CreateInstanceApiKeyRequest $request, BusinessInstance $instance): RedirectResponse
    {
        // Policy explícita (ver index): la intención es InstanceApiKeyPolicy@create.
        abort_unless($this->policy->create($request->user(), $instance), 403);

        $apiKey = OwnerService::createApiKey(
            instance: $instance,
            name: $request->validated('name'),
            userId: $request->user()->id
        );

        $this->logAction(
            'API_KEY_CREATE',
            "API Key '{$apiKey->name}' creada para instancia '{$instance->nombre}'",
            null,
            ['api_key_id' => $apiKey->id],
            $instance
        );

        return redirect()
            ->route('owner.instances.api-keys', $instance)
            ->with('success', 'API Key creada correctamente.')
            ->with('new_api_key', $this->generateRawKey());
    }

    /**
     * Regenerate an API key.
     */
    public function regenerate(Request $request, BusinessInstance $instance, InstanceApiKey $apiKey): RedirectResponse
    {
        $this->authorize('update', $apiKey);

        $rawKey = OwnerService::regenerateApiKey($apiKey);

        $this->logAction(
            'API_KEY_REGENERATE',
            "API Key '{$apiKey->name}' regenerada para instancia '{$instance->nombre}'",
            null,
            ['api_key_id' => $apiKey->id],
            $instance
        );

        return redirect()
            ->route('owner.instances.api-keys', $instance)
            ->with('success', 'API Key regenerada correctamente.')
            ->with('new_api_key', $rawKey);
    }

    /**
     * Toggle an API key active/inactive status.
     */
    public function toggle(Request $request, BusinessInstance $instance, InstanceApiKey $apiKey): RedirectResponse
    {
        $this->authorize('update', $apiKey);

        OwnerService::toggleApiKey($apiKey);
        $apiKey->refresh();
        $status = $apiKey->is_active ? 'activada' : 'desactivada';

        $this->logAction(
            'API_KEY_TOGGLED',
            "API Key '{$apiKey->name}' {$status} en instancia '{$instance->nombre}'",
            null,
            [
                'api_key_id' => $apiKey->id,
                'new_status' => $apiKey->is_active,
            ],
            $instance
        );

        return redirect()
            ->route('owner.instances.api-keys', $instance)
            ->with('success', "API Key \"{$apiKey->name}\" {$status} correctamente.");
    }

    /**
     * Reveal the full API key to an owner.
     */
    public function reveal(BusinessInstance $instance, InstanceApiKey $apiKey): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $apiKey);

        return response()->json([
            'key' => $apiKey->key_raw ?? $apiKey->key,
            'name' => $apiKey->name,
        ]);
    }

    /**
     * Remove (soft delete) or restore an API key.
     */
    public function destroy(Request $request, BusinessInstance $instance, InstanceApiKey $apiKey): RedirectResponse
    {
        $this->authorize('delete', $apiKey);

        $name = $apiKey->name;

        if ($apiKey->trashed()) {
            OwnerService::restoreApiKey($apiKey);

            $this->logAction(
                'API_KEY_RESTORE',
                "API Key '{$name}' restaurada de instancia '{$instance->nombre}'",
                null,
                ['api_key_id' => $apiKey->id],
                $instance
            );

            return redirect()
                ->route('owner.instances.api-keys', $instance)
                ->with('success', "API Key \"{$name}\" restaurada correctamente.");
        }

        OwnerService::deleteApiKey($apiKey);

        $this->logAction(
            'API_KEY_DELETE',
            "API Key '{$name}' eliminada de instancia '{$instance->nombre}'",
            null,
            ['api_key_id' => $apiKey->id],
            $instance
        );

        return redirect()
            ->route('owner.instances.api-keys', $instance)
            ->with('success', "API Key \"{$name}\" eliminada correctamente.");
    }

    /**
     * Permanently delete an API key (force delete).
     */
    public function forceDestroy(Request $request, BusinessInstance $instance): RedirectResponse
    {
        $request->validate(['apiKeyId' => 'required|integer|exists:instance_api_keys,id']);

        $apiKey = InstanceApiKey::withoutGlobalScopes()
            ->withTrashed()
            ->where('business_instance_id', $instance->id)
            ->whereKey($request->apiKeyId)
            ->firstOrFail();

        $this->authorize('forceDelete', $apiKey);

        $name = $apiKey->name;

        OwnerService::forceDeleteApiKey($apiKey);

        $this->logAction(
            'API_KEY_FORCE_DELETE',
            "API Key '{$name}' eliminada permanentemente de instancia '{$instance->nombre}'",
            null,
            ['api_key_id' => $apiKey->id],
            $instance
        );

        return redirect()
            ->route('owner.instances.api-keys', $instance)
            ->with('success', "API Key \"{$name}\" eliminada permanentemente.");
    }

    private function logAction(string $action, string $description, ?array $oldValues, ?array $newValues, ?\Illuminate\Database\Eloquent\Model $model): void
    {
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
            Log::error('Failed to log API key action: '.$e->getMessage());
        }
    }

    private function generateRawKey(): string
    {
        return 'iak_'.Str::random(40);
    }
}
