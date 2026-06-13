<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCotizacionRequest;
use App\Http\Requests\UpdateCotizacionRequest;
use App\Models\Cotizacion;
use App\Services\CotizacionEmailService;
use App\Services\CotizacionService;
use App\Services\PrintService;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    public function __construct(
        protected CotizacionService $cotizacionService,
        protected CotizacionEmailService $emailService,
        protected PrintService $printService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:cotizaciones.view')->only(['index', 'show', 'pdf', 'ticket']);
        $this->middleware('permission:cotizaciones.create')->only(['create', 'store']);
        $this->middleware('permission:cotizaciones.edit')->only(['edit', 'update', 'cambiarEstado', 'enviar']);
        $this->middleware('permission:cotizaciones.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->cotizacionService->list($request->all());
        return view('cotizaciones.index', $data);
    }

    public function create()
    {
        return view('cotizaciones.create', $this->cotizacionService->getCreateData());
    }

    public function store(StoreCotizacionRequest $request)
    {
        try {
            $cotizacion = $this->cotizacionService->create($request->validated());
            return redirect()->route('cotizaciones.show', $cotizacion)
                ->with('success', "Cotización {$cotizacion->numero} creada exitosamente");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear la cotización: ' . $e->getMessage());
        }
    }

    public function show(Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items.producto', 'venta']);
        return view('cotizaciones.show', ['cotizacion' => $cotizacione]);
    }

    public function edit(Cotizacion $cotizacione)
    {
        return view('cotizaciones.edit', $this->cotizacionService->getEditData($cotizacione));
    }

    public function update(UpdateCotizacionRequest $request, Cotizacion $cotizacione)
    {
        try {
            $this->cotizacionService->update($cotizacione, $request->validated());
            return redirect()->route('cotizaciones.show', $cotizacione)
                ->with('success', 'Cotización actualizada');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Cotizacion $cotizacione)
    {
        try {
            $numero = $cotizacione->numero;
            $this->cotizacionService->delete($cotizacione);
            return redirect()->route('cotizaciones.index')
                ->with('success', "Cotización {$numero} eliminada");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, Cotizacion $cotizacione)
    {
        $request->validate(['estado' => 'required|in:borrador,enviada,aprobada,rechazada,vencida,anulada']);

        $result = $this->cotizacionService->cambiarEstado(
            $cotizacione,
            $request->estado,
            $request->boolean('enviar_email'),
            $request->input('mensaje_email')
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function enviar(Request $request, Cotizacion $cotizacione)
    {
        $request->validate([
            'email_destino' => 'nullable|email',
            'mensaje'       => 'nullable|string|max:1000',
            'incluir_pdf'   => 'nullable|boolean',
        ]);

        $resultado = $this->emailService->enviar(
            $cotizacione,
            $request->input('mensaje', ''),
            $request->input('email_destino'),
            $request->boolean('incluir_pdf', true)
        );

        if ($resultado['success']) {
            return back()->with('success',
                "Cotización enviada por email a {$resultado['destinatario']}"
                . (isset($resultado['mail_id']) && $resultado['mail_id'] ? " (ID: {$resultado['mail_id']})" : '')
            );
        }

        return back()->with('error', 'Error al enviar email: ' . $resultado['error']);
    }

    public function convertirAVenta(Request $request, Cotizacion $cotizacione)
    {
        try {
            $venta = $this->cotizacionService->convertirAVenta($cotizacione);
            return redirect()->route('ventas.show', $venta)
                ->with('success', "Cotización convertida a venta #{$venta->id}");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al convertir: ' . $e->getMessage());
        }
    }

    public function pdf(Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items']);
        $cotizacione->calcularTotales();
        $pdf = \PDF::loadView('cotizaciones.pdf', compact('cotizacione'));
        return $pdf->stream("cotizacion-{$cotizacione->numero}.pdf");
    }

    public function ticket(Request $request, Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items']);
        return view('cotizaciones.ticket', [
            'cotizacion' => $cotizacione,
            'paperWidth' => (int) $request->input('paper', 80),
            'autoPrint'  => $request->boolean('autoprint', true),
        ]);
    }

    public function ticketText(Request $request, Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items']);
        $paperWidth = (int) $request->input('paper', 80);
        $format = $request->input('format', 'txt');
        $content = $this->printService->renderCotizacionTicket($cotizacione, $paperWidth);

        if ($format === 'escpos') {
            $content = $this->printService->toEscPos($content);
            $filename = "cotizacion-{$cotizacione->numero}.bin";
            $mimeType = 'application/octet-stream';
        } else {
            $filename = "cotizacion-{$cotizacione->numero}.txt";
            $mimeType = 'text/plain';
        }

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function buscarProductos(Request $request)
    {
        return response()->json($this->cotizacionService->buscarProductos($request->get('q', '')));
    }
}
