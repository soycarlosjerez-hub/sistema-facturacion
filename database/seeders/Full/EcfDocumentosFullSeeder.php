<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EcfDocumentosFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `ecf_documentos` (3 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('ecf_documentos');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('ecf_documentos')->truncate();

        $rows = [
            ['id' => 1, 'venta_id' => 37, 'secuencia_ecf_id' => 22, 'certificado_digital_id' => null, 'encf' => 'E320000000001', 'tipo_ecf' => 'E32', 'estado' => 'aprobado', 'fecha_emision' => '2026-07-12 23:52:25', 'fecha_firma' => '2026-07-12 23:52:25', 'fecha_envio' => '2026-07-12 23:52:25', 'fecha_aprobacion' => '2026-07-12 23:52:25', 'fecha_anulacion' => null, 'ultimo_informe_diario' => null, 'monto_gravado_total' => 490.0, 'monto_exento_total' => 0.0, 'itbis_total' => 88.2, 'monto_total' => 578.2, 'xml_path' => 'ecf/xml/E320000000001.xml', 'xml_archivado' => 0, 'xml_archivo_path' => null, 'xml_archivado_en' => null, 'xml_content' => '<?xml version="1.0" encoding="UTF-8"?>
<ECF xmlns="https://dgii.gov.do/onecore/electronicinvoice/v1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://dgii.gov.do/onecore/electronicinvoice/v1 DGII_ecf.xsd">
  <Encabezado>
    <Version>1.0</Version>
    <IdDoc>
      <TipoeCF>E32</TipoeCF>
      <eNCF>E320000000001</eNCF>
      <FechaVencimientoSecuencia>2031-07-12</FechaVencimientoSecuencia>
      <FechaEmision>2026-07-12</FechaEmision>
      <HoraEmision>23:52:25</HoraEmision>
    </IdDoc>
    <Emisor>
      <RNCEmisor>000000000</RNCEmisor>
      <RazonSocialEmisor>Michelle Casero y Gourmet</RazonSocialEmisor>
      <DireccionEmisor>Santo Domingo, R.D.</DireccionEmisor>
      <TelefonoEmisor>809-348-4259</TelefonoEmisor>
    </Emisor>
    <Comprador>
      <TipoDocumentoIdentificacionComprador>2</TipoDocumentoIdentificacionComprador>
      <RNCComprador>000-00000-0</RNCComprador>
      <RazonSocialComprador>Alfredo Armada</RazonSocialComprador>
      <EmailComprador>warcold@gmail.com</EmailComprador>
    </Comprador>
    <Totales>
      <MontoGravadoTotal>490.00</MontoGravadoTotal>
      <MontoExentoTotal>0.00</MontoExentoTotal>
      <ITBIS1>88.20</ITBIS1>
      <TotalITBIS>88.20</TotalITBIS>
      <MontoTotal>578.20</MontoTotal>
    </Totales>
  </Encabezado>
  <DetallesItems>
    <Item>
      <NumeroLinea>1</NumeroLinea>
      <CodigoItem>109</CodigoItem>
      <DescripcionItem>ALITAS A LA BARBACOA PICANTES</DescripcionItem>
      <CantidadItem>1.00</CantidadItem>
      <UnidadMedida>43</UnidadMedida>
      <PrecioUnitarioItem>490.00</PrecioUnitarioItem>
      <MontoItem>490.00</MontoItem>
      <MontoDescuento>0.00</MontoDescuento>
      <IndicadorFacturacion>1</IndicadorFacturacion>
      <TasaITBIS>18.00</TasaITBIS>
      <MontoITBIS>88.20</MontoITBIS>
    </Item>
  </DetallesItems>
  <FechaHoraFirma>2026-07-12T23:52:25</FechaHoraFirma>
<Signature xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod>http://www.w3.org/TR/2001/REC-xml-c14n-20010315</CanonicalizationMethod><SignatureMethod>SHA256-SIMULATED</SignatureMethod><Reference>#ECF<DigestMethod>SHA256-SIMULATED<![CDATA[mtsUdDhtvAx10JVcurqpSqM8KouNZ5F6wUD+H7Wmv/g=]]></DigestMethod></Reference></SignedInfo><SignatureValue>YjI0ZTY5OWFkNWZjMjdiYzQ0ZGJkMzViZWZhNGY0Mzk1ZGUxMGY3ZjA5YWQyNmVkOTU0MzcyZjBmYWZlNGIzOA==</SignatureValue><KeyInfo><X509Data><X509Certificate>U0lNVUxBVEVEX0NFUlRfMDAwMDAwMDAw</X509Certificate><X509SerialNumber>SIM-B24E699AD5FC27BC</X509SerialNumber><X509Issuer>DGII Sandbox</X509Issuer></X509Data></KeyInfo></Signature></ECF>
', 'firma_digital' => 'YjI0ZTY5OWFkNWZjMjdiYzQ0ZGJkMzViZWZhNGY0Mzk1ZGUxMGY3ZjA5YWQyNmVkOTU0MzcyZjBmYWZlNGIzOA==', 'codigo_seguridad' => 'DB502A', 'track_id_dgii' => 'TRK-1F3696D8DF4766A2', 'mensaje_dgii' => 'e-CF recibido y aceptado por DGII (simulación)', 'intentos_envio' => 1, 'motivo_anulacion' => null, 'nota_credito_id' => null, 'documento_original_id' => null, 'anulado_por_encf' => null, 'usuario_id' => 8, 'created_at' => '2026-07-12 21:52:25', 'updated_at' => '2026-07-12 21:52:25', 'tenant_id' => 2],
            ['id' => 2, 'venta_id' => 25, 'secuencia_ecf_id' => 22, 'certificado_digital_id' => null, 'encf' => 'E320000000002', 'tipo_ecf' => 'E32', 'estado' => 'aprobado', 'fecha_emision' => '2026-07-21 12:59:34', 'fecha_firma' => '2026-07-21 12:59:34', 'fecha_envio' => '2026-07-21 12:59:34', 'fecha_aprobacion' => '2026-07-21 12:59:34', 'fecha_anulacion' => null, 'ultimo_informe_diario' => null, 'monto_gravado_total' => 1078.0, 'monto_exento_total' => 0.0, 'itbis_total' => 194.04, 'monto_total' => 1272.04, 'xml_path' => 'ecf/xml/E320000000002.xml', 'xml_archivado' => 0, 'xml_archivo_path' => null, 'xml_archivado_en' => null, 'xml_content' => '<?xml version="1.0" encoding="UTF-8"?>
<ECF xmlns="https://dgii.gov.do/onecore/electronicinvoice/v1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://dgii.gov.do/onecore/electronicinvoice/v1 DGII_ecf.xsd">
  <Encabezado>
    <Version>1.0</Version>
    <IdDoc>
      <TipoeCF>E32</TipoeCF>
      <eNCF>E320000000002</eNCF>
      <FechaVencimientoSecuencia>2031-07-12</FechaVencimientoSecuencia>
      <FechaEmision>2026-07-21</FechaEmision>
      <HoraEmision>12:59:34</HoraEmision>
    </IdDoc>
    <Emisor>
      <RNCEmisor>000000000</RNCEmisor>
      <RazonSocialEmisor>Michelle Casero y Gourmet</RazonSocialEmisor>
      <DireccionEmisor>Santo Domingo, R.D.</DireccionEmisor>
      <TelefonoEmisor>809-348-4259</TelefonoEmisor>
    </Emisor>
    <Comprador>
      <TipoDocumentoIdentificacionComprador>2</TipoDocumentoIdentificacionComprador>
      <RNCComprador>000-00000-0</RNCComprador>
      <RazonSocialComprador>Consumidor Final</RazonSocialComprador>
    </Comprador>
    <Totales>
      <MontoGravadoTotal>1078.00</MontoGravadoTotal>
      <MontoExentoTotal>0.00</MontoExentoTotal>
      <ITBIS1>194.04</ITBIS1>
      <TotalITBIS>194.04</TotalITBIS>
      <MontoTotal>1272.04</MontoTotal>
    </Totales>
  </Encabezado>
  <DetallesItems>
    <Item>
      <NumeroLinea>1</NumeroLinea>
      <CodigoItem>141</CodigoItem>
      <DescripcionItem>PASTA CON CAMARONES</DescripcionItem>
      <CantidadItem>1.00</CantidadItem>
      <UnidadMedida>43</UnidadMedida>
      <PrecioUnitarioItem>693.00</PrecioUnitarioItem>
      <MontoItem>693.00</MontoItem>
      <MontoDescuento>0.00</MontoDescuento>
      <IndicadorFacturacion>1</IndicadorFacturacion>
      <TasaITBIS>18.00</TasaITBIS>
      <MontoITBIS>124.74</MontoITBIS>
    </Item>
    <Item>
      <NumeroLinea>2</NumeroLinea>
      <CodigoItem>115</CodigoItem>
      <DescripcionItem>CERDO AL LIMON</DescripcionItem>
      <CantidadItem>1.00</CantidadItem>
      <UnidadMedida>43</UnidadMedida>
      <PrecioUnitarioItem>385.00</PrecioUnitarioItem>
      <MontoItem>385.00</MontoItem>
      <MontoDescuento>0.00</MontoDescuento>
      <IndicadorFacturacion>1</IndicadorFacturacion>
      <TasaITBIS>18.00</TasaITBIS>
      <MontoITBIS>69.30</MontoITBIS>
    </Item>
  </DetallesItems>
  <FechaHoraFirma>2026-07-21T12:59:34</FechaHoraFirma>
<Signature xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod>http://www.w3.org/TR/2001/REC-xml-c14n-20010315</CanonicalizationMethod><SignatureMethod>SHA256-SIMULATED</SignatureMethod><Reference>#ECF<DigestMethod>SHA256-SIMULATED<![CDATA[RrTiMraosYnTTA94MLO+4jwgqHO5PF/DSHvP0aimiWQ=]]></DigestMethod></Reference></SignedInfo><SignatureValue>NjAxNzU2ZWI1YWM0OGFjMGY4NzIxYjdjNjMzZWEwZDVlMzI2Y2JmYzAxNWI1OWJjOTE4ZDRkMDg2ZDY4NmVkZg==</SignatureValue><KeyInfo><X509Data><X509Certificate>U0lNVUxBVEVEX0NFUlRfMDAwMDAwMDAw</X509Certificate><X509SerialNumber>SIM-601756EB5AC48AC0</X509SerialNumber><X509Issuer>DGII Sandbox</X509Issuer></X509Data></KeyInfo></Signature></ECF>
', 'firma_digital' => 'NjAxNzU2ZWI1YWM0OGFjMGY4NzIxYjdjNjMzZWEwZDVlMzI2Y2JmYzAxNWI1OWJjOTE4ZDRkMDg2ZDY4NmVkZg==', 'codigo_seguridad' => '46A054', 'track_id_dgii' => 'TRK-B3806D61B784FF10', 'mensaje_dgii' => 'e-CF recibido y aceptado por DGII (simulación)', 'intentos_envio' => 1, 'motivo_anulacion' => null, 'nota_credito_id' => null, 'documento_original_id' => null, 'anulado_por_encf' => null, 'usuario_id' => 8, 'created_at' => '2026-07-21 10:59:34', 'updated_at' => '2026-07-21 10:59:34', 'tenant_id' => 2],
            ['id' => 3, 'venta_id' => 54, 'secuencia_ecf_id' => 22, 'certificado_digital_id' => null, 'encf' => 'E320000000003', 'tipo_ecf' => 'E32', 'estado' => 'firmado', 'fecha_emision' => '2026-08-11 00:04:05', 'fecha_firma' => null, 'fecha_envio' => null, 'fecha_aprobacion' => null, 'fecha_anulacion' => null, 'ultimo_informe_diario' => null, 'monto_gravado_total' => 105.0, 'monto_exento_total' => 0.0, 'itbis_total' => 18.9, 'monto_total' => 123.9, 'xml_path' => null, 'xml_archivado' => 0, 'xml_archivo_path' => null, 'xml_archivado_en' => null, 'xml_content' => null, 'firma_digital' => null, 'codigo_seguridad' => '73745C', 'track_id_dgii' => null, 'mensaje_dgii' => null, 'intentos_envio' => 0, 'motivo_anulacion' => null, 'nota_credito_id' => null, 'documento_original_id' => null, 'anulado_por_encf' => null, 'usuario_id' => 28, 'created_at' => '2026-08-10 22:04:05', 'updated_at' => '2026-08-10 22:04:05', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('ecf_documentos')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
