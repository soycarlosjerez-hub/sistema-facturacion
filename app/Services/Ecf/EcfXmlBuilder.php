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
        $venta = Venta::with(['cliente', 'detalles.producto', 'usuario'])->findOrFail($ecf->venta_id);
        $empresa = SystemSetting::allCached();

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $root = $xml->createElementNS(
            'https://dgii.gov.do/onecore/electronicinvoice/v1',
            'ECF'
        );
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
        if ($ecf->tipo_ecf === 'E34' && $ecf->documento_original_id) {
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
        $idDoc->appendChild($xml->createElement('FechaVencimientoSecuencia', $ecf->secuencia->fecha_vencimiento->format('Y-m-d')));
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
        $tipoDoc = RncValidator::tipoDocumentoDgii($cliente->tipo_documento ?? null);
        $rncComprador = $this->cleanRnc($cliente->rnc_cedula ?? '');
        if ($ecf->tipo_ecf === 'E32' && $rncComprador === '') {
            $rncComprador = '000000000';
        }

        $comprador = $xml->createElement('Comprador');
        $comprador->appendChild($xml->createElement('TipoDocumentoIdentificacionComprador', $tipoDoc));
        $comprador->appendChild($xml->createElement('RNCComprador', RncValidator::formato($rncComprador, $cliente->tipo_documento ?? 'rnc')));
        $comprador->appendChild($xml->createElement('RazonSocialComprador', $cliente->nombre ?? 'Consumidor Final'));
        if ($cliente && !empty($cliente->email)) {
            $comprador->appendChild($xml->createElement('EmailComprador', $cliente->email));
        }
        $encabezado->appendChild($comprador);

        $totales = $xml->createElement('Totales');
        $totales->appendChild($xml->createElement('MontoGravadoTotal', $this->fmt((float)$ecf->monto_gravado_total)));
        $totales->appendChild($xml->createElement('MontoExentoTotal', $this->fmt((float)$ecf->monto_exento_total)));
        $totales->appendChild($xml->createElement('ITBIS1', $this->fmt((float)$ecf->itbis_total)));
        $totales->appendChild($xml->createElement('TotalITBIS', $this->fmt((float)$ecf->itbis_total)));
        $totales->appendChild($xml->createElement('MontoTotal', $this->fmt((float)$ecf->monto_total)));
        $encabezado->appendChild($totales);
    }

    private function appendDetalles(DOMDocument $xml, DOMElement $root, Venta $venta): void
    {
        $detalles = $xml->createElement('DetallesItems');
        $lineNum = 1;

        foreach ($venta->detalles as $detalle) {
            $producto = $detalle->producto;
            $cantidad = (float) $detalle->cantidad;
            $precioUnitario = (float) $detalle->precio_unitario;
            $itbisPorcentaje = (float) ($producto->itbis_porcentaje ?? 18);
            $subtotalBruto = $cantidad * $precioUnitario;
            $itbisItem = $subtotalBruto * ($itbisPorcentaje / 100);

            $item = $xml->createElement('Item');
            $item->appendChild($xml->createElement('NumeroLinea', (string) $lineNum++));
            $item->appendChild($xml->createElement('CodigoItem', $producto->codigo_barras ?? (string) $producto->id));
            $item->appendChild($xml->createElement('DescripcionItem', $producto->nombre));
            $item->appendChild($xml->createElement('CantidadItem', $this->fmt($cantidad)));
            $item->appendChild($xml->createElement('UnidadMedida', '43'));
            $item->appendChild($xml->createElement('PrecioUnitarioItem', $this->fmt($precioUnitario)));
            $item->appendChild($xml->createElement('MontoItem', $this->fmt($subtotalBruto)));
            $item->appendChild($xml->createElement('MontoDescuento', '0.00'));
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
        if (!$original) return;

        $ref = $xml->createElement('Referencia');
        $ref->appendChild($xml->createElement('NCF', $original->encf));
        $ref->appendChild($xml->createElement('Fecha', $original->fecha_emision->format('Y-m-d')));
        $ref->appendChild($xml->createElement('MontoTotal', $this->fmt((float)$original->monto_total)));
        $ref->appendChild($xml->createElement('MotivoAnulacion', $ecf->motivo_anulacion ?? 'Sin motivo'));
        $root->appendChild($ref);
    }

    public function buildE41(EcfDocumento $ecf, \App\Models\Compra $compra): string
    {
        $empresa = SystemSetting::allCached();
        $proveedor = $compra->proveedor;
        $rncVal = new RncValidator();
        $tipoDocProveedor = $proveedor?->rnc ? $rncVal->inferirTipo($proveedor->rnc) : 'RNC';

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $root = $xml->createElementNS(
            'https://dgii.gov.do/onecore/electronicinvoice/v1',
            'ECF'
        );
        $xml->appendChild($root);

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $root->setAttribute('xsi:schemaLocation',
            'https://dgii.gov.do/onecore/electronicinvoice/v1 DGII_ecf.xsd'
        );

        // Encabezado
        $idDoc = $xml->createElement('IdDoc');
        $idDoc->appendChild($xml->createElement('TipoCF', 'E41'));
        $idDoc->appendChild($xml->createElement('EcfNumero', $ecf->encf));
        $idDoc->appendChild($xml->createElement('FechaEmision', $ecf->fecha_emision->format('Y-m-d')));
        $idDoc->appendChild($xml->createElement('HoraEmision', $ecf->fecha_emision->format('H:i:s')));
        $root->appendChild($idDoc);

        // Emisor (la empresa)
        $emisor = $xml->createElement('Emisor');
        $emisor->appendChild($xml->createElement('RNC', $this->cleanRnc($empresa['rnc_empresa'] ?? '')));
        $emisor->appendChild($xml->createElement('RazonSocial', $empresa['nombre_empresa'] ?? ''));
        $emisor->appendChild($xml->createElement('NombreComercial', $empresa['nombre_empresa'] ?? ''));
        $dir = $xml->createElement('DireccionEmisor');
        $dir->appendChild($xml->createElement('Direccion', $empresa['direccion'] ?? ''));
        $dir->appendChild($xml->createElement('Municipio', $empresa['ciudad'] ?? ''));
        $dir->appendChild($xml->createElement('Provincia', ''));
        $emisor->appendChild($dir);
        $emisor->appendChild($xml->createElement('Telefono', $empresa['telefono'] ?? ''));
        $emisor->appendChild($xml->createElement('CorreoElectronico', $empresa['email'] ?? ''));
        $root->appendChild($emisor);

        // Proveedor (vendedor)
        $comprador = $xml->createElement('Comprador');
        $tipoDoc = $tipoDocProveedor === 'CEDULA' ? 'Cedula' : ($tipoDocProveedor === 'RNC' ? 'RNC' : 'Otro');
        $comprador->appendChild($xml->createElement('TipoDocumento', $tipoDoc));
        $comprador->appendChild($xml->createElement('Documento', $this->cleanRnc($proveedor?->rnc ?? '')));
        $comprador->appendChild($xml->createElement('Nombre', $proveedor?->nombre ?? 'Proveedor'));
        $comprador->appendChild($xml->createElement('Direccion', $proveedor?->direccion ?? ''));
        $comprador->appendChild($xml->createElement('CorreoElectronico', $proveedor?->email ?? ''));
        $root->appendChild($comprador);

        // Detalles
        $detalles = $xml->createElement('DetallesFactura');
        foreach ($compra->detalles as $i => $det) {
            $item = $xml->createElement('Item');
            $item->appendChild($xml->createElement('NumeroLinea', $i + 1));
            $item->appendChild($xml->createElement('Descripcion', $det->producto?->nombre ?? 'Producto'));
            $item->appendChild($xml->createElement('Cantidad', $this->fmt($det->cantidad)));
            $item->appendChild($xml->createElement('PrecioUnitario', $this->fmt($det->precio_unitario)));
            $base = $det->cantidad * $det->precio_unitario;
            $imp = $base * ($det->itbis_porcentaje ?? 18) / 100;
            $item->appendChild($xml->createElement('MontoGravado', $this->fmt($base)));
            $item->appendChild($xml->createElement('MontoItbis', $this->fmt($imp)));
            $detalles->appendChild($item);
        }
        $root->appendChild($detalles);

        // Totales
        $totales = $xml->createElement('Totales');
        $totales->appendChild($xml->createElement('TotalGravado', $this->fmt($compra->subtotal ?? 0)));
        $totales->appendChild($xml->createElement('TotalItbis', $this->fmt($compra->itbis_total ?? 0)));
        $totales->appendChild($xml->createElement('Total', $this->fmt($compra->total)));
        $root->appendChild($totales);

        // CodigoSeguridad
        $root->appendChild($xml->createElement('CodigoSeguridad', $ecf->codigo_seguridad ?? ''));

        $this->appendFechaHoraFirma($xml, $root, $ecf);

        return $xml->saveXML();
    }

    private function appendFechaHoraFirma(DOMDocument $xml, DOMElement $root, EcfDocumento $ecf): void
    {
        $fecha = $ecf->fecha_firma ?? $ecf->fecha_emision;
        $root->appendChild($xml->createElement('FechaHoraFirma', $fecha->format('Y-m-d\TH:i:s')));
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
