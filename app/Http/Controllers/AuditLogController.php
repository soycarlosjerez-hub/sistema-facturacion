<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->ofAction($request->action);
        }
        if ($request->filled('model')) {
            $query->where('model_type', 'like', '%\\' . $request->model);
        }
        if ($request->filled('user_id')) {
            $query->ofUser($request->user_id);
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('description', 'like', "%{$s}%");
        }

        $logs = $query->paginate(30);
        $actions = AuditLog::distinct('action')->pluck('action');
        $models = AuditLog::distinct('model_type')->pluck('model_type')->map(fn($m) => class_basename($m));

        return view('audit_logs.index', compact('logs', 'actions', 'models'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('audit_logs.show', compact('auditLog'));
    }
}
