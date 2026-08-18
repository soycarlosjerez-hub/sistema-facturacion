<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status', 'all');
        $filter = $request->input('filter');

        $query = UserNotification::where('user_id', $user->id);

        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($filter) {
            $query->where('category', $filter);
        }

        $notifications = $query->latest()->paginate($perPage);
        $unreadCount = UserNotification::where('user_id', $user->id)->whereNull('read_at')->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'status', 'filter'));
    }

    public function apiIndex(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 20);
        $filter = $request->input('filter');
        $status = $request->input('status', 'all');

        $query = UserNotification::where('user_id', $user->id);

        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($filter) {
            $query->where('category', $filter);
        }

        $notifications = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $notifications->items(),
            'links' => [
                'first' => $notifications->url(1),
                'last' => $notifications->url($notifications->lastPage()),
                'prev' => $notifications->previousPageUrl(),
                'next' => $notifications->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'unread_count' => UserNotification::where('user_id', $user->id)->whereNull('read_at')->count(),
            ],
        ]);
    }

    public function apiUnreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = UserNotification::where('user_id', $user->id)->whereNull('read_at')->count();

        return response()->json([
            'count' => $count,
            'has_unread' => $count > 0,
        ]);
    }

    public function apiUnreadByCategory(): JsonResponse
    {
        $user = Auth::user();
        $notifications = UserNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->get();

        $categories = [];
        foreach ($notifications->groupBy('category') as $category => $notifs) {
            $categories[$category] = [
                'count' => $notifs->count(),
                'icon' => $notifs->first()?->data['category_icon'] ?? 'bi-bell',
                'label' => $notifs->first()?->data['category_label'] ?? ucfirst($category),
            ];
        }

        return response()->json([
            'total_unread' => $notifications->count(),
            'by_category' => $categories,
        ]);
    }

    /**
     * Feed de actividad para polling en tiempo real.
     */
    public function apiFeed(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = min((int) $request->input('limit', 10), 50);
        $sinceId = (int) $request->input('since_id', 0);

        $query = UserNotification::where('user_id', $user->id);

        if ($sinceId > 0) {
            $query->where('id', '>', $sinceId);
        }

        $items = $query->latest()->limit($limit)->get()->map(fn ($n) => $this->serialize($n));

        return response()->json([
            'items' => $items,
            'unread_count' => UserNotification::where('user_id', $user->id)->whereNull('read_at')->count(),
            'has_new' => $sinceId > 0 && $items->isNotEmpty(),
        ]);
    }

    public function apiRecent(int $limit = 5): JsonResponse
    {
        $user = Auth::user();
        $notifications = UserNotification::where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(fn ($n) => $this->serialize($n)),
            'unread_count' => UserNotification::where('user_id', $user->id)->whereNull('read_at')->count(),
        ]);
    }

    protected function serialize(UserNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'category' => $n->category,
            'title' => $n->title,
            'body' => $n->body,
            'action' => $n->action ?? $n->data['verb'] ?? null,
            'actor_id' => $n->actor_id,
            'actor_name' => $n->actor_name,
            'actor_avatar' => $n->actor_avatar,
            'icon' => $n->data['icon'] ?? 'bi-bell',
            'color' => $n->data['color'] ?? '#3b82f6',
            'category_icon' => $n->data['category_icon'] ?? 'bi-bell',
            'category_label' => $n->data['category_label'] ?? 'Sistema',
            'action_url' => $n->data['action_url'] ?? null,
            'read' => !is_null($n->read_at),
            'created_at' => $n->created_at->diffForHumans(),
            'created_at_raw' => $n->created_at->toISOString(),
        ];
    }

    public function apiMarkAsRead($id): JsonResponse
    {
        $user = Auth::user();
        $notification = UserNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída',
        ]);
    }

    public function apiMarkAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        UserNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas',
        ]);
    }

    public function apiDelete($id): JsonResponse
    {
        $user = Auth::user();
        $notification = UserNotification::where('user_id', $user->id)->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificación eliminada',
        ]);
    }

    public function apiCleanOld(): JsonResponse
    {
        $user = Auth::user();
        $cutoff = now()->subDays(30);
        $deleted = UserNotification::where('user_id', $user->id)
            ->where('created_at', '<', $cutoff)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deleted} notificaciones antiguas eliminadas",
            'deleted_count' => $deleted,
        ]);
    }

    public function apiPreferences(): JsonResponse
    {
        $user = Auth::user();
        $prefs = NotificationPreference::forUser($user);

        return response()->json($prefs);
    }

    public function apiUpdatePreferences(Request $request): JsonResponse
    {
        $user = Auth::user();
        $prefs = NotificationPreference::forUser($user);
        $allowed = (new NotificationPreference())->getFillable();

        $data = $request->only($allowed);
        foreach ($data as $key => $value) {
            $prefs->{$key} = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        $prefs->save();

        return response()->json([
            'success' => true,
            'message' => 'Preferencias de notificación actualizadas',
        ]);
    }
}