<?php

namespace App\Services;

use App\Models\BusinessInstance;
use App\Models\InstanceNotificationSetting;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Crea notificaciones de actividad para todo el equipo de una instancia.
     *
     * @param string  $type        Tipo de actividad (sale_created, cliente_created, ...)
     * @param string  $category    Categoría (sale, order, payment, inventory, cash, fiscal, system, ...)
     * @param string  $title       Título principal del feed
     * @param string  $body        Detalle / subtítulo
     * @param array   $extra       Datos extra: icon, color, action_url, category_icon, category_label, verb
     * @param int|null $tenantId   business_instance_id donde ocurrió la actividad
     * @param User|null $actor     Usuario que realizó la acción
     * @param array   $recipientIds Destinatarios explícitos (opcional)
     */
    public function notifyInstance(
        string $type,
        string $category,
        string $title,
        string $body,
        array $extra = [],
        ?int $tenantId = null,
        ?User $actor = null,
        array $recipientIds = []
    ): void {
        $tenantId = $tenantId ?: $actor?->business_instance_id;

        if ($tenantId && !$this->instanceAllows($tenantId, $type)) {
            return;
        }

        if (empty($recipientIds)) {
            $recipientIds = $this->teamIdsForInstance($tenantId);
        }
        if (empty($recipientIds)) {
            return;
        }

        $recipientIds = $this->filterByUserPreferences($recipientIds, $type);

        $now = now();
        $rows = [];
        $base = $this->buildPayload($type, $category, $title, $body, $extra, $tenantId, $actor, $now);

        foreach ($recipientIds as $userId) {
            $rows[] = array_merge($base, ['user_id' => $userId]);
        }

        if (!empty($rows)) {
            DB::table('user_notifications')->insert($rows);
        }
    }

    /**
     * Registra una actividad de feed a partir de un cambio CRUD (auditable).
     */
    public function feedFromAudit(string $action, string $description, Model $model, ?User $actor = null): void
    {
        $actor = $actor ?: auth()->user();
        if (!$actor || !$actor->business_instance_id) {
            return;
        }

        $this->notifyInstance(
            type: $this->crudType($action, $model),
            category: $this->categoryFor($model),
            title: $this->labelFor($model),
            body: $description,
            extra: [
                'icon' => $this->iconFor($action),
                'color' => $this->colorFor($action),
                'action_url' => $this->urlForModel($model),
                'category_icon' => $this->categoryIconFor($model),
                'category_label' => $this->categoryLabelFor($model),
                'verb' => $this->verbFor($action),
            ],
            tenantId: $actor->business_instance_id,
            actor: $actor,
        );
    }

    /**
     * Lista de IDs de los usuarios (todo el equipo) de una instancia.
     */
    protected function teamIdsForInstance(?int $tenantId): array
    {
        if (!$tenantId) {
            return [];
        }

        return User::query()
            ->where('business_instance_id', $tenantId)
            ->pluck('id')
            ->all();
    }

    protected function instanceAllows(?int $tenantId, string $type): bool
    {
        if (!$tenantId) {
            return true;
        }

        $instance = BusinessInstance::find($tenantId);
        if (!$instance) {
            return true;
        }

        try {
            $settings = InstanceNotificationSetting::forInstance($instance);
            $knownKeys = (new InstanceNotificationSetting())->getFillable();

            if (in_array($type, $knownKeys, true)) {
                return $settings->isEnabled($type);
            }
        } catch (\Throwable $e) {
            return true;
        }

        return true;
    }

    protected function filterByUserPreferences(array $userIds, string $type): array
    {
        $knownKeys = (new NotificationPreference())->getFillable();

        if (!in_array($type, $knownKeys, true)) {
            return $userIds;
        }

        $defaults = NotificationPreference::defaultPreferences();
        $prefs = NotificationPreference::query()
            ->whereIn('user_id', $userIds)
            ->get()
            ->keyBy('user_id');

        return array_values(array_filter($userIds, function ($userId) use ($type, $defaults, $prefs) {
            $enabled = $prefs->get($userId)?->isEnabled($type) ?? ($defaults[$type] ?? true);

            return $enabled;
        }));
    }

    protected function buildPayload(
        string $type,
        string $category,
        string $title,
        string $body,
        array $extra,
        ?int $tenantId,
        ?User $actor,
        \Illuminate\Support\Carbon $now
    ): array {
        $data = array_merge([
            'icon' => 'bi-bell',
            'color' => '#3b82f6',
            'action_url' => null,
            'category_icon' => 'bi-bell',
            'category_label' => ucfirst($category),
            'verb' => null,
        ], $extra);

        return [
            'tenant_id' => $tenantId,
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name ?? 'Sistema',
            'actor_avatar' => null,
            'action' => $data['verb'],
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'body' => $body,
            'data' => json_encode($data),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    // ------------------------------------------------------------------
    // Helpers CRUD (Auditable)
    // ------------------------------------------------------------------

    protected function crudType(string $action, Model $model): string
    {
        return str(class_basename($model))->snake() . '_' . $action;
    }

    protected function verbFor(string $action): string
    {
        return match ($action) {
            'created' => 'creó',
            'updated' => 'actualizó',
            'deleted' => 'eliminó',
            default => $action,
        };
    }

    protected function iconFor(string $action): string
    {
        return match ($action) {
            'created' => 'bi-plus-circle',
            'updated' => 'bi-pencil-square',
            'deleted' => 'bi-trash',
            default => 'bi-activity',
        };
    }

    protected function colorFor(string $action): string
    {
        return match ($action) {
            'created' => '#10b981',
            'updated' => '#3b82f6',
            'deleted' => '#ef4444',
            default => '#6366f1',
        };
    }

    protected function labelFor(Model $model): string
    {
        try {
            if (isset($model->nombre) && $model->nombre) {
                return (string) $model->nombre;
            }
            if (isset($model->name) && $model->name) {
                return (string) $model->name;
            }
        } catch (\Throwable $e) {
            // noop
        }

        return $model->auditLabel();
    }

    protected function categoryFor(Model $model): string
    {
        return $this->categoryMap($model)['category'] ?? 'system';
    }

    protected function categoryLabelFor(Model $model): string
    {
        return $this->categoryMap($model)['label'] ?? 'Sistema';
    }

    protected function categoryIconFor(Model $model): string
    {
        return $this->categoryMap($model)['icon'] ?? 'bi-bell';
    }

    protected function categoryMap(Model $model): array
    {
        $map = [
            'Venta' => ['category' => 'sale', 'label' => 'Ventas', 'icon' => 'bi-receipt'],
            'Cotizacion' => ['category' => 'sale', 'label' => 'Cotizaciones', 'icon' => 'bi-file-text'],
            'Devolucion' => ['category' => 'sale', 'label' => 'Devoluciones', 'icon' => 'bi-arrow-counterclockwise'],
            'Orden' => ['category' => 'order', 'label' => 'Órdenes', 'icon' => 'bi-list-ul'],
            'Mesa' => ['category' => 'order', 'label' => 'Mesas', 'icon' => 'bi-grid'],
            'Producto' => ['category' => 'inventory', 'label' => 'Productos', 'icon' => 'bi-box-seam'],
            'Compra' => ['category' => 'inventory', 'label' => 'Compras', 'icon' => 'bi-truck'],
            'Proveedor' => ['category' => 'inventory', 'label' => 'Proveedores', 'icon' => 'bi-person-lines-fill'],
            'Almacen' => ['category' => 'inventory', 'label' => 'Almacenes', 'icon' => 'bi-box'],
            'Conduce' => ['category' => 'inventory', 'label' => 'Condiciones', 'icon' => 'bi-truck'],
            'Cliente' => ['category' => 'cliente', 'label' => 'Clientes', 'icon' => 'bi-people'],
            'Caja' => ['category' => 'cash', 'label' => 'Caja', 'icon' => 'bi-cash-stack'],
            'Gasto' => ['category' => 'gasto', 'label' => 'Gastos', 'icon' => 'bi-receipt-cutoff'],
            'Sucursal' => ['category' => 'system', 'label' => 'Sucursales', 'icon' => 'bi-building'],
            'User' => ['category' => 'system', 'label' => 'Usuarios', 'icon' => 'bi-person'],
        ];

        return $map[class_basename($model)] ?? ['category' => 'system', 'label' => 'Sistema', 'icon' => 'bi-bell'];
    }

    protected function urlForModel(Model $model): ?string
    {
        $basename = class_basename($model);

        $routes = [
            'Venta' => ['ventas.show', $model->id],
            'Cotizacion' => ['cotizaciones.show', $model->id],
            'Orden' => ['ordenes.show', $model->id],
            'Producto' => ['productos.show', $model->id],
            'Compra' => ['compras.show', $model->id],
            'Proveedor' => ['proveedores.show', $model->id],
            'Cliente' => ['clientes.show', $model->id],
            'Gasto' => ['gastos.show', $model->id],
            'Caja' => ['cajas.index', null],
            'Sucursal' => ['sucursales.index', null],
            'Mesa' => ['restaurante.mesas.index', null],
            'Almacen' => ['almacenes.index', null],
        ];

        if (!isset($routes[$basename])) {
            return null;
        }

        try {
            [$route, $param] = $routes[$basename];

            return $param !== null ? route($route, $param) : route($route);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
