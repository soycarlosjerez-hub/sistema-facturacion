<?php

namespace App\Services;

use App\Models\User;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Tipos de prioridad disponibles
     */
    public const PRIORITIES = [
        'info' => ['icon' => 'bi-info-circle', 'color' => '#3b82f6'],
        'success' => ['icon' => 'bi-check-circle', 'color' => '#22c55e'],
        'warning' => ['icon' => 'bi-exclamation-triangle', 'color' => '#f59e0b'],
        'danger' => ['icon' => 'bi-x-circle', 'color' => '#ef4444'],
    ];

    /**
     * Categorías de notificación
     */
    public const CATEGORIES = [
        'sale' => ['label' => 'Venta', 'icon' => 'bi-receipt'],
        'order' => ['label' => 'Orden', 'icon' => 'bi-box'],
        'payment' => ['label' => 'Pago', 'icon' => 'bi-credit-card'],
        'inventory' => ['label' => 'Inventario', 'icon' => 'bi-box-seam'],
        'cash' => ['label' => 'Caja', 'icon' => 'bi-cash-stack'],
        'fiscal' => ['label' => 'Fiscal', 'icon' => 'bi-file-earmark-text'],
        'system' => ['label' => 'Sistema', 'icon' => 'bi-gear'],
    ];

    /**
     * Crear una notificación para usuarios de la instancia
     */
    public function create(
        string $title,
        string $body,
        string $category = 'system',
        string $priority = 'info',
        ?string $actionUrl = null,
        ?array $metadata = null,
        ?User $actor = null,
        ?User $recipient = null
    ): \Illuminate\Database\Eloquent\Collection {
        
        $categoryMeta = self::CATEGORIES[$category] ?? self::CATEGORIES['system'];
        $priorityMeta = self::PRIORITIES[$priority] ?? self::PRIORITIES['info'];
        
        $users = $this->getTargetUsers($actor, $recipient);
        $created = collect();

        foreach ($users as $user) {
            $pref = NotificationPreference::forUser($user);
            
            $prefKey = $this->getPrefKeyFromCategory($category, $priority);
            if (!$pref->isEnabled($prefKey)) {
                continue;
            }

            try {
                $notification = $user->notifications()->create([
                    'type' => "{$category}.{$priority}",
                    'data' => [
                        'title' => $title,
                        'body' => $body,
                        'category' => $category,
                        'priority' => $priority,
                        'action_url' => $actionUrl,
                        'metadata' => $metadata ?? [],
                        'actor_id' => $actor?->id,
                        'actor_name' => $actor?->name,
                        'icon' => $priorityMeta['icon'],
                        'color' => $priorityMeta['color'],
                        'category_icon' => $categoryMeta['icon'],
                        'category_label' => $categoryMeta['label'],
                    ],
                ]);
                $created->push($notification);
            } catch (\Exception $e) {
                Log::error("NotificationService: Failed to create notification for user {$user->id}: " . $e->getMessage());
            }
        }

        return $created;
    }

    /**
     * Obtener usuarios objetivo basado en el actor y recipient
     */
    protected function getTargetUsers(?User $actor, ?User $recipient): array
    {
        if ($recipient) {
            return [$recipient];
        }

        if ($actor) {
            $instanceId = $actor->business_instance_id;
            return User::where('business_instance_id', $instanceId)
                ->where(function ($q) {
                    $q->whereHas('roles', fn($qr) => $qr->where('name', 'admin'))
                      ->orWhere('role', 'admin');
                })
                ->get()
                ->toArray();
        }

        return [];
    }

    /**
     * Mapear categoria/prioridad a pref key
     */
    protected function getPrefKeyFromCategory(string $category, string $priority): string
    {
        $mapping = [
            'sale' => [
                'info' => 'sale_created',
                'success' => 'sale_paid',
                'danger' => 'sale_cancelled',
            ],
            'order' => [
                'info' => 'order_confirmed',
                'success' => 'order_ready',
                'warning' => 'order_shipped',
            ],
            'payment' => [
                'info' => 'payment_received',
                'warning' => 'credit_overdue',
                'success' => 'credit_abono',
            ],
            'inventory' => [
                'danger' => 'stock_critical',
                'success' => 'stock_restocked',
                'info' => 'product_created',
            ],
            'cash' => [
                'info' => 'shift_opened',
                'success' => 'shift_closed',
                'warning' => 'cash_shortage',
            ],
            'fiscal' => [
                'warning' => 'ncff_expiring',
                'danger' => 'ecf_certificate_expiring',
            ],
            'system' => [
                'success' => 'backup_completed',
                'danger' => 'backup_failed',
                'info' => 'user_registered',
            ],
        ];

        return $mapping[$category][$priority] ?? 'sale_created';
    }

    /**
     * Notificación rápida desde categoría y mensaje
     */
    public function notify(
        string $message,
        string $category = 'system',
        string $priority = 'info',
        ?string $actionUrl = null,
        ?array $metadata = null,
        ?User $actor = null,
        ?User $recipient = null
    ) {
        return $this->create(
            title: $this->generateTitle($category, $priority),
            body: $message,
            category: $category,
            priority: $priority,
            actionUrl: $actionUrl,
            metadata: $metadata,
            actor: $actor,
            recipient: $recipient
        );
    }

    /**
     * Generar título automático según categoría y prioridad
     */
    protected function generateTitle(string $category, string $priority): string
    {
        $titles = [
            'sale' => [
                'info' => 'Nueva Venta Registrada',
                'success' => 'Venta Pagada',
                'danger' => 'Venta Cancelada',
            ],
            'order' => [
                'info' => 'Nueva Orden Recibida',
                'success' => 'Orden Lista para Retiro',
                'warning' => 'Orden Enviada a Delivery',
            ],
            'payment' => [
                'info' => 'Pago Recibido',
                'warning' => 'Crédito Vencido',
                'success' => 'Abono a Cuenta',
            ],
            'inventory' => [
                'danger' => 'Stock Crítico Detectado',
                'success' => 'Producto Reabastecido',
                'info' => 'Nuevo Producto Creado',
            ],
            'cash' => [
                'info' => 'Turno Abierto',
                'success' => 'Turno Cerrado',
                'warning' => 'Discrepancia en Caja',
            ],
            'fiscal' => [
                'warning' => 'NCF Próximo a Vencer',
                'danger' => 'Certificado ECF Por Vencer',
            ],
            'system' => [
                'success' => 'Backup Completado',
                'danger' => 'Error del Sistema',
                'info' => 'Nuevo Usuario Registrado',
            ],
        ];

        return $titles[$category][$priority] ?? 'Notificación del Sistema';
    }

    /**
     * Verificar si una categoría tiene alguna notificación sin leer reciente
     */
    public function hasUnreadInCategory(string $category, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();
        if (!$user) return false;

        return $user->unreadNotifications()
            ->where('data->category', $category)
            ->exists();
    }

    /**
     * Contar notificaciones sin leer por categoría
     */
    public function unreadByCategory(?User $user = null): array
    {
        $user = $user ?? Auth::user();
        if (!$user) return [];

        $notifications = $user->unreadNotifications->groupBy(fn($n) => $n->data['category'] ?? 'system');

        $result = [];
        foreach ($notifications as $category => $notifs) {
            $result[$category] = [
                'count' => $notifs->count(),
                'icon' => self::CATEGORIES[$category]['icon'] ?? 'bi-bell',
                'label' => self::CATEGORIES[$category]['label'] ?? ucfirst($category),
            ];
        }

        return $result;
    }
}
