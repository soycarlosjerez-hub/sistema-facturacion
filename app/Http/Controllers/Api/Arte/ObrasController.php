<?php

namespace App\Http\Controllers\Api\Arte;

use App\Http\Controllers\Controller;
use App\Http\Resources\ObraResource;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObrasController extends Controller
{
    public function index(Request $request)
    {
        $query = Obra::with(['categoria', 'certificate'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->medium, fn ($q) => $q->where('medium', $request->medium))
            ->when($request->technique, fn ($q) => $q->where('technique', 'like', "%{$request->technique}%"))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('titulo', 'like', "%{$request->search}%")
                    ->orWhere('codigo_unico', 'like', "%{$request->search}%")
                    ->orWhere('medium', 'like', "%{$request->search}%");
            }))
            ->when($request->is_original !== null, fn ($q) => $q->where('is_original', filter_var($request->is_original, FILTER_VALIDATE_BOOLEAN)))
            ->when($request->condition, fn ($q) => $q->where('condition_status', $request->condition))
            ->when($request->has_certificate !== null, fn ($q) => $q->where(function ($inner) use ($request) {
                if (filter_var($request->has_certificate, FILTER_VALIDATE_BOOLEAN)) {
                    $inner->whereNotNull('certificate_number');
                } else {
                    $inner->whereNull('certificate_number');
                }
            }))
            ->when($request->year_from, fn ($q) => $q->where('year_created', '>=', $request->year_from))
            ->when($request->year_to, fn ($q) => $q->where('year_created', '<=', $request->year_to));

        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return ObraResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'codigo_unico' => 'nullable|string|max:50|unique:obras,codigo_unico',
            'dimensiones' => 'nullable|string|max:100',
            'peso_kg' => 'nullable|numeric|min:0',
            'medium' => 'required|in:bronce,marmol,madera,hierro,mixed_media,arcilla,yeso,otros',
            'technique' => 'nullable|string|max:100',
            'year_created' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'edition_number' => 'nullable|integer|min:1',
            'edition_total' => 'nullable|integer|min:1|required_if:edition_number,!null',
            'certificate_number' => 'nullable|string|max:100',
            'photos' => 'nullable|array',
            'photos.*' => 'required|string',
            'condition_status' => 'required|in:excelente,bueno,regular,necesita_restauracion',
            'creation_date' => 'nullable|date',
            'exhibition_history' => 'nullable|array',
            'exhibition_history.*' => 'required|string',
            'is_original' => 'required|boolean',
            'status' => 'required|in:disponible,vendido,reservado,en_consulta,en_exposicion,en_consignacion',
            'cost_materials' => 'nullable|numeric|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
        ]);

        $obra = Obra::create($validated);

        return new ObraResource($obra->load(['categoria', 'certificate']));
    }

    public function show(Obra $obra)
    {
        return new ObraResource($obra->load(['categoria', 'certificate']));
    }

    public function update(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'codigo_unico' => 'sometimes|string|max:50|unique:obras,codigo_unico,' . $obra->id,
            'dimensiones' => 'sometimes|string|max:100',
            'peso_kg' => 'sometimes|numeric|min:0',
            'medium' => 'sometimes|in:bronce,marmol,madera,hierro,mixed_media,arcilla,yeso,otros',
            'technique' => 'sometimes|string|max:100',
            'year_created' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'edition_number' => 'sometimes|nullable|integer|min:1',
            'edition_total' => 'sometimes|nullable|integer|min:1',
            'certificate_number' => 'sometimes|nullable|string|max:100',
            'photos' => 'sometimes|nullable|array',
            'photos.*' => 'required|string',
            'condition_status' => 'sometimes|in:excelente,bueno,regular,necesita_restauracion',
            'creation_date' => 'sometimes|nullable|date',
            'exhibition_history' => 'sometimes|nullable|array',
            'exhibition_history.*' => 'required|string',
            'is_original' => 'sometimes|boolean',
            'status' => 'sometimes|in:disponible,vendido,reservado,en_consulta,en_exposicion,en_consignacion',
            'cost_materials' => 'sometimes|nullable|numeric|min:0',
            'categoria_id' => 'sometimes|nullable|exists:categorias,id',
        ]);

        $obra->update($validated);

        return new ObraResource($obra->load(['categoria', 'certificate']));
    }

    public function destroy(Obra $obra)
    {
        $obra->delete();
        return response()->json(['message' => 'Obra eliminada correctamente.']);
    }

    public function uploadPhotos(Request $request, Obra $obra)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $uploadedPaths = [];
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('obras/' . $obra->id, 'public');
            $uploadedPaths[] = $path;
        }

        $existingPhotos = $obra->photos ?? [];
        if (!is_array($existingPhotos)) {
            $existingPhotos = [];
        }
        $allPhotos = array_merge($existingPhotos, $uploadedPaths);
        $obra->update(['photos' => $allPhotos]);

        return response()->json([
            'message' => 'Fotos subidas correctamente.',
            'photos' => $obra->getAllPhotos(),
        ]);
    }

    public function deletePhoto(Request $request, Obra $obra, $filename)
    {
        $photos = $obra->photos ?? [];
        if (!is_array($photos)) {
            return response()->json(['message' => 'No hay fotos para eliminar.'], 400);
        }

        $found = array_search($filename, $photos);
        if ($found === false) {
            return response()->json(['message' => 'Foto no encontrada.'], 404);
        }

        unset($photos[$found]);
        $photos = array_values($photos);
        $obra->update(['photos' => $photos]);

        return response()->json(['message' => 'Foto eliminada correctamente.']);
    }

    public function updateStatus(Request $request, Obra $obra)
    {
        $validated = $request->validate([
            'status' => 'required|in:disponible,vendido,reservado,en_consulta,en_exposicion,en_consignacion',
        ]);

        $obra->update($validated);

        return response()->json([
            'message' => "Estado de la obra actualizado a '{$obra->status_label}'.",
            'data' => new ObraResource($obra),
        ]);
    }
}
