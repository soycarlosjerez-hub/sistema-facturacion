<?php

namespace App\Http\Controllers\Api\Art;

use App\Http\Controllers\Controller;
use App\Http\Resources\ObraDetailPublicResource;
use App\Http\Resources\ObraPublicResource;
use App\Http\Resources\ExhibicionPublicResource;
use App\Models\Exhibicion;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $perPage = min((int) $request->input('per_page', 12), 48);

        $query = Obra::where('status', 'disponible')
            ->orWhere('status', 'en_exposicion');

        $query->when($request->search, fn ($q) => $q->where('titulo', 'like', "%{$request->search}%"));
        $query->when($request->medium, fn ($q) => $q->where('medium', $request->medium));
        $query->when($request->technique, fn ($q) => $q->where('technique', 'like', "%{$request->technique}%"));
        $query->when($request->year_from, fn ($q) => $q->where('year_created', '>=', $request->year_from));
        $query->when($request->year_to, fn ($q) => $q->where('year_created', '<=', $request->year_to));
        $query->when($request->original_only, fn ($q) => $q->where('is_original', true));
        $query->when($request->edition_only, fn ($q) => $q->where('is_original', false));

        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return ObraPublicResource::collection(
            $query->paginate($perPage)
        )->additional(['links' => []]);
    }

    public function show($slug): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $obra = Obra::where('slug', $slug)
            ->whereIn('status', ['disponible', 'en_exposicion', 'vendido', 'reservado', 'en_consulta'])
            ->firstOrFail();

        return new ObraDetailPublicResource($obra);
    }

    public function jsonLd($slug): JsonResponse
    {
        $obra = Obra::where('slug', $slug)
            ->whereIn('status', ['disponible', 'en_exposicion', 'vendido', 'reservado', 'en_consulta'])
            ->firstOrFail();

        $availability = match ($obra->status) {
            'disponible' => 'https://schema.org/InStock',
            'reservado' => 'https://schema.org/PreOrder',
            'vendido' => 'https://schema.org/SoldOut',
            default => 'https://schema.org/OfferItemNotFound',
        };

        $jsonLd = [
            '@context' => 'https://schema.org/',
            '@type' => 'CreativeWork',
            'name' => $obra->titulo,
            'creator' => [
                '@type' => 'Person',
                'name' => config('app.name', 'Escultor'),
            ],
            'dateCreated' => $obra->creation_date?->toDateString() ?? $obra->created_at?->toDateString(),
            'artMedium' => ucfirst($obra->medium ?: 'Unknown'),
            'artform' => 'Sculpture',
            'description' => $obra->descripcion ?? "",
            'dimensions' => [
                '@type' => 'QuantitativeValue',
                'value' => $obra->dimensiones ?: '',
            ],
            'weight' => $obra->peso_kg ? [
                '@type' => 'QuantitativeValue',
                'value' => (float) $obra->peso_kg,
                'unitCode' => 'KGM',
            ] : null,
            'image' => $obra->getAllPhotos(),
            'offers' => [
                '@type' => 'Offer',
                'availability' => $availability,
                'priceCurrency' => 'DOP',
            ],
        ];

        return response()->json($jsonLd, 200, [], JSON_UNESCAPED_UNICODE)
            ->header('Content-Type', 'application/ld+json');
    }

    public function exhibitionsIndex(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $perPage = min((int) $request->input('per_page', 12), 48);

        $query = Exhibicion::withCount('obras')
            ->where('activo', true)
            ->where(function ($q) {
                $q->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now());
            });

        $query->when($request->tipo, fn ($q) => $q->where('tipo', $request->tipo));
        $query->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
            $inner->where('titulo', 'like', "%{$request->search}%")
                ->orWhere('lugar', 'like', "%{$request->search}%");
        }));

        $query->orderBy('fecha_inicio', 'desc');

        return ExhibicionPublicResource::collection(
            $query->paginate($perPage)
        )->additional(['links' => []]);
    }

    public function exhibitionShow($slug): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $exhibicion = Exhibicion::where('slug', $slug)
            ->where('activo', true)
            ->with('obras')
            ->firstOrFail();

        return new ExhibicionPublicResource($exhibicion);
    }

    public function about(): JsonResponse
    {
        $settings = config('art', []);

        return response()->json([
            'data' => [
                'artist_name' => $settings['artist_name'] ?? config('app.name', 'Escultor'),
                'bio' => $settings['bio'] ?? '',
                'bio_short' => $settings['bio_short'] ?? '',
                'foto_perfil' => $settings['foto_perfil'] ? asset('storage/' . $settings['foto_perfil']) : null,
                'biography_full' => $settings['biography_full'] ?? '',
                'education' => $settings['education'] ?? [],
                'awards' => $settings['awards'] ?? [],
                'social_media' => [
                    'instagram' => $settings['social.instagram'] ?? '',
                    'facebook' => $settings['social.facebook'] ?? '',
                    'twitter' => $settings['social.twitter'] ?? '',
                    'youtube' => $settings['social.youtube'] ?? '',
                    'website' => $settings['social.website'] ?? '',
                ],
                'contact_email' => $settings['contact_email'] ?? '',
                'studio_location' => $settings['studio_location'] ?? '',
            ],
        ]);
    }

    public function contact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string|min:10|max:2000',
        ]);

        $this->sendContactNotification($validated);

        return response()->json([
            'message' => 'Mensaje enviado correctamente. Te responderemos pronto.',
        ]);
    }

    public function requestQuote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'titulo_encargo' => 'required|string|max:255',
            'descripcion' => 'required|string|min:20|max:2000',
            'presupuesto_estimado' => 'nullable|numeric|min:0',
            'fecha_deseada' => 'nullable|date|after:today',
            'como_conoce' => 'nullable|in:redes_sociales,google,referencia,galeria,evento,otro',
        ]);

        $this->sendQuoteRequest($validated);

        return response()->json([
            'message' => 'Solicitud enviada correctamente. Nos pondremos en contacto contigo pronto.',
        ]);
    }

    protected function sendContactNotification(array $data): void
    {
        $subject = "[Contacto Web] {$data['asunto']}";
        $body = "Nombre: {$data['nombre']}\n";
        $body .= "Email: {$data['email']}\n";
        if (!empty($data['telefono'])) {
            $body .= "Telefono: {$data['telefono']}\n";
        }
        $body .= "\nMensaje:\n{$data['mensaje']}";

        \Mail::raw($body, function ($message) use ($subject, $data) {
            $message->to(config('mail.admin_contact', env('MAIL_ADMIN_CONTACT')))
                ->subject($subject);
            $message->replyTo($data['email'], $data['nombre']);
        });
    }

    protected function sendQuoteRequest(array $data): void
    {
        $subject = "[Cotizacion Encargo] {$data['titulo_encargo']}";
        $body = "Nombre: {$data['nombre']}\n";
        $body .= "Email: {$data['email']}\n";
        $body .= "Telefono: {$data['telefono']}\n";
        $body .= "Como Conocio: " . ($data['como_conoce'] ?? 'No especificado') . "\n";
        $body .= "Presupuesto Estimado: " . ($data['presupuesto_estimado'] ?? 'No especificado') . "\n";
        $body .= "Fecha Deseada: " . ($data['fecha_deseada'] ?? 'No especificada') . "\n";
        $body .= "\nDescripcion:\n{$data['descripcion']}";

        \Mail::raw($body, function ($message) use ($subject, $data) {
            $message->to(config('mail.admin_contact', env('MAIL_ADMIN_CONTACT')))
                ->subject($subject);
            $message->replyTo($data['email'], $data['nombre']);
        });
    }
}
