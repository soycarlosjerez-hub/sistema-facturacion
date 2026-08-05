<?php

namespace App\Http\Controllers\Api\Arte;

use App\Http\Controllers\Controller;
use App\Http\Resources\EncargoResource;
use App\Models\Encargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EncargosController extends Controller
{
    public function index(Request $request)
    {
        $query = Encargo::with(['cliente', 'user'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('titulo', 'like', "%{$request->search}%");
            }))->orWhereHas('cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->search}%");
            })
            ->when($request->active_only, fn ($q) => $q->whereNotIn('status', ['completado', 'cancelado']))
            ->when($request->overdue_only, fn ($q) => $q->where('estimated_completion', '<', now())->whereNotIn('status', ['completado', 'cancelado']));

        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return EncargoResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'user_id' => 'nullable|exists:users,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'boceto_path' => 'nullable|string',
            'sketch_approved' => 'nullable|boolean',
            'precio_total' => 'required|numeric|min:0',
            'deposito' => 'nullable|numeric|min:0',
            'estimated_completion' => 'nullable|date',
            'notas' => 'nullable|string',
        ]);

        $validated['saldo'] = $validated['precio_total'] - ($validated['deposito'] ?? 0);
        $encargo = Encargo::create($validated);

        return new EncargoResource($encargo->load(['cliente', 'user']));
    }

    public function show(Encargo $encargo)
    {
        return new EncargoResource($encargo->load(['cliente', 'user']));
    }

    public function update(Request $request, Encargo $encargo)
    {
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|nullable|string',
            'boceto_path' => 'sometimes|nullable|string',
            'sketch_approved' => 'sometimes|nullable|boolean',
            'precio_total' => 'sometimes|numeric|min:0',
            'deposito' => 'sometimes|nullable|numeric|min:0',
            'saldo' => 'sometimes|nullable|numeric|min:0',
            'estimated_completion' => 'sometimes|nullable|date',
            'status' => 'sometimes|in:solicitado,aprobado,deposito,creacion,progreso,aprobado_final,listo_entrega,completado,cancelado',
            'notas' => 'sometimes|nullable|string',
        ]);

        if (isset($validated['precio_total']) || isset($validated['deposito'])) {
            $precio = $validated['precio_total'] ?? $encargo->precio_total;
            $deposito = $validated['deposito'] ?? $encargo->deposito;
            $validated['saldo'] = $precio - $deposito;
        }

        $encargo->update($validated);

        return new EncargoResource($encargo->load(['cliente', 'user']));
    }

    public function destroy(Encargo $encargo)
    {
        $encargo->delete();
        return response()->json(['message' => 'Encargo eliminado correctamente.']);
    }

    public function updateProgress(Request $request, Encargo $encargo)
    {
        $validated = $request->validate([
            'avance_porcentaje' => 'sometimes|integer|min:0|max:100',
            'actual_completion' => 'sometimes|nullable|date',
            'notas' => 'sometimes|nullable|string',
        ]);

        if (isset($validated['avance_porcentaje']) && $validated['avance_porcentaje'] >= 100) {
            $validated['status'] = 'completado';
        } elseif (isset($validated['avance_porcentaje']) && $validated['avance_porcentaje'] >= 60) {
            $validated['status'] = 'progreso';
        }

        $encargo->update($validated);

        return response()->json([
            'message' => 'Avance del encargo actualizado.',
            'data' => new EncargoResource($encargo->load(['cliente'])),
        ]);
    }

    public function uploadProgressPhoto(Request $request, Encargo $encargo)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $path = $request->file('photo')->store('encargos/' . $encargo->id, 'public');

        $existingPhotos = $encargo->progress_photos ?? [];
        if (!is_array($existingPhotos)) {
            $existingPhotos = [];
        }
        $existingPhotos[] = $path;
        $encargo->update(['progress_photos' => $existingPhotos]);

        return response()->json([
            'message' => 'Foto de progreso subida correctamente.',
            'progress_photo' => asset('storage/' . $path),
        ]);
    }
}
