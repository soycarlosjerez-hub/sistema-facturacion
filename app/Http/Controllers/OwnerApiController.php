<?php

namespace App\Http\Controllers;

use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:owner');
    }

    public function index(Request $request)
    {
        $query = ApiRequestLog::with(['user', 'businessInstance'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->method));
        }

        if ($request->filled('uri')) {
            $query->where('uri', 'like', '%' . $request->uri . '%');
        }

        if ($request->filled('response_status')) {
            $query->where('response_status', $request->response_status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uri', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $logs = $query->paginate(50);

        $stats = [
            'total' => $total,
            'success' => ApiRequestLog::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->where('response_status', '>=', 200)->where('response_status', '<', 400)->count(),
            'errors' => ApiRequestLog::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->where('response_status', '>=', 400)->count(),
            'avg_response_time' => ApiRequestLog::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->avg('response_time_ms') ?? 0,
        ];

        return view('owner.api-requests', compact('logs', 'stats'));
    }

    public function apiIndex(Request $request)
    {
        $query = ApiRequestLog::with(['user', 'businessInstance'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->method));
        }

        if ($request->filled('uri')) {
            $query->where('uri', 'like', '%' . $request->uri . '%');
        }

        if ($request->filled('response_status')) {
            $query->where('response_status', $request->response_status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uri', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(50));
    }

    public function show(ApiRequestLog $apiRequestLog)
    {
        $apiRequestLog->load(['user', 'businessInstance']);
        return response()->json($apiRequestLog);
    }
}
