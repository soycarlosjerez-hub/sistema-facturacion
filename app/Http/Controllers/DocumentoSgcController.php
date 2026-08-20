<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentoSgcRequest;
use App\Http\Requests\StoreDocumentoProveedorRequest;
use App\Http\Requests\UpdateDocumentoSgcRequest;
use App\Models\DocumentoProveedor;
use App\Models\DocumentoSgc;
use App\Models\Proveedor;
use App\Services\DocumentoSgcService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoSgcController extends Controller
{
    public function __construct(
        protected DocumentoSgcService $sgcService
    ) {}

    public function dashboard()
    {
        $stats = $this->sgcService->stats();
        $documentos = DocumentoSgc::with('creador')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $proximoRevision = DocumentoSgc::proximoRevision(30)
            ->orderBy('fecha_revision', 'asc')
            ->limit(5)
            ->get();
        $pendientesRevision = DocumentoSgc::pendientesRevision()
            ->orderBy('fecha_revision', 'asc')
            ->limit(5)
            ->get();
        $documentosProvPendientes = \App\Models\DocumentoProveedor::whereNotIn('estado', ['verificado', 'por_cargar'])
            ->where(function ($q) {
                $q->whereNull('fechaVencimiento')
                  ->orWhere('fechaVencimiento', '<=', now()->toDateString());
            })
            ->with('proveedor', 'documentoSgc')
            ->limit(10)
            ->get();

        return view('sgc.dashboard', compact(
            'stats', 'documentos', 'proximoRevision',
            'pendientesRevision', 'documentosProvPendientes'
        ));
    }

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $estado = $request->input('estado');
        $incluirObsoletos = $request->boolean('incluir_obsoletos');

        $query = DocumentoSgc::query()->orderBy('created_at', 'desc');

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                  ->orWhere('titulo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        if ($categoria) {
            $query->categoria($categoria);
        }

        if ($estado) {
            if ($estado === 'vigente') {
                $query->vigentes();
            } elseif ($estado === 'obsoleto') {
                $query->porEstado($estado);
            } else {
                $query->where('estado', $estado);
            }
        }

        if (!$incluirObsoletos && !$estado) {
            $query->whereNotIn('estado', ['obsoleto', 'archivado']);
        }

        $documentos = $query->with('creador', 'proveedor')->paginate(20)->withQueryString();

        return view('sgc.documentos.index', compact('documentos', 'buscar', 'categoria', 'estado'));
    }

    public function create()
    {
        $prooveedores = Proveedor::orderBy('nombre')->get();
        $categorias = [
            'politica' => 'Política',
            'trabajo_instructivo' => 'Trabajo/Instructivo',
            'procedimiento' => 'Procedimiento',
            'formulario' => 'Formulario',
            'registro' => 'Registro',
            'matriz' => 'Matriz',
            'reporte' => 'Reporte',
            'otro' => 'Otro',
        ];
        return view('sgc.documentos.create', compact('proveedores', 'categorias'));
    }

    public function store(StoreDocumentoSgcRequest $request)
    {
        try {
            $data = $request->validated();
            if (!empty($data['observaciones'])) {
                $data['_observaciones'] = $data['observaciones'];
                unset($data['observaciones']);
            }
            $this->sgcService->crear($data);
            return redirect()->route('sgc.documentos.index')->with('success', 'Documento creado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear el documento: ' . $e->getMessage());
        }
    }

    public function show(DocumentoSgc $documento)
    {
        $documento->load('creador', 'modificador', 'aprobador', 'proveedor', 'versiones.0');
        return view('sgc.documentos.show', compact('documento'));
    }

    public function edit(DocumentoSgc $documento)
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $categorias = [
            'politica' => 'Política',
            'trabajo_instructivo' => 'Trabajo/Instructivo',
            'procedimiento' => 'Procedimiento',
            'formulario' => 'Formulario',
            'registro' => 'Registro',
            'matriz' => 'Matriz',
            'reporte' => 'Reporte',
            'otro' => 'Otro',
        ];
        return view('sgc.documentos.edit', compact('documento', 'proveedores', 'categorias'));
    }

    public function update(UpdateDocumentoSgcRequest $request, DocumentoSgc $documento)
    {
        try {
            $data = $request->validated();
            if (!empty($data['observaciones'])) {
                $data['_observaciones'] = $data['observaciones'];
                unset($data['observaciones']);
            }
            $this->sgcService->actualizar($documento, $data);
            return redirect()->route('sgc.documentos.show', $documento)->with('success', 'Documento actualizado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_actualizacion', 'Error al actualizar el documento: ' . $e->getMessage());
        }
    }

    public function aprobar(DocumentoSgc $documento)
    {
        $this->sgcService->aprobar($documento);
        return back()->with('success', 'Documento aprobado exitosamente.');
    }

    public function rechazar(DocumentoSgc $documento)
    {
        $this->sgcService->rechazar($documento);
        return back()->with('success', 'Documento devuelto a borrador.');
    }

    public function marcarObsoleto(DocumentoSgc $documento)
    {
        $this->sgcService->marcarObsoleto($documento);
        return back()->with('success', 'Documento marcado como obsoleto.');
    }

    public function destroy(DocumentoSgc $documento)
    {
        try {
            $this->sgcService->eliminar($documento);
            return back()->with('success', 'Documento eliminado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }

    public function archivoShow(DocumentoSgc $documento, string $file = 'main')
    {
        if (!$documento->archivo_path) {
            abort(404, 'No hay archivo adjunto a este documento.');
        }
        return $this->sgcService->descargarArchivo($documento);
    }

    // -- Documentos de Proveedores --

    public function documentosProveedor(Proveedor $proveedor)
    {
        $documentos = $proveedor->documentosProveedores()
            ->with('documentoSgc', 'uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sgc.proveedores.documentos', compact('proveedor', 'documentos'));
    }

    public function crearDocumentoProveedor(Proveedor $proveedor, Request $request)
    {
        $documentosSgc = !$request->routeIs('sgc.documentos-proveedor.create')
            ? DocumentoSgc::where('estado', 'vigente')->orderBy('titulo')->get()
            : null;

        return view('sgc.proveedores.documentos.create', compact('proveedor', 'documentosSgc'));
    }

    public function storeDocumentoProveedor(StoreDocumentoProveedorRequest $request, Proveedor $proveedor)
    {
        $data = $request->validated();
        $this->sgcService->cargarDocumentoProveedor($proveedor, $data);
        return back()->with('success', 'Documento del proveedor cargado exitosamente.');
    }

    public function destroyDocumentoProveedor(DocumentoProveedor $document)
    {
        $this->sgcService->eliminarDocumentoProveedor($document);
        return back()->with('success', 'Documento del proveedor eliminado.');
    }

    public function archivoProveedor(DocumentoProveedor $document)
    {
        if (!$document->archivo_path) {
            abort(404, 'No hay archivo adjunto.');
        }
        return Storage::response($document->archivo_path, $document->archivo_original_name ?? 'documento_proveedor', [
            'Content-Type' => $document->archivo_mime_type ?? 'application/octet-stream',
        ]);
    }
}
