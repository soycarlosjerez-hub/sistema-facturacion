<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EcfArchiveService
{
    public const RETENCION_ANIOS = 5;

    public const ARCHIVO_COMPRESION = 'zip';

    public function archivarDocumentosAnteriores(\DateTimeInterface $fechaLimite): array
    {
        $documentos = EcfDocumento::where('fecha_emision', '<', $fechaLimite)
            ->whereNotIn('estado', ['anulado', 'expirado'])
            ->whereNotNull('xml_content')
            ->where('xml_archivado', false)
            ->get();

        if ($documentos->isEmpty()) {
            return [
                'success' => true,
                'mensaje' => 'No hay documentos para archivar',
                'archivados' => 0,
            ];
        }

        $archivados = 0;
        $errores = 0;

        DB::beginTransaction();
        try {
            foreach ($documentos as $doc) {
                try {
                    $this->archivarIndividual($doc);
                    $archivados++;
                } catch (\Throwable $e) {
                    $errores++;
                    Log::error('e-CF: error archivando documento', [
                        'ecf_id' => $doc->id,
                        'encf' => $doc->encf,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('e-CF: error en transaccion de archivado masivo', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        Log::info('e-CF: archivado masivo completado', [
            'fecha_limite' => $fechaLimite->format('Y-m-d'),
            'archivados' => $archivados,
            'errores' => $errores,
        ]);

        return [
            'success' => true,
            'mensaje' => "Archivado completado: {$archivados} documentos, {$errores} errores",
            'archivados' => $archivados,
            'errores' => $errores,
        ];
    }

    public function descargarArchivoZip(string $anio, string $mes): ?string
    {
        $inicio = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth();
        $fin = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth();

        $documentos = EcfDocumento::whereBetween('fecha_emision', [$inicio, $fin])
            ->where('estado', 'aprobado')
            ->whereNotNull('xml_content')
            ->orderBy('fecha_emision')
            ->get();

        if ($documentos->isEmpty()) {
            return null;
        }

        $tempDir = sys_get_temp_dir() . '/ecf-archive-' . uniqid();
        @mkdir($tempDir, 0755, true);

        foreach ($documentos as $doc) {
            $filename = $doc->encf . '_' . $doc->fecha_emision->format('YmdHis') . '.xml';
            $filepath = $tempDir . DIRECTORY_SEPARATOR . $filename;
            file_put_contents($filepath, $doc->xml_content);
        }

        $zipPath = $tempDir . DIRECTORY_SEPARATOR . "ecf_{$anio}_{$mes}.zip";
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($documentos as $doc) {
                $filename = $doc->encf . '_' . $doc->fecha_emision->format('YmdHis') . '.xml';
                $filepath = $tempDir . DIRECTORY_SEPARATOR . $filename;
                $zip->addFile($filepath, $filename);
            }
            $zip->close();
        }

        $this->limpiarDirectorioTemporal($tempDir);

        return $zipPath;
    }

    public function limpiarArchivosAnterioresA(\DateTimeInterface $fechaLimite): array
    {
        $documentos = EcfDocumento::where('fecha_emision', '<', $fechaLimite)
            ->where('xml_archivado', true)
            ->get();

        $eliminados = 0;

        foreach ($documentos as $doc) {
            if ($doc->xml_path && Storage::disk('public')->exists($doc->xml_path)) {
                Storage::disk('public')->delete($doc->xml_path);
                $eliminados++;
            }
        }

        EcfDocumento::where('fecha_emision', '<', $fechaLimite)
            ->where('xml_archivado', true)
            ->update(['xml_content' => null, 'xml_path' => null]);

        return [
            'success' => true,
            'mensaje' => "Limpieza completada: {$eliminados} archivos eliminados",
            'eliminados' => $eliminados,
        ];
    }

    public function obtenerEstadisticasAlmacenamiento(): array
    {
        $totalDocs = EcfDocumento::count();
        $archivados = EcfDocumento::where('xml_archivado', true)->count();
        $noArchivados = EcfDocumento::where('xml_archivado', false)->whereNotNull('xml_content')->count();

        $tamanoEstimado = EcfDocumento::whereNotNull('xml_content')
            ->selectRaw('SUM(LENGTH(xml_content)) as tamano')
            ->value('tamano') ?? 0;

        $porAnio = EcfDocumento::selectRaw('YEAR(fecha_emision) as anio, COUNT(*) as cantidad')
            ->groupBy('anio')
            ->orderBy('anio', 'desc')
            ->get();

        return [
            'total_documentos' => $totalDocs,
            'archivados' => $archivados,
            'no_archivados' => $noArchivados,
            'tamano_estimado_bytes' => (int) $tamanoEstimado,
            'tamano_estimado_mb' => round((int) $tamanoEstimado / 1024 / 1024, 2),
            'por_anio' => $porAnio,
            'proximo_archivado_sugerido' => now()->modify('-' . (self::RETENCION_ANIOS - 1) . ' years')->format('Y-m-d'),
        ];
    }

    private function archivarIndividual(EcfDocumento $doc): void
    {
        if (!$doc->xml_content) {
            return;
        }

        $anio = $doc->fecha_emision->format('Y');
        $mes = $doc->fecha_emision->format('m');
        $carpeta = "archive/{$anio}/{$mes}";

        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta, 0755, true);
        }

        $filename = $doc->encf . '_' . $doc->fecha_emision->format('YmdHis') . '.xml';
        $path = $carpeta . DIRECTORY_SEPARATOR . $filename;

        Storage::disk('public')->put($path, $doc->xml_content);

        $doc->update([
            'xml_archivado' => true,
            'xml_archivo_path' => $path,
            'xml_archivado_en' => now(),
        ]);
    }

    private function limpiarDirectorioTemporal(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $filepath = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($filepath)) {
                @unlink($filepath);
            }
        }

        @rmdir($dir);
    }
}
