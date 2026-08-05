<?php

namespace App\Http\Controllers\Api\Arte;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificadoAutenticidadResource;
use App\Models\CertificadoAutenticidad;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificatesController extends Controller
{
    public function index(Request $request)
    {
        $query = CertificadoAutenticidad::with(['obra'])
            ->when($request->obra_id, fn ($q) => $q->where('obra_id', $request->obra_id))
            ->when($request->number, fn ($q) => $q->where('numero_certificado', 'like', "%{$request->number}%"));

        return CertificadoAutenticidadResource::collection($query->orderBy('created_at', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'numero_certificado' => 'required|string|max:100|unique:certificados_autenticidad,numero_certificado',
            'firmado_en' => 'nullable|date',
        ]);

        $certificado = CertificadoAutenticidad::create($validated);

        $qrData = route('api.public.art.catalog.show', ['slug' => $certificado->obra->slug]);
        $qrPath = 'certs/qr-' . $certificado->numero_certificado . '.png';
        QrCode::format('png')->size(300)->generate($qrData, storage_path('app/public/' . $qrPath));

        $certificado->update(['qr_code' => $qrPath]);

        return new CertificadoAutenticidadResource($certificado->load('obra'));
    }

    public function show(CertificadoAutenticidad $certificate)
    {
        return new CertificadoAutenticidadResource($certificate->load('obra'));
    }

    public function update(Request $request, CertificadoAutenticidad $certificate)
    {
        $validated = $request->validate([
            'numero_certificado' => 'sometimes|string|max:100|unique:certificados_autenticidad,numero_certificado,' . $certificate->id,
            'firmado_en' => 'sometimes|nullable|date',
            'expirado' => 'sometimes|nullable|boolean',
        ]);

        $certificate->update($validated);

        return new CertificadoAutenticidadResource($certificate->load('obra'));
    }

    public function destroy(CertificadoAutenticidad $certificate)
    {
        $certificate->delete();
        return response()->json(['message' => 'Certificado eliminado correctamente.']);
    }
}
