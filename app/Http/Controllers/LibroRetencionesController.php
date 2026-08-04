<?php

namespace App\Http\Controllers;

use App\Services\LibroRetencionesService;
use App\Exports\LibroRetencionesConsolidadoExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LibroRetencionesController extends Controller
{
    public function __construct(protected LibroRetencionesService $service) {}

    /**
     * Muestra el Libro de Retenciones Consolidado.
     */
    public function index(Request $request)
    {
        $data = $this->service->index($request);

        return view('libros-retenciones.index', $data);
    }

    /**
     * Exporta a Excel usando PhpSpreadsheet (Maatwebsite) con dos hojas.
     */
    public function exportExcel(Request $request)
    {
        $data = $this->service->exportData($request);
        $mes = $data['mes'];
        $anio = $data['anio'];

        return Excel::download(
            new LibroRetencionesConsolidadoExport($data),
            "libro_retenciones_{$anio}_{$mes}.xlsx"
        );
    }

    /**
     * Exporta a PDF.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->service->index($request);
        $mesNombre = \Carbon\Carbon::create($data['anio'], $data['mes'], 1)->format('F');

        $pdf = Pdf::loadView('libros-retenciones.pdf', $data)
            ->setPaper('letter', 'landscape');

        return $pdf->download("libro_retenciones_{$data['anio']}_{$data['mes']}.pdf");
    }
}
