<?php

namespace App\Services;

use App\Models\DocumentoSgc;
use App\Models\DocumentoProveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentoSgcService
{
    public function crear(array $data): DocumentoSgc
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();

        $doc = DocumentoSgc::create($data);

        if ($this->manejarArchivo($data['archivo'] ?? null, $doc)) {
            $doc->load('creador', 'aprobador', 'proveedor');
        } else {
            $doc->load('creador', 'aprobador', 'proveedor');
        }

        return $doc;
    }

    public function actualizar(DocumentoSgc $doc, array $data): DocumentoSgc
    {
        $data['modificado_por'] = Auth::id();

        if (isset($data['archivo'])) {
            $this->manejarArchivo($data['archivo'], $doc);
            unset($data['archivo']);
        }

        $doc->update($data);
        $doc->load('modificador', 'aprobador', 'proveedor');

        return $doc;
    }

    public function aprobar(DocumentoSgc $doc): DocumentoSgc
    {
        $doc->update([
            'estado' => 'vigente',
            'aprobado_por' => Auth::id(),
        ]);
        $doc->load('aprobador');
        return $doc;
    }

    public function rechazar(DocumentoSgc $doc): DocumentoSgc
    {
        $doc->update(['estado' => 'borrador']);
        return $doc;
    }

    public function marcarObsoleto(DocumentoSgc $doc): DocumentoSgc
    {
        $doc->update(['estado' => 'obsoleto']);
        return $doc;
    }

    public function archivar(DocumentoSgc $doc): DocumentoSgc
    {
        $doc->update(['estado' => 'archivado']);
        return $doc;
    }

    public function eliminar(DocumentoSgc $doc): bool
    {
        $archivoPath = $doc->archivo_path;

        $doc->delete();

        if ($archivoPath) {
            Storage::disk('public')->delete($archivoPath);
        }

        return true;
    }

    public function descargarArchivo(DocumentoSgc $doc): ?\Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$doc->archivo_path) return null;
        return Storage::response($doc->archivo_path, $doc->archivo_original_name ?? $doc->codigo . '_' . $doc->version . '.' . $doc->formato, [
            'Content-Type' => $doc->archivo_mime_type ?? 'application/octet-stream',
        ]);
    }

    public function cargarDocumentoProveedor(Proveedor $proveedor, array $data): DocumentoProveedor
    {
        $data['subido_por'] = Auth::id();
        $data['tenant_id'] = Auth::user()->business_instance_id;

        $dp = DocumentoProveedor::create($data);

        if (isset($data['archivo'])) {
            $fileName = time() . '_' . Str::random(20) . '_' . Str::slug(pathinfo($data['archivo']->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $data['archivo']->getClientOriginalExtension();
            $path = $data['archivo']->storeAs('sgc_documentos_proveedores', $fileName, 'public');
            $dp->archivo_path = $path;
            $dp->archivo_original_name = $data['archivo']->getClientOriginalName();
            $dp->archivo_mime_type = $data['archivo']->getMimeType();
            $dp->archivo_size_bytes = $data['archivo']->getSize();
            $dp->saveQuietly();
        }

        $dp->load('proveedor', 'documentoSgc', 'uploader');
        return $dp;
    }

    public function eliminarDocumentoProveedor(DocumentoProveedor $dp): bool
    {
        if ($dp->archivo_path) {
            Storage::disk('public')->delete($dp->archivo_path);
        }
        $dp->delete();
        return true;
    }

    public function stats(): array
    {
        $total = DocumentoSgc::count();
        $vigentes = DocumentoSgc::vigentes()->count();
        $porCategoria = DocumentoSgc::selectRaw('categoria, COUNT(*) as total')
            ->groupBy('categoria')
            ->get()
            ->pluck('total', 'categoria')
            ->toArray();

        $pendientesRevision = DocumentoSgc::pendientesRevision()->count();
        $proximoRevision30 = DocumentoSgc::proximoRevision(30)->count();
        $proximoRevision90 = DocumentoSgc::proximoRevision(90)->count();

        $docProvVigentes = DocumentoProveedor::where('estado', 'vigente')->count();
        $docProvPendientes = DocumentoProveedor::where('estado', '!=', 'vigente')->whereNotIn('estado', ['por_cargar'])->count();

        return [
            'total' => $total,
            'vigentes' => $vigentes,
            'obsoletos' => DocumentoSgc::where('estado', 'obsoleto')->count(),
            'archivados' => DocumentoSgc::where('estado', 'archivado')->count(),
            'en_revision' => DocumentoSgc::where('estado', 'revision')->count(),
            'en_aprobacion' => DocumentoSgc::where('estado', 'aprobado')->count(),
            'borradores' => DocumentoSgc::where('estado', 'borrador')->count(),
            'por_categoria' => $porCategoria,
            'pendientes_revision' => $pendientesRevision,
            'proximo_revision_30' => $proximoRevision30,
            'proximo_revision_90' => $proximoRevision90,
            'documentos_proveedor_vigentes' => $docProvVigentes,
            'documentos_proveedor_pendientes' => $docProvPendientes,
            'categorias' => [
                'politica' => 'Política',
                'trabajo_instructivo' => 'Trabajo/Instructivo',
                'procedimiento' => 'Procedimiento',
                'formulario' => 'Formulario',
                'registro' => 'Registro',
                'matriz' => 'Matriz',
                'reporte' => 'Reporte',
                'otro' => 'Otro',
            ],
            'estados' => [
                'borrador' => 'Borrador',
                'revision' => 'En Revisión',
                'aprobado' => 'Aprobado',
                'vigente' => 'Vigente',
                'obsoleto' => 'Obsoleto',
                'archivado' => 'Archivado',
            ],
        ];
    }

    private function manejarArchivo($file, DocumentoSgc $doc): bool
    {
        if (!$file || !$file->isValid()) {
            return false;
        }

        if ($doc->archivo_path) {
            Storage::disk('public')->delete($doc->archivo_path);
        }

        $fileName = time() . '_' . Str::random(20) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('sgc_documentos', $fileName, 'public');

        $doc->archivo_path = $path;
        $doc->archivo_original_name = $file->getClientOriginalName();
        $doc->archivo_mime_type = $file->getMimeType();
        $doc->archivo_size_bytes = $file->getSize();
        $doc->checksum_sha256 = hash_file('sha256', $file->getRealPath());

        $doc->saveQuietly();

        return true;
    }
}
