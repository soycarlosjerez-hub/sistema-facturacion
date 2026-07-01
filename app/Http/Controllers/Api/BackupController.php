<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BackupResource;
use App\Models\Backup;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        $query = Backup::query()
            ->when($request->search, fn ($q) => $q->where('filename', 'like', '%' . $request->search . '%'));

        return BackupResource::collection($query->orderBy('created_at', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'filename' => 'required|string|max:500',
            'size' => 'required|integer|min:0',
            'type' => 'required|string|max:20',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $backup = Backup::create($validated);

        return new BackupResource($backup);
    }

    public function show(Backup $backup)
    {
        return new BackupResource($backup);
    }

    public function destroy(Backup $backup)
    {
        $backup->delete();
        return response()->json(['message' => 'Backup eliminado.']);
    }
}
