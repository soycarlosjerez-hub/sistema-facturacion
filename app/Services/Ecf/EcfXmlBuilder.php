<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use App\Models\SystemSetting;
use App\Models\Venta;
use App\Support\RncValidator;
use DOMDocument;
use DOMElement;

class EcfXmlBuilder
{
    public function build(EcfDocumento $ecf): string
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'detalles.obra', 'usuario', 'pagos'])->findOrFail($ecf->venta_id);
        $empresa = SystemSetting::allCached();

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $root = $xml->createElementNS(
            'https://dgii.gov.do/onecore/electronicinvoice/v1',
            'ECF'
        );
        $root->setAttribute('id', 'ECF');
        $xml->appendChild($root);

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $root->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'https://dgii.gov.do/onecore/electronicinvoice/v1 DGII_ecf.xsd'
        );

        $this->appendEncabezado($xml, $root, $ecf, $venta, $empresa);
        $this->appendDetalles($xml, $root, $venta);
        if (in_array($ecf->tipo_ecf, ['E33', 'E34'], true) && $ecf->documento_original_id) {
            $this->appendReferenciaE34($xml, $root, $ecf);
        }
        $this->appendFechaHoraFirma($xml, $root, $ecf);

        return $xml->saveXML();
    }

    private function appendEncabezado(DOMDocument $xml, DOMElement $root, EcfDocumento $ecf, Venta $venta, array $empresa): void
    {
        $encabezado = $xml->createElement('Encabezado');
        $root->appendChild($encabezado);

        $encabezado->appendChild($xml->createElement('Version', '1.0'));

        $idDoc = $xml->createElement('IdDoc');
        $idDoc->appendChild($xml->createElement('TipoeCF', $ecf->tipo_ecf));
        $idDoc->appendChild($xml->createElement('eNCF', $ecf->encf));
        $idDoc->appendChild($xml->createElement('FechaVencimientoSecuencia', $ecf->secuencia?->fecha_vencimiento?->format('Y-m-d') ?? ''));
        $idDoc->appendChild($xml->createElement('FechaEmision', $ecf->fecha_emision->format('Y-m-d')));
        $idDoc->appendChild($xml->createElement('HoraEmision', $ecf->fecha_emision->format('H:i:s')));
        $encabezado->appendChild($idDoc);

        $emisor = $xml->createElement('Emisor');
        $emisor->appendChild($xml->createElement('RNCEmisor', $empresa['empresa_rnc'] ?? '000000000'));
        $emisor->appendChild($xml->createElement('RazonSocialEmisor', $empresa['empresa_nombre'] ?? 'EMPRESA DEMO SRL'));
        $emisor->appendChild($xml->createElement('DireccionEmisor', $empresa['empresa_direccion'] ?? 'Santo Domingo, R.D.'));
        $emisor->appendChild($xml->createElement('TelefonoEmisor', $empresa['empresa_telefono'] ?? '809-000-0000'));
        $encabezado->appendChild($emisor);

        $cliente = $venta->cliente;
        if (! $cliente) {
            if ($ecf->tipo_ecf === 'E32') {
                $cliente = (object) [
                    'tipo_documento' => null,
                    'rnc_cedula' => '',
                    'nombre' => 'Consumidor Final',
                    'email' => null,
                ];
            } else {
                throw new \RuntimeException('La venta debe tener un cliente asociado para generar el e-CF');
            }
        }
        $tipoDoc = RncValidator::tipoDocumentoDgii($cliente->tipo_documento ?? null);
        $rncComprador = $this->cleanRnc($cliente->rnc_cedula ?? '');
        if ($ecf->tipo_ecf === 'E32' && $rncComprador === '') {
            $rncComprador = '000000000';
        }

        $comprador = $xml->createElement('Comprador');
        $comprador->appendChild($xml->createElement('TipoDocumentoIdentificacionComprador', $tipoDoc));
        $comprador->appendChild($xml->createElement('RNCComprador', RncValidator::formato($rncComprador, $cliente->tipo_documento ?? 'rnc')));
        $comprador->appendChild($xml->createElement('RazonSocialComprador', $cliente->nombre ?? 'Consumidor Final'));
        if ($cliente && ! empty($cliente->email)) {
            $comprador->appendChild($xml->createElement('EmailComprador', $cliente->email));
        }
        $encabezado->appendChild($comprador);

        $totales = $xml->createElement('Totales');
        $totales->appendChild($xml->createElement('MontoGravadoTotal', $this->fmt((float) $ecf->monto_gravado_total)));
        $totales->appendChild($xml->createElement('MontoExentoTotal', $this->fmt((float) $ecf->monto_exento_total)));
        $totales->appendChild($xml->createElement('ITBIS1', $this->fmt((float) $ecf->itbis_total)));
        $totales->appendChild($xml->createElement('TotalITBIS', $this->fmt((float) $ecf->itbis_total)));
        $retenciones = $this->parseRetenciones($venta);
        $totales->appendChild($xml->createElement('TotalITBISRetenido', $this->fmt($retenciones['itbis_retenido'])));
        $totales->appendChild($xml->createElement('TotalISRRetencion', $this->fmt($retenciones['isr_retenido'])));
        $totales->appendChild($xml->createElement('MontoTotal', $this->fmt((float) $ecf->monto_total)));
        if ((float) ($venta->propina ?? 0) > 0) {
            $totales->appendChild($xml->createElement('Propina', $this->fmt((float) $venta->propina)));
        }
        $encabezado->appendChild($totales);

        $this->appendFormaDePago($xml, $encabezado, $venta);
    }

    private function appendDetalles(DOMDocument $xml, DOMElement $root, Venta $venta): void
    {
        $detalles = $xml->createElement('DetallesItems');
        $lineNum = 1;

        foreach ($venta->detalles as $detalle) {
            $producto = $detalle->producto;
            $obra = $detalle->obra;
            $cantidad = (float) $detalle->cantidad;
            $precioUnitario = (float) $detalle->precio_unitario;
            $itbisPorcentaje = (float) ($detalle->itbis_porcentaje ?? $producto->itbis_porcentaje ?? SystemSetting::itbisDefault());
            $subtotalBruto = $cantidad * $precioUnitario;
            $descuentoLinea = $this->calcularDescuentoLinea($detalle, $subtotalBruto);
            $baseImponible = max(0, $subtotalBruto - $descuentoLinea);
            $itbisItem = $baseImponible * ($itbisPorcentaje / 100);

            $item = $xml->createElement('Item');
            $item->appendChild($xml->createElement('NumeroLinea', (string) $lineNum++));
            $item->appendChild($xml->createElement('CodigoItem', $obra ? 'OBRA-'.$obra->id : ($producto->codigo_barras ?? $producto->codigo_referencia ?? (string) $producto->id)));
            $item->appendChild($xml->createElement('DescripcionItem', $obra?->titulo ?? $producto->nombre));
            $item->appendChild($xml->createElement('CantidadItem', $this->fmt($cantidad)));
            $item->appendChild($xml->createElement('UnidadMedida', $producto->unidad_medida ?? '43'));
            $item->appendChild($xml->createElement('PrecioUnitarioItem', $this->fmt($precioUnitario)));
            $item->appendChild($xml->createElement('MontoItem', $this->fmt($baseImponible)));
            $item->appendChild($xml->createElement('MontoDescuento', $this->fmt($descuentoLinea)));
            $item->appendChild($xml->createElement('IndicadorFacturacion', $itbisPorcentaje > 0 ? '1' : '2'));
            $item->appendChild($xml->createElement('TasaITBIS', $this->fmt($itbisPorcentaje)));
            $item->appendChild($xml->createElement('MontoITBIS', $this->fmt($itbisItem)));

            $detalles->appendChild($item);
        }

        $root->appendChild($detalles);
    }

    private function appendReferenciaE34(DOMDocument $xml, DOMElement $root, EcfDocumento $ecf): void
    {
        $original = $ecf->documentoOriginal;
        if (! $original) {
            return;
        }

        $ref = $xml->createElement('Referencia');
        $ref->appendChild($xml->createElement('NCFModificado', $original->encf));
        $ref->appendChild($xml->createElement('NCF', $original->encf));
        $ref->appendChild($xml->createElement('Fecha', $original->fecha_emision->format('Y-m-d')));
        $ref->appendChild($xml->createElement('MontoTotal', $this->fmt((float) $original->monto_total)));
        $ref->appendChild($xml->createElement('MotivoAnulacion', $ecf->motivo_anulacion ?? 'Sin motivo'));
        $root->appendChild($ref);
    }

    public function buildE41(EcfDocumento $ecf, \App\Models\Compra $compra): string
    {
        $empresa = SystemSetting::allCached();
        $proveedor = $compra->proveedor;
        $tipoDocProveedor = $proveedor?->rnc ? RncValidator::inferirTipo($proveedor->rnc) : 'rnc';

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $root = $xml->createElementNS(
            'https://dgii.gov.do/onecore/electronicinvoice/v1',
            'ECF'
        );
        $root->setAttribute('id', 'ECF');
        $xml->appendChild($root);

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $root->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'https://dgii.gov.do/onecore/electronicinvoice/v1 DGII_ecf.xsd'
        );

        // Encabezado (mismo formato que E31/E32)
        $encabezado = $xml->createElement('Encabezado');
        $root->appendChild($encabezado);

        $encabezado->appendChild($xml->createElement('Version', '1.0'));

        $idDoc = $xml->createElement('IdDoc');
        $idDoc->appendChild($xml->createElement('TipoeCF', 'E41'));
        $idDoc->appendChild($xml->createElement('eNCF', $ecf->encf));
        $idDoc->appendChild($xml->createElement('FechaVencimientoSecuencia', $ecf->secuencia?->fecha_vencimiento?->format('Y-m-d') ?? ''));
        $idDoc->appendChild($xml->createElement('FechaEmision', $ecf->fecha_emision->format('Y-m-d')));
        $idDoc->appendChild($xml->createElement('HoraEmision', $ecf->fecha_emision->format('H:i:s')));
        $encabezado->appendChild($idDoc);

        // Emisor (la empresa compradora)
        $emisor = $xml->createElement('Emisor');
        $emisor->appendChild($xml->createElement('RNCEmisor', $this->cleanRnc($empresa['empresa_rnc'] ?? '')));
        $emisor->appendChild($xml->createElement('RazonSocialEmisor', $empresa['empresa_nombre'] ?? ''));
        $emisor->appendChild($xml->createElement('DireccionEmisor', $empresa['empresa_direccion'] ?? ''));
        $emisor->appendChild($xml->createElement('TelefonoEmisor', $empresa['empresa_telefono'] ?? ''));
        $encabezado->appendChild($emisor);

        // Proveedor (vendedor) como Comprador
        $tipoDoc = RncValidator::tipoDocumentoDgii($tipoDocProveedor);
        $rncProveedor = $this->cleanRnc($proveedor?->rnc ?? '');

        $comprador = $xml->createElement('Comprador');
        $comprador->appendChild($xml->createElement('TipoDocumentoIdentificacionComprador', $tipoDoc));
        $comprador->appendChild($xml->createElement('RNCComprador', RncValidator::formato($rncProveedor, $tipoDocProveedor)));
        $comprador->appendChild($xml->createElement('RazonSocialComprador', $proveedor?->nombre ?? 'Proveedor'));
        if ($proveedor && ! empty($proveedor->email)) {
            $comprador->appendChild($xml->createElement('EmailComprador', $proveedor->email));
        }
        $encabezado->appendChild($comprador);

        // Totales
        $totales = $xml->createElement('Totales');
        $totales->appendChild($xml->createElement('MontoGravadoTotal', $this->fmt((float) ($compra->subtotal ?? 0))));
        $totales->appendChild($xml->createElement('MontoExentoTotal', $this->fmt(0)));
        $totales->appendChild($xml->createElement('ITBIS1', $this->fmt((float) ($compra->itbis_total ?? 0))));
        $totales->appendChild($xml->createElement('TotalITBIS', $this->fmt((float) ($compra->itbis_total ?? 0))));
        $totales->appendChild($xml->createElement('MontoTotal', $this->fmt((float) $compra->total)));
        $encabezado->appendChild($totales);

        // Detalles (mismo formato que ventas)
        $detalles = $xml->createElement('DetallesItems');
        foreach ($compra->detalles as $i => $det) {
            $item = $xml->createElement('Item');
            $item->appendChild($xml->createElement('NumeroLinea', $i + 1));
            $item->appendChild($xml->createElement('CodigoItem', $det->producto?->codigo_barras ?? (string) $det->producto_id));
            $item->appendChild($xml->createElement('DescripcionItem', $det->producto?->nombre ?? 'Producto'));
            $item->appendChild($xml->createElement('CantidadItem', $this->fmt($det->cantidad)));
            $item->appendChild($xml->createElement('UnidadMedida', '43'));
            $item->appendChild($xml->createElement('PrecioUnitarioItem', $this->fmt($det->precio_unitario)));
            $base = $det->cantidad * $det->precio_unitario;
            $item->appendChild($xml->createElement('MontoItem', $this->fmt($base)));
            $item->appendChild($xml->createElement('MontoDescuento', '0.00'));
            $item->appendChild($xml->createElement('IndicadorFacturacion', '1'));
            $item->appendChild($xml->createElement('TasaITBIS', $this->fmt($det->itbis_porcentaje ?? SystemSetting::itbisDefault())));
            $item->appendChild($xml->createElement('MontoITBIS', $this->fmt($base * ($det->itbis_porcentaje ?? SystemSetting::itbisDefault()) / 100)));
            $detalles->appendChild($item);
        }
        $root->appendChild($detalles);

        $this->appendFechaHoraFirma($xml, $root, $ecf);

        return $xml->saveXML();
    }

    private function appendFechaHoraFirma(DOMDocument $xml, DOMElement $root, EcfDocumento $ecf): void
    {
        $fecha = $ecf->fecha_firma ?? $ecf->fecha_emision;
        $root->appendChild($xml->createElement('FechaHoraFirma', $fecha->format('Y-m-d\TH:i:s')));
    }

    /**
     * Descuento de línea: soporta monto fijo o porcentaje (detalle.descuento_tipo).
     */
    private function calcularDescuentoLinea($detalle, float $subtotalBruto): float
    {
        $descuento = (float) ($detalle->descuento ?? 0);
        if ($descuento <= 0) {
            return 0.0;
        }

        if (($detalle->descuento_tipo ?? 'monto') === 'porcentaje') {
            return round($subtotalBruto * ($descuento / 100), 2);
        }

        return min($descuento, $subtotalBruto);
    }

    /**
     * Retenciones de la venta (columna JSON `retenciones`):
     * ['itbis_retenido' => x, 'isr_retenido' => y]. Defaults 0.
     */
    private function parseRetenciones(Venta $venta): array
    {
        $raw = $venta->retenciones;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }

        return [
            'itbis_retenido' => (float) ($raw['itbis_retenido'] ?? $raw['itbis'] ?? 0),
            'isr_retenido' => (float) ($raw['isr_retenido'] ?? $raw['isr'] ?? 0),
        ];
    }

    /**
     * Forma de pago según pagos registrados. Tabla DGII:
     * 1=efectivo, 2=cheque/transferencia, 3=tarjeta, 4=compra a crédito,
     * 5=permuta, 6=nota de crédito, 7=mixto.
     */
    private function appendFormaDePago(DOMDocument $xml, DOMElement $encabezado, Venta $venta): void
    {
        $map = [
            'efectivo' => '1',
            'transferencia' => '2',
            'tarjeta' => '3',
            'fiado' => '4',
            'cuenta_abierta' => '4',
            'mixto' => '7',
        ];

        $metodos = $venta->pagos->pluck('metodo_pago')->filter()->unique()->values();
        if ($metodos->isEmpty()) {
            return;
        }

        $codigo = $metodos->count() > 1
            ? '7'
            : ($map[strtolower((string) $metodos->first())] ?? '1');

        $encabezado->appendChild($xml->createElement('FormaDePago', $codigo));
    }

    private function cleanRnc(?string $rnc): string
    {
        return preg_replace('/[^0-9]/', '', (string) $rnc);
    }

    private function fmt(float $num): string
    {
        return number_format($num, 2, '.', '');
    }
}
