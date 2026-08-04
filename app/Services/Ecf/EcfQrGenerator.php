<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function generateLocalQr(EcfDocumento $ecf, int $size = 300): ?string
    {
        $url = $this->buildUrl($ecf);

        if (class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            return \SimpleSoftwareIO\QrCode\Facades\QrCode::size($size)->generate($url);
        }

        if (class_exists('\BaconQrCode\Renderer\RendererStyle\RendererStyle')) {
            return $this->generateWithBacon($url, $size);
        }

        return $this->generatePurePhp($url, $size);
    }

    public function saveLocalQr(EcfDocumento $ecf, int $size = 300): ?string
    {
        $svg = $this->generateLocalQr($ecf, $size);

        if (!$svg) {
            return null;
        }

        $directory = "ecf/qr/" . $ecf->fecha_emision->format('Y/m');
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory, 0755, true);
        }

        $filename = $ecf->encf . '.svg';
        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        Storage::disk('public')->put($path, $svg);

        return $path;
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

    private function generateWithBacon(string $url, int $size): ?string
    {
        try {
            $writer = new \BaconQrCode\Writer();
            $rendererStyle = new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size);
            $renderer = new \BaconQrCode\Renderer\Image\SvgImageRenderer($rendererStyle);

            $matrix = (new \BaconQrCode\Encoder())->encode($url, \BaconQrCode\Common\CharacterSetEci::getInstance(
                \BaconQrCode\Common\Charset::UTF_8
            ));

            return $writer->writeString($matrix, 'utf-8');
        } catch (\Throwable $e) {
            Log::warning('e-CF: error generando QR con Bacon', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function generatePurePhp(string $url, int $size): ?string
    {
        try {
            $qrData = $this->convertToQrMatrix($url);
            $padding = 4;
            $moduleSize = max(2, intdiv($size, count($qrData) + ($padding * 2)));
            $imageSize = (count($qrData) + ($padding * 2)) * $moduleSize;

            $image = imagecreatetruecolor($imageSize, $imageSize);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            imagefill($image, 0, 0, $white);

            for ($y = 0; $y < count($qrData); $y++) {
                for ($x = 0; $x < count($qrData[$y]); $x++) {
                    if ($qrData[$y][$x]) {
                        imagefilledrectangle(
                            $image,
                            ($x + $padding) * $moduleSize,
                            ($y + $padding) * $moduleSize,
                            ($x + $padding + 1) * $moduleSize - 1,
                            ($y + $padding + 1) * $moduleSize - 1,
                            $black
                        );
                    }
                }
            }

            ob_start();
            imagepng($image);
            $pngData = ob_get_clean();
            imagedestroy($image);

            return 'data:image/png;base64,' . base64_encode($pngData);
        } catch (\Throwable $e) {
            Log::warning('e-CF: error generando QR puro PHP', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function convertToQrMatrix(string $data): array
    {
        $version = 1;
        $modules = 21;

        for ($v = 1; $v <= 10; $v++) {
            $requiredCapacity = $this->getQrCapacity($v);
            if (strlen($data) <= $requiredCapacity) {
                $version = $v;
                $modules = 17 + ($v * 4);
                break;
            }
            $version = $v;
        }

        $matrix = array_fill(0, $modules, array_fill(0, $modules, 0));

        $this->placeFinderPatterns($matrix, $modules);
        $this->placeAlignmentPattern($matrix, $version, $modules);
        $this->placeTimingPatterns($matrix, $modules);
        $this->placeDarkModule($matrix, $modules);

        $binaryData = $this->encodeData($data, $version);
        $this->placeBinaryData($matrix, $binaryData, $modules);

        $this->applyMask($matrix, $version, $modules);

        return $matrix;
    }

    private function placeFinderPatterns(array &$matrix, int $modules): void
    {
        $positions = [[0, 0], [0, $modules - 7], [$modules - 7, 0]];

        foreach ($positions as [$y, $x]) {
            for ($dy = -1; $dy <= 7; $dy++) {
                for ($dx = -1; $dx <= 7; $dx++) {
                    $my = $y + $dy;
                    $mx = $x + $dx;
                    if ($my < 0 || $my >= count($matrix) || $mx < 0 || $mx >= count($matrix[0])) {
                        continue;
                    }

                    if ($dy === -1 || $dy === 7 || $dx === -1 || $dx === 7 ||
                        ($dy >= 2 && $dy <= 4 && $dx >= 2 && $dx <= 4)) {
                        $matrix[$my][$mx] = 1;
                    } else {
                        $matrix[$my][$mx] = 0;
                    }
                }
            }
        }
    }

    private function placeAlignmentPattern(array &$matrix, int $version, int $modules): void
    {
        if ($version < 2) {
            return;
        }

        $position = $this->getAlignmentPositions($version);
        foreach ($position as $y) {
            foreach ($position as $x) {
                if ($this->isNearFinder($matrix, $y, $x, $modules)) {
                    continue;
                }
                for ($dy = -2; $dy <= 2; $dy++) {
                    for ($dx = -2; $dx <= 2; $dx++) {
                        if (abs($dy) === 2 || abs($dx) === 2 || ($dy === 0 && $dx === 0)) {
                            $matrix[$y + $dy][$x + $dx] = 1;
                        }
                    }
                }
            }
        }
    }

    private function placeTimingPatterns(array &$matrix, int $modules): void
    {
        for ($i = 8; $i < $modules - 8; $i++) {
            $matrix[6][$i] = ($i % 2 === 0) ? 1 : 0;
            $matrix[$i][6] = ($i % 2 === 0) ? 1 : 0;
        }
    }

    private function placeDarkModule(array &$matrix, int $modules): void
    {
        $matrix[8][8] = 1;
    }

    private function isNearFinder(array $matrix, int $y, int $x, int $modules): bool
    {
        $finderRanges = [
            [0, 8],
            [0, $modules - 7],
            [$modules - 7, 8],
        ];

        foreach ($finderRanges as [$start, $end]) {
            if (($y >= $start - 2 && $y <= $end + 2) && ($x >= $start - 2 && $x <= $end + 2)) {
                return true;
            }
        }

        return false;
    }

    private function encodeData(string $data, int $version): array
    {
        $bits = '';
        $charCountBits = $this->getCharCountBits($version);

        $modeBits = '0100';
        $bits .= $modeBits;
        $bits .= str_pad(decbin(strlen($data)), $charCountBits, '0', STR_PAD_LEFT);

        for ($i = 0; $i < strlen($data); $i++) {
            $bits .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
        }

        $totalBits = $this->getTotalDataBits($version);
        if (strlen($bits) < $totalBits) {
            $bits .= '0000';
            while (strlen($bits) < $totalBits && strlen($bits) % 8 !== 0) {
                $bits .= '0';
            }
            $padBytes = [0xEC, 0x11];
            $padIndex = 0;
            while (strlen($bits) < $totalBits) {
                $bits .= str_pad(decbin($padBytes[$padIndex]), 8, '0', STR_PAD_LEFT);
                $padIndex = 1 - $padIndex;
            }
        }

        $result = [];
        for ($i = 0; $i < strlen($bits); $i += 8) {
            $result[] = bindec(substr($bits, $i, 8));
        }

        return $result;
    }

    private function placeBinaryData(array &$matrix, array $data, int $modules): void
    {
        $bitIndex = 0;
        $upward = true;

        for ($right = $modules - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }

            for ($vert = 0; $vert < $modules; $vert++) {
                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    $y = $upward ? ($modules - 1 - $vert) : $vert;

                    if ($x < 0 || $y < 0 || $y >= $modules) {
                        continue;
                    }

                    if ($matrix[$y][$x] === 0 && $bitIndex < count($data)) {
                        $byteIndex = intdiv($bitIndex, 8);
                        $bitPos = 7 - ($bitIndex % 8);
                        $matrix[$y][$x] = (int) (($data[$byteIndex] >> $bitPos) & 1);
                        $bitIndex++;
                    }
                }
            }
            $upward = !$upward;
        }
    }

    private function applyMask(array &$matrix, int $version, int $modules): void
    {
        $maskPattern = $version % 8;

        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix[$y][$x] === 0 || $this->isFunctionalCell($matrix, $y, $x, $modules)) {
                    continue;
                }

                $shouldFlip = false;
                switch ($maskPattern) {
                    case 0: $shouldFlip = ($y + $x) % 2 === 0; break;
                    case 1: $shouldFlip = $y % 2 === 0; break;
                    case 2: $shouldFlip = $x % 3 === 0; break;
                    case 3: $shouldFlip = ($y + $x) % 3 === 0; break;
                    case 4: $shouldFlip = (intdiv($y, 2) + intdiv($x, 3)) % 2 === 0; break;
                    case 5: $shouldFlip = (($y * $x) % 2) + (($y * $x) % 3) === 0; break;
                    case 6: $shouldFlip = (($y * $x) % 2) + (($y * $x) % 3) % 2 === 0; break;
                    case 7: $shouldFlip = (($y + $x) % 2) + (($y * $x) % 3) % 2 === 0; break;
                }

                $matrix[$y][$x] = $shouldFlip ? 1 - $matrix[$y][$x] : $matrix[$y][$x];
            }
        }
    }

    private function isFunctionalCell(array $matrix, int $y, int $x, int $modules): bool
    {
        if ($y < 0 || $y >= count($matrix) || $x < 0 || $x >= count($matrix[0])) {
            return true;
        }

        for ($dy = -1; $dy <= 7; $dy++) {
            for ($dx = -1; $dx <= 7; $dx++) {
                if (($y + $dy >= 0 && $y + $dy < count($matrix) && $x + $dx >= 0 && $x + $dx < count($matrix[0]))) {
                    if ($this->isFinderPatternPosition($y + $dy, $x + $dx, $modules)) {
                        return true;
                    }
                }
            }
        }

        if ($y === 6 || $x === 6) {
            return true;
        }

        if ($y === 8 && $x === 8) {
            return true;
        }

        return false;
    }

    private function isFinderPatternPosition(int $y, int $x, int $modules): bool
    {
        $ranges = [[0, 8], [0, $modules - 7], [$modules - 7, 8]];
        foreach ($ranges as [$start, $end]) {
            if ($y >= $start - 1 && $y <= $end + 1 && $x >= $start - 1 && $x <= $end + 1) {
                return true;
            }
        }
        return false;
    }

    private function getQrCapacity(int $version): int
    {
        $capacities = [288, 404, 556, 708, 888, 1060, 1244, 1412, 1596, 1780];
        return $capacities[$version - 1] ?? 288;
    }

    private function getCharCountBits(int $version): int
    {
        if ($version <= 9) {
            return 8;
        }
        return 16;
    }

    private function getTotalDataBits(int $version): int
    {
        $totalBits = [2304, 3248, 4296, 5344, 6520, 7704, 8888, 10064, 11272, 12512];
        return $totalBits[$version - 1] ?? 2304;
    }

    private function getAlignmentPositions(int $version): array
    {
        $positions = [
            1 => [],
            2 => [6, 18],
            3 => [6, 22],
            4 => [6, 26],
            5 => [6, 30],
            6 => [6, 34],
            7 => [6, 22, 38],
            8 => [6, 24, 42],
            9 => [6, 26, 46],
            10 => [6, 28, 50],
        ];
        return $positions[$version] ?? [6];
    }
}
