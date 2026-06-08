<?php

namespace App\Http\Controllers;

use App\Models\EcfDocumento;
use App\Services\Ecf\EcfService;
use App\Support\RncValidator;
use Illuminate\Http\Request;

class EcfController extends Controller
{
    public function __construct(private EcfService $ecfService) {}

    public function index(Request $request)
    {
        $query = EcfDocumento::with(['venta.cliente', 'secuencia', 'usuario', 'notaCredito'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_ecf')) {
            $query->where('tipo_ecf', $request->tipo_ecf);
        }
        if ($request->filled('encf')) {
            $query->where('encf', 'like', '%' . $request->encf . '%');
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_emision', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->hasta);
        }

        $ecfs = $query->paginate(20)->withQueryString();
        $estados = EcfDocumento::ESTADOS;
        $tipos = EcfDocumento::TIPOS;

        $stats = [
            'total' => EcfDocumento::count(),
            'aprobados' => EcfDocumento::where('estado', 'aprobado')->count(),
            'pendientes' => EcfDocumento::whereIn('estado', EcfDocumento::$pendingStates)->count(),
            'rechazados' => EcfDocumento::where('estado', 'rechazado')->count(),
        ];

        return view('ecf.index', compact('ecfs', 'estados', 'tipos', 'stats'));
    }

    public function show(EcfDocumento $ecf)
    {
        $ecf->load([
            'venta.cliente',
            'venta.detalles.producto',
            'secuencia',
            'certificado',
            'usuario',
            'logs',
            'notaCredito',
            'documentoOriginal',
        ]);
        $qrUrl = app(\App\Services\Ecf\EcfQrGenerator::class)->toQrApiUrl($ecf);
        return view('ecf.show', compact('ecf', 'qrUrl'));
    }

    public function firmar(EcfDocumento $ecf)
    {
        try {
            $ecf = $this->ecfService->firmar($ecf);
            return back()->with('success', "e-CF {$ecf->encf} firmado correctamente");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al firmar: ' . $e->getMessage());
        }
    }

    public function enviar(Request $request, EcfDocumento $ecf)
    {
        try {
            $ecf = $this->ecfService->firmarYEnviar($ecf);
            $msg = $ecf->estado === 'aprobado'
                ? "✓ e-CF {$ecf->encf} enviado y APROBADO por DGII"
                : "⚠ e-CF {$ecf->encf} enviado pero RECHAZADO: {$ecf->mensaje_dgii}";
            return back()->with($ecf->estado === 'aprobado' ? 'success' : 'warning', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }

    public function consultar(Request $request, EcfDocumento $ecf)
    {
        try {
            $ecf = $this->ecfService->consultarEstado($ecf);
            return back()->with('success', "Estado DGII: {$ecf->estado} - {$ecf->mensaje_dgii}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al consultar: ' . $e->getMessage());
        }
    }

    public function notaDebito(Request $request, EcfDocumento $ecf)
    {
        $request->validate([
            'monto_adicional' => 'required|numeric|min:1',
            'motivo' => 'required|string|min:5|max:500',
        ]);
        try {
            $nd = $this->ecfService->generarNotaDebito($ecf, $request->monto_adicional, $request->motivo);
            return back()->with('success', "Nota de Débito E33 generada: {$nd->encf} (estado: {$nd->estado})");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al generar Nota de Débito: ' . $e->getMessage());
        }
    }

    public function anular(Request $request, EcfDocumento $ecf)
    {
        $request->validate(['motivo' => 'required|string|min:5|max:500']);
        try {
            $result = $this->ecfService->anular($ecf, $request->motivo);
            $nc = $result->notaCredito;
            $msg = "e-CF {$ecf->encf} anulado";
            if ($nc) {
                $msg .= ". Nota de Crédito E34 generada: {$nc->encf} (estado: {$nc->estado})";
            }
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }

    public function xml(EcfDocumento $ecf)
    {
        if (empty($ecf->xml_content)) {
            abort(404, 'El e-CF no tiene XML generado aún');
        }
        $filename = $ecf->encf . '.xml';
        return response($ecf->xml_content)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
    }

    public function pdf(EcfDocumento $ecf)
    {
        $ecf->load(['venta.cliente', 'venta.detalles.producto', 'venta.usuario', 'secuencia', 'notaCredito', 'documentoOriginal']);
        $qrUrl = app(\App\Services\Ecf\EcfQrGenerator::class)->toQrApiUrl($ecf);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.ecf-pdf', compact('ecf', 'qrUrl'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream("ecf-{$ecf->encf}.pdf");
    }

    public function validarRnc(Request $request)
    {
        $request->validate(['rnc' => 'required|string', 'tipo' => 'nullable|string']);

        $valido = RncValidator::validar($request->rnc, $request->tipo);
        $tipoInferido = RncValidator::inferirTipo($request->rnc);
        $formateado = RncValidator::formato($request->rnc, $tipoInferido);
        $tipoDgii = RncValidator::tipoDocumentoDgii($tipoInferido);

        return response()->json([
            'valido' => $valido,
            'rnc_original' => $request->rnc,
            'rnc_limpio' => preg_replace('/[^0-9]/', '', $request->rnc),
            'rnc_formateado' => $formateado,
            'tipo_inferido' => $tipoInferido,
            'tipo_dgii' => $tipoDgii,
            'mensaje' => $valido ? 'Documento válido según DGII' : 'Documento inválido (dígito verificador incorrecto)',
        ]);
    }
}
