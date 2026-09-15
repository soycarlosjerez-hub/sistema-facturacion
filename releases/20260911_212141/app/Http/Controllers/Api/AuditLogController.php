<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'auditable'])
            ->when($request->user_id, fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->action, fn ($q) => $q->where('action', $request->action))
            ->when($request->model_type, fn ($q) => $q->where('model_type', $request->model_type))
            ->when($request->fecha_desde, fn ($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
            ->when($request->fecha_hasta, fn ($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta));

        return response()->json([
            'data' => $query->orderBy('created_at', 'desc')->paginate(15),
        ]);
    }
}
