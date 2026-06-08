<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use App\Models\SystemSetting;

class EcfQrGenerator
{
    public function buildQueryString(EcfDocumento $ecf): string
    {
        $empresa = SystemSetting::allCached();
        $rnc = $empresa['empresa_rnc'] ?? '000000000';
        $encf = $ecf->encf;
        $monto = number_format((float) $ecf->monto_total, 2, '.', '');
        $fecha = $ecf->fecha_emision->format('Y-m-d');

        $codigoSeguridad = $ecf->codigo_seguridad ?? $this->generarCodigoSeguridad($ecf);

        $params = [
            'rnc' => $rnc,
            'encf' => $encf,
            'monto' => $monto,
            'fecha' => $fecha,
            'codigo' => $codigoSeguridad,
        ];

        return http_build_query($params);
    }

    public function buildUrl(EcfDocumento $ecf): string
    {
        $base = config('dgii.qr_endpoint');
        $query = $this->buildQueryString($ecf);
        return $base . '?' . $query;
    }

    public function generarCodigoSeguridad(EcfDocumento $ecf): string
    {
        $seed = $ecf->encf . '|' . $ecf->fecha_emision->format('Y-m-d') . '|' . $ecf->monto_total;
        $hash = hash_hmac('sha256', $seed, config('app.key'));
        return strtoupper(substr($hash, 0, 6));
    }

    public function toQrApiUrl(EcfDocumento $ecf, int $size = 200): string
    {
        $url = $this->buildUrl($ecf);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($url);
    }

    public function toSvgQr(EcfDocumento $ecf, int $size = 200): ?string
    {
        if (!class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode') && !class_exists('\BaconQrCode\Writer')) {
            return null;
        }

        $url = $this->buildUrl($ecf);

        if (class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            return \SimpleSoftwareIO\QrCode\Facades\QrCode::size($size)->generate($url);
        }

        return null;
    }
}
