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
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = false;
        $xml->loadXML($xmlContent);

        $xpath = new \DOMXPath($xml);
        $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

        $elementosEcf = $xpath->query('//ecf:ECF') ?: $xpath->query('//ECF');
        $ecfNode = $elementosEcf->item(0);
        
        if (!$ecfNode) {
            $ecfNode = $xml->documentElement;
        }

        $xmlContentParaHash = $xml->saveXML($ecfNode);
        $digestValue = base64_encode(hash('sha256', $xmlContentParaHash, true));

        $signature = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Signature');

        $signedInfo = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignedInfo');

        $canonicalizationMethod = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:CanonicalizationMethod');
        $canonicalizationMethod->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfo->appendChild($canonicalizationMethod);

        $signatureMethod = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignatureMethod');
        $signatureMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');
        $signedInfo->appendChild($signatureMethod);

        $reference = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Reference');
        $reference->setAttribute('URI', '#ECF');

        $transforms = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Transforms');
        $transform = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Transform');
        $transform->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($transform);
        $reference->appendChild($transforms);

        $digestMethod = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $reference->appendChild($digestMethod);

        $digestValueElem = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:DigestValue');
        $digestValueText = $xml->createTextNode($digestValue);
        $digestValueElem->appendChild($digestValueText);
        $reference->appendChild($digestValueElem);

        $signedInfo->appendChild($reference);
        $signature->appendChild($signedInfo);

        $signatureValue = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:SignatureValue');
        $signatureValueText = $xml->createTextNode($signatureData['firma']);
        $signatureValue->appendChild($signatureValueText);
        $signature->appendChild($signatureValue);

        $keyInfo = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
        $x509Data = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Data');
        
        $x509Cert = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate');
        $x509Cert->appendChild($xml->createTextNode($signatureData['certificado']));
        $x509Data->appendChild($x509Cert);

        $x509Serial = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509SerialNumber');
        $x509Serial->appendChild($xml->createTextNode($signatureData['serial']));
        $x509Data->appendChild($x509Serial);

        $x509Issuer = $xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509IssuerName');
        $x509Issuer->appendChild($xml->createTextNode($signatureData['emisor']));
        $x509Data->appendChild($x509Issuer);

        $keyInfo->appendChild($x509Data);
        $signature->appendChild($keyInfo);

        $ecfNode->appendChild($signature);

        return $xml->saveXML();
    }
}
