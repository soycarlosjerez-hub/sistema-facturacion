<?php

namespace App\Services\Ecf;

use App\Models\CertificadoDigital;
use Illuminate\Support\Facades\Log;

class EcfDigitalSignature
{
    public function sign(string $xmlContent, ?CertificadoDigital $cert = null): array
    {
        $configAmb = config('dgii.ambiente');
        $isProduction = config('dgii.ambiente') === 'produccion';

        if (!$cert) {
            $cert = CertificadoDigital::vigentes()->orderBy('fecha_vencimiento', 'desc')->first();
        }

        if (!$cert || !$cert->vigente()) {
            Log::info('e-CF: sin certificado válido, usando firma simulada', ['ambiente' => $configAmb]);
            return $this->simularFirma($xmlContent);
        }

        if (config('dgii.simular_dgii') && !$isProduction) {
            return $this->simularFirma($xmlContent, $cert);
        }

        return $this->firmarConCertificadoReal($xmlContent, $cert);
    }

    private function firmarConCertificadoReal(string $xmlContent, CertificadoDigital $cert): array
    {
        $path = $cert->archivo_path;
        if (!file_exists($path)) {
            throw new \RuntimeException("Archivo de certificado no encontrado: {$path}");
        }

        $password = $cert->password;
        if (!$password) {
            throw new \RuntimeException("No se pudo descifrar la contraseña del certificado");
        }

        $pfxContent = file_get_contents($path);
        if (!$pfxContent) {
            throw new \RuntimeException("No se pudo leer el certificado");
        }

        $certs = [];
        if (!openssl_pkcs12_read($pfxContent, $certs, $password)) {
            throw new \RuntimeException("No se pudo leer el archivo PKCS#12. Contraseña incorrecta o archivo inválido.");
        }

        $privateKey = openssl_pkey_get_private($certs['pkey']);
        $certificate = openssl_x509_read($certs['cert']);

        if (!$privateKey || !$certificate) {
            throw new \RuntimeException("No se pudo obtener la clave privada o el certificado");
        }

        $certDetails = openssl_x509_parse($certificate);
        $serialNumber = $certDetails['serialNumber'] ?? null;
        $issuer = $certDetails['issuer']['CN'] ?? $certDetails['issuer']['O'] ?? 'Desconocido';

        $signature = '';
        if (!openssl_sign($xmlContent, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException("Error al firmar el documento");
        }

        $firmaBase64 = base64_encode($signature);
        $certBase64 = base64_encode($certs['cert']);

        $xmlFirmado = $this->insertarFirmaEnXml($xmlContent, [
            'firma' => $firmaBase64,
            'certificado' => $certBase64,
            'serial' => $serialNumber,
            'emisor' => $issuer,
            'algoritmo' => 'SHA256withRSA',
        ]);

        return [
            'xml' => $xmlFirmado,
            'firma' => $firmaBase64,
            'serial' => $serialNumber,
            'emisor' => $issuer,
            'algoritmo' => 'SHA256withRSA',
            'metodo' => 'openssl_real',
        ];
    }

    private function simularFirma(string $xmlContent, ?CertificadoDigital $cert = null): array
    {
        $timestamp = microtime(true);
        $hash = hash('sha256', $xmlContent . '|' . $timestamp . '|' . ($cert?->rnc_titular ?? 'DEMO'));

        $firmaBase64 = base64_encode($hash);
        $certBase64 = base64_encode('SIMULATED_CERT_' . ($cert?->rnc_titular ?? '000000000'));

        $xmlFirmado = $this->insertarFirmaEnXml($xmlContent, [
            'firma' => $firmaBase64,
            'certificado' => $certBase64,
            'serial' => 'SIM-' . strtoupper(substr($hash, 0, 16)),
            'emisor' => $cert?->emisor_cert ?? 'DGII Sandbox',
            'algoritmo' => 'SHA256-SIMULATED',
        ]);

        return [
            'xml' => $xmlFirmado,
            'firma' => $firmaBase64,
            'serial' => 'SIM-' . strtoupper(substr($hash, 0, 16)),
            'emisor' => $cert?->emisor_cert ?? 'DGII Sandbox',
            'algoritmo' => 'SHA256-SIMULATED',
            'metodo' => 'simulado',
        ];
    }

    private function insertarFirmaEnXml(string $xmlContent, array $signatureData): string
    {
        $xml = new \DOMDocument();
        $xml->loadXML($xmlContent);

        $root = $xml->documentElement;
        $signature = $xml->createElement('Signature');
        $signature->setAttribute('xmlns', 'http://www.w3.org/2000/09/xmldsig#');

        $signedInfo = $xml->createElement('SignedInfo');
        $signedInfo->appendChild($xml->createElement('CanonicalizationMethod', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315'));
        $signedInfo->appendChild($xml->createElement('SignatureMethod', $signatureData['algoritmo']));
        $reference = $xml->createElement('Reference', '#ECF');
        $digest = $xml->createElement('DigestMethod', $signatureData['algoritmo']);
        $digest->appendChild(new \DOMCdataSection(base64_encode(hash('sha256', $xmlContent, true))));
        $reference->appendChild($digest);
        $signedInfo->appendChild($reference);
        $signature->appendChild($signedInfo);

        $signature->appendChild($xml->createElement('SignatureValue', $signatureData['firma']));
        $keyInfo = $xml->createElement('KeyInfo');
        $x509Data = $xml->createElement('X509Data');
        $x509Data->appendChild($xml->createElement('X509Certificate', $signatureData['certificado']));
        $x509Data->appendChild($xml->createElement('X509SerialNumber', $signatureData['serial']));
        $x509Data->appendChild($xml->createElement('X509Issuer', $signatureData['emisor']));
        $keyInfo->appendChild($x509Data);
        $signature->appendChild($keyInfo);

        $root->appendChild($signature);

        return $xml->saveXML();
    }
}
