<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use App\Models\EcfLogEnvio;
use App\Models\SecuenciaEcf;
use App\Models\SystemSetting;
use App\Models\Venta;
use App\Models\Compra;
use App\Support\RncValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EcfService
{
    public function __construct(
        private EcfXmlBuilder $builder,
        private EcfDigitalSignature $signature,
        private DgiiConnector $dgii,
        private EcfQrGenerator $qr,
    ) {}

    public function generarEcf(Venta $venta, ?string $tipoEcfOverride = null): EcfDocumento
    {
        $tipoEcf = $tipoEcfOverride ?: $this->determinarTipoEcf($venta);
        $maxAttempts = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            DB::beginTransaction();
            try {
                $secuencia = $this->obtenerSecuenciaDisponible($tipoEcf);
                if (!$secuencia) {
                    throw new \RuntimeException("No hay secuencias e-CF activas para el tipo {$tipoEcf}");
                }

                $numero = $secuencia->getNextNumero();
                $encf = $secuencia->tipo_ecf . str_pad($numero, 10, '0', STR_PAD_LEFT);

                $totales = $this->calcularTotales($venta);

                $ecf = EcfDocumento::create([
                    'venta_id' => $venta->id,
                    'secuencia_ecf_id' => $secuencia->id,
                    'encf' => $encf,
                    'tipo_ecf' => $tipoEcf,
                    'estado' => 'borrador',
                    'fecha_emision' => now(),
                    'monto_gravado_total' => $totales['gravado'],
                    'monto_exento_total' => $totales['exento'],
                    'itbis_total' => $totales['itbis'],
                    'monto_total' => $totales['total'],
                    'codigo_seguridad' => null,
                    'usuario_id' => Auth::id(),
                ]);

            $ecf->load(['secuencia', 'venta.cliente', 'venta.detalles.producto']);

            $ecf->codigo_seguridad = $this->qr->generarCodigoSeguridad($ecf);
            $ecf->save();

            DB::commit();

            $this->log($ecf, 'crear', 'exito', 'e-CF generado en estado borrador');

            return $ecf->fresh();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'encf')) {
                Log::warning('e-CF: ENCF duplicado, reintentando', [
                    'venta_id' => $venta->id, 'encf' => $encf ?? '', 'attempt' => $attempt,
                ]);
                if ($attempt >= $maxAttempts) {
                    throw new \RuntimeException("No se pudo generar un ENCF único después de {$maxAttempts} intentos: " . $e->getMessage());
                }
                continue;
            }
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('e-CF: error al generar', ['venta_id' => $venta->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
    }

    public function firmar(EcfDocumento $ecf): EcfDocumento
    {
        $ecf = $ecf->fresh(['secuencia', 'venta.cliente', 'venta.detalles.producto']);
        $ecf->transicionarA('firmado');

        $xml = $this->builder->build($ecf);

        $this->validarXml($xml, $ecf);

        $cert = $ecf->certificado_digital_id
            ? \App\Models\CertificadoDigital::find($ecf->certificado_digital_id)
            : null;
        $result = $this->signature->sign($xml, $cert);

        $ecf->xml_content = $result['xml'];
        $ecf->firma_digital = $result['firma'];
        $ecf->fecha_firma = now();
        $ecf->estado = 'firmado';
        if ($cert) {
            $ecf->certificado_digital_id = $cert->id;
        }
        $ecf->save();

        $this->guardarXmlFirmado($ecf);

        $this->log($ecf, 'firmar', 'exito', "Firmado con {$result['metodo']} (algoritmo: {$result['algoritmo']})", null, json_encode(['metodo' => $result['metodo'], 'serial' => $result['serial']]));

        return $ecf;
    }

    public function enviar(EcfDocumento $ecf): EcfDocumento
    {
        $ecf = $ecf->fresh();
        if (!in_array($ecf->estado, ['generado', 'firmado', 'rechazado'], true)) {
            throw new \RuntimeException("El e-CF no está en un estado válido para envío (actual: {$ecf->estado})");
        }

        if (in_array($ecf->estado, ['borrador', 'generado']) || empty($ecf->xml_content)) {
            $ecf = $this->firmar($ecf);
        }

        $ecf->increment('intentos_envio');
        $ecf->transicionarA('enviado');
        $ecf->fecha_envio = now();
        $ecf->save();

        $start = microtime(true);

        try {
            $response = $this->dgii->enviar($ecf);
        } catch (\Throwable $e) {
            $this->log($ecf, 'enviar', 'error', 'Error de comunicación DGII: ' . $e->getMessage());
            throw $e;
        }

        $duration = (int) ((microtime(true) - $start) * 1000);

        $ecf->track_id_dgii = $response['track_id'] ?? null;
        $ecf->mensaje_dgii = $response['mensaje'] ?? null;

        if ($response['success'] && ($response['estado'] ?? '') === 'aprobado') {
            $ecf->transicionarA('aprobado');
            $ecf->fecha_aprobacion = now();
        } else {
            $ecf->transicionarA('rechazado');
        }
        $ecf->save();

        $this->log(
            $ecf,
            'enviar',
            $response['success'] ? 'exito' : 'error',
            $response['mensaje'] ?? '',
            $response['codigo_http'] ?? null,
            null,
            $response['response'] ?? null,
            $duration
        );

        if ($ecf->estado === 'aprobado' && $ecf->venta) {
            $ecf->venta->update(['encf' => $ecf->encf, 'tipo_comprobante' => 'ecf']);
        }

        return $ecf;
    }

    public function firmarYEnviar(EcfDocumento $ecf): EcfDocumento
    {
        $ecf = $this->firmar($ecf);
        return $this->enviar($ecf);
    }

    public function consultarEstado(EcfDocumento $ecf): EcfDocumento
    {
        if (empty($ecf->track_id_dgii)) {
            throw new \RuntimeException("El e-CF no ha sido enviado a DGII aún");
        }

        $response = $this->dgii->consultarEstado($ecf->track_id_dgii, $ecf);
        $this->log(
            $ecf,
            'consultar',
            $response['success'] ? 'exito' : 'error',
            $response['mensaje'] ?? '',
            $response['codigo_http'] ?? null,
            null,
            json_encode($response)
        );

        if (($response['estado'] ?? '') === 'aprobado' && $ecf->estado !== 'aprobado') {
            $ecf->transicionarA('aprobado');
            $ecf->fecha_aprobacion = now();
            $ecf->mensaje_dgii = $response['mensaje'];
            $ecf->save();
        }

        return $ecf;
    }

    public function anular(EcfDocumento $ecf, string $motivo): EcfDocumento
    {
        if (!$ecf->puedeAnular()) {
            throw new \RuntimeException("Solo se pueden anular e-CF aprobados (actual: {$ecf->estado})");
        }

        DB::beginTransaction();
        try {
            $ecf->transicionarA('anulado', function ($doc) use ($motivo) {
                $doc->fecha_anulacion = now();
                $doc->motivo_anulacion = $motivo;
            });

            $nc = $this->generarNotaCredito($ecf, $motivo);
            $ecf->nota_credito_id = $nc->id;
            $ecf->save();

            DB::commit();

            $this->log($ecf, 'anular', 'exito', "Anulado: {$motivo}. Nota de Crédito E34: {$nc->encf}");

            return $ecf->fresh('notaCredito');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('e-CF: error al anular', ['ecf_id' => $ecf->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function generarNotaDebito(EcfDocumento $original, float $montoAdicional, string $motivo): EcfDocumento
    {
        $secuencia = $this->obtenerSecuenciaDisponible('E33');
        if (!$secuencia) {
            throw new \RuntimeException("No hay secuencias E33 disponibles para generar la Nota de Débito");
        }

        $numero = $secuencia->getNextNumero();
        $encf = 'E33' . str_pad($numero, 10, '0', STR_PAD_LEFT);

        $porcentajeGravado = $original->monto_total > 0
            ? $original->monto_gravado_total / $original->monto_total
            : 0;
        $gravadoAdicional = round($montoAdicional * $porcentajeGravado, 2);
        $exentoAdicional = round($montoAdicional * (1 - $porcentajeGravado), 2);
        $itbisAdicional = round($gravadoAdicional * 0.18, 2);

        $nc = EcfDocumento::create([
            'venta_id' => $original->venta_id,
            'secuencia_ecf_id' => $secuencia->id,
            'encf' => $encf,
            'tipo_ecf' => 'E33',
            'estado' => 'borrador',
            'fecha_emision' => now(),
            'monto_gravado_total' => $gravadoAdicional,
            'monto_exento_total' => $exentoAdicional,
            'itbis_total' => $itbisAdicional,
            'monto_total' => $montoAdicional,
            'codigo_seguridad' => null,
            'documento_original_id' => $original->id,
            'usuario_id' => Auth::id(),
            'motivo_anulacion' => $motivo,
        ]);

        $nc->load('secuencia', 'venta.cliente', 'venta.detalles.producto');
        $nc->codigo_seguridad = $this->qr->generarCodigoSeguridad($nc);
        $nc->save();

        try {
            $nc = $this->firmar($nc);
            $nc = $this->enviar($nc);
        } catch (\Throwable $e2) {
            Log::warning('e-CF: ND E33 generada pero falló firma/envío', [
                'nc_id' => $nc->id,
                'error' => $e2->getMessage(),
            ]);
        }

        $this->log($nc, 'crear', 'exito', "Nota de Débito E33 generada sobre {$original->encf}: {$motivo}");

        return $nc;
    }

    public function generarNotaCredito(EcfDocumento $original, string $motivo): EcfDocumento
    {
        $secuencia = $this->obtenerSecuenciaDisponible('E34');
        if (!$secuencia) {
            throw new \RuntimeException("No hay secuencias E34 disponibles para generar la Nota de Crédito");
        }

        $numero = $secuencia->getNextNumero();
        $encf = 'E34' . str_pad($numero, 10, '0', STR_PAD_LEFT);

        $nc = EcfDocumento::create([
            'venta_id' => $original->venta_id,
            'secuencia_ecf_id' => $secuencia->id,
            'encf' => $encf,
            'tipo_ecf' => 'E34',
            'estado' => 'borrador',
            'fecha_emision' => now(),
            'monto_gravado_total' => $original->monto_gravado_total,
            'monto_exento_total' => $original->monto_exento_total,
            'itbis_total' => $original->itbis_total,
            'monto_total' => $original->monto_total,
            'codigo_seguridad' => null,
            'documento_original_id' => $original->id,
            'usuario_id' => Auth::id(),
        ]);

        $nc->load('secuencia', 'venta.cliente', 'venta.detalles.producto');

        $nc->codigo_seguridad = $this->qr->generarCodigoSeguridad($nc);
        $nc->save();

        // Firmar y enviar la NC automáticamente
        try {
            $nc = $this->firmar($nc);
            $nc = $this->enviar($nc);
        } catch (\Throwable $e) {
            Log::warning('e-CF: NC E34 generada pero falló firma/envío', [
                'nc_id' => $nc->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->log($nc, 'crear', 'exito', "Nota de Crédito E34 generada por anulación de {$original->encf}: {$motivo}");

        return $nc;
    }

    public function generarE34PorDevolucion(EcfDocumento $original, \App\Models\Devolucion $devolucion): EcfDocumento
    {
        $secuencia = $this->obtenerSecuenciaDisponible('E34');
        if (!$secuencia) {
            throw new \RuntimeException("No hay secuencias E34 disponibles");
        }

        $numero = $secuencia->getNextNumero();
        $encf = 'E34' . str_pad($numero, 10, '0', STR_PAD_LEFT);
        $proporcion = $original->monto_total > 0 ? $devolucion->total / $original->monto_total : 0;

        $nc = EcfDocumento::create([
            'venta_id' => $original->venta_id,
            'secuencia_ecf_id' => $secuencia->id,
            'encf' => $encf,
            'tipo_ecf' => 'E34',
            'estado' => 'borrador',
            'fecha_emision' => now(),
            'monto_gravado_total' => round($original->monto_gravado_total * $proporcion, 2),
            'monto_exento_total' => round($original->monto_exento_total * $proporcion, 2),
            'itbis_total' => round($original->itbis_total * $proporcion, 2),
            'monto_total' => $devolucion->total,
            'codigo_seguridad' => null,
            'documento_original_id' => $original->id,
            'usuario_id' => Auth::id(),
        ]);

        $nc->load('secuencia', 'venta.cliente', 'venta.detalles.producto');
        $nc->codigo_seguridad = $this->qr->generarCodigoSeguridad($nc);
        $nc->save();

        try {
            $nc = $this->firmar($nc);
            $nc = $this->enviar($nc);
        } catch (\Throwable $e) {
            Log::warning('e-CF: E34 por devolución generada pero falló firma/envío', [
                'nc_id' => $nc->id, 'error' => $e->getMessage(),
            ]);
        }

        $this->log($nc, 'crear', 'exito', "E34 por devolución #{$devolucion->codigo} sobre {$original->encf}");

        return $nc;
    }

    public function generarE41(Compra $compra): EcfDocumento
    {
        $secuencia = $this->obtenerSecuenciaDisponible('E41');
        if (!$secuencia) {
            throw new \RuntimeException("No hay secuencias E41 disponibles");
        }

        $numero = $secuencia->getNextNumero();
        $encf = 'E41' . str_pad($numero, 10, '0', STR_PAD_LEFT);

        $proveedor = $compra->proveedor;
        $rncVal = new \App\Support\RncValidator();
        $tipoDoc = $proveedor?->rnc ? $rncVal->inferirTipo($proveedor->rnc) : 'RNC';

        $ecf = EcfDocumento::create([
            'venta_id' => null,
            'secuencia_ecf_id' => $secuencia->id,
            'encf' => $encf,
            'tipo_ecf' => 'E41',
            'estado' => 'borrador',
            'fecha_emision' => now(),
            'monto_gravado_total' => $compra->subtotal ?? 0,
            'monto_exento_total' => 0,
            'itbis_total' => $compra->itbis_total ?? 0,
            'monto_total' => $compra->total,
            'codigo_seguridad' => null,
            'usuario_id' => Auth::id(),
        ]);

        $ecf->load('secuencia');

        try {
            $xml = $this->xmlBuilder->buildE41($ecf, $compra);
            $ecf->xml_content = $xml;
            $ecf->save();

            $ecf->codigo_seguridad = $this->qr->generarCodigoSeguridad($ecf);
            $ecf->save();

            $ecf = $this->firmar($ecf);
            $ecf = $this->enviar($ecf);

            $compra->ecf_documento_id = $ecf->id;
            $compra->save();
        } catch (\Throwable $e) {
            Log::warning('e-CF: E41 generada pero falló firma/envío', [
                'ecf_id' => $ecf->id, 'error' => $e->getMessage(),
            ]);
        }

        $this->log($ecf, 'crear', 'exito', "E41 generada para compra #{$compra->id}");

        return $ecf;
    }

    public function determinarTipoEcf(Venta $venta): string
    {
        $cliente = $venta->cliente;
        $tipoCliente = $cliente?->tipo_cliente ?? 'consumo';
        return SecuenciaEcf::tiposParaCliente($tipoCliente);
    }

    public function validarRncCliente(Venta $venta): array
    {
        $cliente = $venta->cliente;
        if (!$cliente || empty($cliente->rnc_cedula)) {
            return ['valido' => true, 'mensaje' => 'Consumidor Final - sin validación RNC'];
        }

        $tipoDoc = $cliente->tipo_documento ?? RncValidator::inferirTipo($cliente->rnc_cedula);
        $valido = RncValidator::validar($cliente->rnc_cedula, $tipoDoc);

        return [
            'valido' => $valido,
            'rnc' => $cliente->rnc_cedula,
            'tipo' => $tipoDoc,
            'mensaje' => $valido ? 'Documento válido' : 'Documento inválido según algoritmo DGII',
        ];
    }

    private function obtenerSecuenciaDisponible(string $tipoEcf): ?SecuenciaEcf
    {
        return SecuenciaEcf::where('tipo_ecf', $tipoEcf)
            ->where('activo', true)
            ->where('fecha_vencimiento', '>=', now())
            ->whereColumn('actual', '<', 'hasta')
            ->orderBy('fecha_vencimiento')
            ->first();
    }

    private function calcularTotales(Venta $venta): array
    {
        $gravado = 0;
        $exento = 0;
        $itbis = 0;

        foreach ($venta->detalles as $detalle) {
            $producto = $detalle->producto;
            $itbisPorcentaje = (float) ($producto->itbis_porcentaje ?? 18);
            $subtotal = (float) $detalle->subtotal;

            if ($itbisPorcentaje > 0) {
                $gravado += $subtotal;
                $itbis += $subtotal * ($itbisPorcentaje / 100);
            } else {
                $exento += $subtotal;
            }
        }

        return [
            'gravado' => round($gravado, 2),
            'exento' => round($exento, 2),
            'itbis' => round($itbis, 2),
            'total' => round($gravado + $exento + $itbis, 2),
        ];
    }

    private function validarXml(string $xml, EcfDocumento $ecf): void
    {
        $xsdPath = storage_path('app/ecf/schema/DGII_ecf.xsd');
        $libDir = storage_path('app/ecf/schema');

        if (!file_exists($xsdPath)) {
            Log::info('e-CF: XSD schema no encontrado, saltando validación XML', ['path' => $xsdPath]);
            return;
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadXML($xml);

        $prev = set_error_handler(function () {}, E_WARNING);
        $valid = $dom->schemaValidate($xsdPath);
        set_error_handler($prev);

        if (!$valid) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $messages = array_map(fn($e) => trim($e->message), $errors);
            Log::warning('e-CF: XML no válido contra XSD', [
                'encf' => $ecf->encf,
                'errores' => $messages,
            ]);
            throw new \RuntimeException(
                "XML inválido contra esquema DGII:\n" . implode("\n", $messages)
            );
        }

        Log::info('e-CF: XML validado contra XSD correctamente', ['encf' => $ecf->encf]);
    }

    private function guardarXmlFirmado(EcfDocumento $ecf): void
    {
        $path = config('dgii.xml_storage_path');
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
        $filename = $ecf->encf . '.xml';
        $fullPath = $path . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($fullPath, $ecf->xml_content);
        $ecf->xml_path = 'ecf/xml/' . $filename;
        $ecf->save();
    }

    public function log(
        EcfDocumento $ecf,
        string $accion,
        string $estado,
        ?string $mensaje = null,
        ?int $codigoHttp = null,
        ?string $request = null,
        ?string $response = null,
        ?int $duracionMs = null
    ): EcfLogEnvio {
        return EcfLogEnvio::create([
            'ecf_documento_id' => $ecf->id,
            'accion' => $accion,
            'estado_resultado' => $estado,
            'codigo_http' => $codigoHttp,
            'request_payload' => $request,
            'response_payload' => $response,
            'mensaje' => $mensaje,
            'duracion_ms' => $duracionMs,
            'created_at' => now(),
        ]);
    }
}
