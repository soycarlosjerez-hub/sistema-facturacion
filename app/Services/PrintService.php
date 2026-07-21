<?php

namespace App\Services;

use App\Models\Conduce;
use App\Models\Cotizacion;
use App\Models\HistorialImpresion;
use App\Models\Impresora;
use App\Models\Orden;
use App\Models\Venta;
use Illuminate\Support\Facades\Log;

class PrintService
{
    public const PAPER_58MM = 58;
    public const PAPER_80MM = 80;

    public function renderCotizacionTicket(Cotizacion $cotizacion, int $paperWidth = self::PAPER_80MM): string
    {
        $cotizacion->load(['cliente', 'user', 'items']);
        $charsPerLine = $this->getCharsPerLine($paperWidth);
        $output = '';

        $output .= $this->center($cotizacion->user?->empresa?->nombre ?? config('app.name', 'Sistema'), $charsPerLine) . "\n";
        $output .= $this->center('RNC: ' . ($cotizacion->user?->empresa?->rnc ?? 'N/A'), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->center('*** COTIZACION ***', $charsPerLine) . "\n";
        $output .= $this->center($cotizacion->numero, $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->leftRight('Fecha:', $cotizacion->fecha->format('d/m/Y H:i'), $charsPerLine) . "\n";
        $output .= $this->leftRight('Valida hasta:', $cotizacion->fecha_validez->format('d/m/Y'), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= "Cliente: " . substr($cotizacion->cliente?->nombre ?? 'N/A', 0, $charsPerLine - 9) . "\n";
        if ($cotizacion->cliente?->rnc) {
            $output .= "RNC: " . $cotizacion->cliente->rnc . "\n";
        }
        if ($cotizacion->cliente?->telefono) {
            $output .= "Tel: " . $cotizacion->cliente->telefono . "\n";
        }
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= "PRODUCTOS:\n";
        $output .= $this->separator($charsPerLine) . "\n";

        foreach ($cotizacion->items as $item) {
            $output .= $this->truncate($item->nombre, $charsPerLine) . "\n";
            $qtyPrice = $item->cantidad . ' x RD$' . number_format($item->precio_unitario, 2);
            $subtotal = 'RD$' . number_format($item->subtotal, 2);
            $output .= $this->leftRight($qtyPrice, $subtotal, $charsPerLine) . "\n";
            if ($item->descuento > 0) {
                $output .= $this->leftRight('  Desc:', '-RD$' . number_format($item->descuento, 2), $charsPerLine) . "\n";
            }
        }

        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->leftRight('Subtotal:', 'RD$' . number_format($cotizacion->subtotal, 2), $charsPerLine) . "\n";
        if ($cotizacion->descuento > 0) {
            $output .= $this->leftRight('Descuento:', '-RD$' . number_format($cotizacion->descuento, 2), $charsPerLine) . "\n";
        }
        $output .= $this->leftRight('ITBIS (18%):', 'RD$' . number_format($cotizacion->itbis, 2), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine, '=') . "\n";
        $output .= $this->leftRight('TOTAL:', 'RD$' . number_format($cotizacion->total, 2), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine, '=') . "\n";
        $output .= $this->center('Estado: ' . strtoupper($cotizacion->estado_label), $charsPerLine) . "\n";
        $output .= "\n";

        if ($cotizacion->notas) {
            $output .= $this->separator($charsPerLine) . "\n";
            $output .= "Notas:\n";
            $output .= $this->wordWrap($cotizacion->notas, $charsPerLine) . "\n";
        }
        if ($cotizacion->condiciones) {
            $output .= $this->separator($charsPerLine) . "\n";
            $output .= "Terminos y Condiciones:\n";
            $output .= $this->wordWrap($cotizacion->condiciones, $charsPerLine) . "\n";
        }

        $output .= "\n";
        $output .= $this->center('Esta cotizacion es valida hasta', $charsPerLine) . "\n";
        $output .= $this->center($cotizacion->fecha_validez->format('d/m/Y'), $charsPerLine) . "\n";
        $output .= "\n\n\n";

        return $output;
    }

    public function renderVentaTicket(Venta $venta, int $paperWidth = self::PAPER_80MM): string
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto']);
        $charsPerLine = $this->getCharsPerLine($paperWidth);
        $output = '';
        $fechaVenta = $venta->fecha instanceof \DateTimeInterface
            ? $venta->fecha
            : \Carbon\Carbon::parse($venta->fecha);

        $output .= $this->center(config('app.name', 'Sistema'), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->center('*** FACTURA ***', $charsPerLine) . "\n";
        $output .= $this->center('Venta #' . str_pad($venta->id, 6, '0', STR_PAD_LEFT), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->leftRight('Fecha:', $fechaVenta->format('d/m/Y H:i'), $charsPerLine) . "\n";
        $output .= $this->leftRight('Cajero:', substr($venta->usuario?->name ?? 'N/A', 0, 20), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= "Cliente: " . substr($venta->cliente?->nombre ?? 'N/A', 0, $charsPerLine - 9) . "\n";
        if ($venta->cliente?->rnc) {
            $output .= "RNC: " . $venta->cliente->rnc . "\n";
        }
        $output .= $this->separator($charsPerLine) . "\n";

        foreach ($venta->detalles as $det) {
            $output .= $this->truncate($det->producto?->nombre ?? 'N/A', $charsPerLine) . "\n";
            $qtyPrice = $det->cantidad . ' x RD$' . number_format($det->precio_unitario, 2);
            $subtotal = 'RD$' . number_format($det->subtotal, 2);
            $output .= $this->leftRight($qtyPrice, $subtotal, $charsPerLine) . "\n";
        }

        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->leftRight('Subtotal:', 'RD$' . number_format($venta->subtotal, 2), $charsPerLine) . "\n";
        $output .= $this->leftRight('ITBIS:', 'RD$' . number_format($venta->impuestos, 2), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine, '=') . "\n";
        $output .= $this->leftRight('TOTAL:', 'RD$' . number_format($venta->total, 2), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine, '=') . "\n";
        $output .= "\n";
        $output .= $this->center('Gracias por su compra!', $charsPerLine) . "\n";
        $output .= "\n\n\n";

        return $output;
    }

    public function renderConduceTicket(Conduce $conduce, int $paperWidth = self::PAPER_80MM): string
    {
        $conduce->load(['cliente', 'usuario', 'items.producto']);
        $charsPerLine = $this->getCharsPerLine($paperWidth);
        $output = '';

        $output .= $this->center(config('app.name', 'Sistema'), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->center('*** CONDUCE ***', $charsPerLine) . "\n";
        $output .= $this->center($conduce->numero, $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->leftRight('Fecha:', $conduce->fecha->format('d/m/Y'), $charsPerLine) . "\n";
        $output .= $this->leftRight('Entrega:', $conduce->fecha_entrega?->format('d/m/Y') ?? 'N/A', $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= "Cliente: " . substr($conduce->cliente?->nombre ?? 'N/A', 0, $charsPerLine - 9) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= "PRODUCTOS:\n";
        $output .= $this->separator($charsPerLine) . "\n";

        foreach ($conduce->items as $item) {
            $output .= $this->truncate($item->producto?->nombre ?? 'N/A', $charsPerLine) . "\n";
            $qtyPrice = $item->cantidad . ' x RD$' . number_format($item->precio_unitario, 2);
            $subtotal = 'RD$' . number_format($item->subtotal, 2);
            $output .= $this->leftRight($qtyPrice, $subtotal, $charsPerLine) . "\n";
            if ($item->cantidad_recibida !== null) {
                $output .= $this->leftRight('  Recibido:', (string)$item->cantidad_recibida, $charsPerLine) . "\n";
            }
        }

        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->leftRight('Total Items:', (string)$conduce->items->count(), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine, '=') . "\n";
        $output .= $this->leftRight('TOTAL:', 'RD$' . number_format($conduce->total, 2), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine, '=') . "\n";

        if ($conduce->transportista || $conduce->vehiculo) {
            $output .= "\n";
            $output .= "Transporte:\n";
            if ($conduce->transportista) $output .= "  Conductor: {$conduce->transportista}\n";
            if ($conduce->vehiculo) $output .= "  Vehiculo: {$conduce->vehiculo}\n";
        }

        $output .= "\n";
        $output .= $this->center('Firma de recibido:', $charsPerLine) . "\n";
        $output .= "\n\n\n";

        return $output;
    }

    public function toEscPos(string $text): string
    {
        $init = "\x1B\x40";
        $encoded = @iconv('UTF-8', 'CP850//TRANSLIT', $text);
        if ($encoded === false) {
            $encoded = $text;
        }
        $cut = "\x1D\x56\x00";
        return $init . $encoded . $cut;
    }

    public function saveAsTextFile(string $content, string $filename): string
    {
        $dir = storage_path('app/tickets');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir . '/' . $filename;
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Enviar texto directamente a una impresora física
     */
    public function enviarATexto(Impresora $impresora, string $texto): string
    {
        $contenido = $this->toEscPos($texto);

        return match ($impresora->tipo_conexion) {
            'red' => $this->enviarARed($impresora, $contenido),
            'local' => $this->enviarALocal($impresora, $contenido),
            'compartida' => $this->enviarACompartida($impresora, $contenido),
            'pdf' => $this->enviarAPdf($impresora, $texto),
            default => throw new \RuntimeException("Tipo de conexión no soportado: {$impresora->tipo_conexion}"),
        };
    }

    /**
     * Imprimir documento completo usando mike42/escpos-php
     */
    public function imprimirConEscpos(Impresora $impresora, string $texto): void
    {
        $connector = match ($impresora->tipo_conexion) {
            'red' => new \Mike42\Escpos\PrintConnectors\NetworkPrintConnector(
                $impresora->direccion_ip,
                $impresora->puerto ?? 9100
            ),
            'local' => new \Mike42\Escpos\PrintConnectors\FilePrintConnector(
                $impresora->ruta_compartida ?? ($impresora->driver === 'windows' ? 'LPT1' : '/dev/usb/lp0')
            ),
            'compartida' => new \Mike42\Escpos\PrintConnectors\WindowsPrintConnector(
                $impresora->ruta_compartida ?? 'smb://localhost/printer'
            ),
            default => throw new \RuntimeException("Conexión no soportada para ESC/POS: {$impresora->tipo_conexion}"),
        };

        $printer = new \Mike42\Escpos\Printer($connector);
        $printer->initialize();
        $printer->setTextSize(1, 1);
        $printer->selectPrintMode();
        $printer->setJustification(\Mike42\Escpos\Printer::JUSTIFY_LEFT);

        $lines = explode("\n", $texto);
        foreach ($lines as $line) {
            if (str_starts_with($line, '***') && str_ends_with($line, '***')) {
                $printer->setEmphasis(true);
                $printer->text($line . "\n");
                $printer->setEmphasis(false);
            } else {
                $printer->text($line . "\n");
            }
        }

        $printer->cut();
        $printer->close();
    }

    /**
     * Imprimir un documento completo (venta, cotizacion, conduce)
     */
    public function imprimirDocumento(
        string $tipo,
        int $id,
        Impresora $impresora,
        string $formato = 'ticket',
        int $copias = 1,
        string $papelTamano = '80mm',
    ): string {
        $paperWidth = $papelTamano === '58mm' ? self::PAPER_58MM : self::PAPER_80MM;
        $texto = '';

        $modelo = match ($tipo) {
            'venta' => Venta::findOrFail($id),
            'cotizacion' => Cotizacion::findOrFail($id),
            'conduce' => Conduce::findOrFail($id),
            default => throw new \RuntimeException("Tipo de documento inválido: {$tipo}"),
        };

        $texto = match ($tipo) {
            'venta' => $this->renderVentaTicket($modelo, $paperWidth),
            'cotizacion' => $this->renderCotizacionTicket($modelo, $paperWidth),
            'conduce' => $this->renderConduceTicket($modelo, $paperWidth),
        };

        $documentoNumero = match ($tipo) {
            'venta' => '#' . str_pad($modelo->id, 6, '0', STR_PAD_LEFT),
            'cotizacion' => $modelo->numero,
            'conduce' => $modelo->numero,
        };

        $errores = [];
        $exitoso = true;

        for ($i = 0; $i < $copias; $i++) {
            try {
                if ($impresora->tipo_conexion === 'pdf') {
                    $this->enviarAPdf($impresora, $texto, "{$tipo}_{$id}_copia" . ($i + 1));
                } elseif (class_exists('\Mike42\Escpos\Printer')) {
                    $this->imprimirConEscpos($impresora, $texto);
                } else {
                    $this->enviarATexto($impresora, $texto);
                }
            } catch (\Throwable $e) {
                $exitoso = false;
                $errores[] = $e->getMessage();
                Log::error("Error imprimiendo {$tipo}#{$id}", [
                    'impresora' => $impresora->nombre,
                    'copia' => $i + 1,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        HistorialImpresion::create([
            'imprimible_type' => get_class($modelo),
            'imprimible_id' => $modelo->id,
            'impresora_id' => $impresora->id,
            'user_id' => auth()->id(),
            'tipo_documento' => $tipo,
            'documento_numero' => $documentoNumero,
            'formato' => $formato,
            'copias' => $copias,
            'papel_tamano' => $papelTamano,
            'exitoso' => $exitoso,
            'error_mensaje' => $errores ? implode('; ', $errores) : null,
            'tamanio_bytes' => strlen($texto),
        ]);

        if (!$exitoso) {
            throw new \RuntimeException("Error al imprimir: " . implode('; ', $errores));
        }

        return "Documento {$tipo} {$documentoNumero} impreso correctamente en {$impresora->nombre} ({$copias} copia(s))";
    }

    private function enviarARed(Impresora $impresora, string $contenido): string
    {
        $socket = @fsockopen($impresora->direccion_ip, $impresora->puerto ?? 9100, $errno, $errstr, 5);
        if (!$socket) {
            throw new \RuntimeException("No se pudo conectar a {$impresora->direccion_ip}:{$impresora->puerto} - {$errstr}");
        }
        fwrite($socket, $contenido);
        fclose($socket);
        return "Enviado a {$impresora->nombre} (red)";
    }

    private function enviarALocal(Impresora $impresora, string $contenido): string
    {
        $path = $impresora->ruta_compartida ?? (PHP_OS_FAMILY === 'Windows' ? 'LPT1' : '/dev/usb/lp0');
        $fp = @fopen($path, 'wb');
        if (!$fp) {
            throw new \RuntimeException("No se pudo abrir el puerto: {$path}");
        }
        fwrite($fp, $contenido);
        fclose($fp);
        return "Enviado a {$impresora->nombre} (local: {$path})";
    }

    private function enviarACompartida(Impresora $impresora, string $contenido): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $path = $impresora->ruta_compartida ?? 'LPT1';
            $fp = @fopen($path, 'wb');
            if (!$fp) {
                throw new \RuntimeException("No se pudo abrir: {$path}");
            }
            fwrite($fp, $contenido);
            fclose($fp);
            return "Enviado a {$impresora->nombre} (compartida)";
        }
        // En Linux, usar smbclient
        $ruta = $impresora->ruta_compartida;
        $tmpFile = tempnam(sys_get_temp_dir(), 'print_');
        file_put_contents($tmpFile, $contenido);
        exec("smbclient '{$ruta}' -c 'print {$tmpFile}' 2>&1", $output, $code);
        @unlink($tmpFile);
        if ($code !== 0) {
            throw new \RuntimeException("Error smbclient: " . implode("\n", $output));
        }
        return "Enviado a {$impresora->nombre} (SMB)";
    }

    private function enviarAPdf(Impresora $impresora, string $texto, ?string $filename = null): string
    {
        $dir = storage_path('app/prints');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = $filename ?? 'print_' . now()->format('Ymd_His');
        $path = $dir . '/' . $filename . '.txt';
        file_put_contents($path, $texto);

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML(
                '<pre style="font-family:monospace;font-size:10px;">' . e($texto) . '</pre>'
            );
            $pdfPath = $dir . '/' . $filename . '.pdf';
            $pdf->save($pdfPath);
            return "PDF generado: {$pdfPath}";
        } catch (\Throwable $e) {
            return "Archivo guardado: {$path} (PDF no disponible: {$e->getMessage()})";
        }
    }

    private function getCharsPerLine(int $paperWidth): int
    {
        return $paperWidth === self::PAPER_58MM ? 32 : 42;
    }

    private function center(string $text, int $width): string
    {
        $text = trim($text);
        $len = mb_strlen($text);
        if ($len >= $width) return $text;
        $pad = (int) floor(($width - $len) / 2);
        return str_repeat(' ', $pad) . $text;
    }

    private function leftRight(string $left, string $right, int $width): string
    {
        $leftLen = mb_strlen($left);
        $rightLen = mb_strlen($right);
        $totalContent = $leftLen + $rightLen;

        if ($totalContent >= $width) {
            $maxLeft = max(1, $width - $rightLen - 1);
            if ($leftLen > $maxLeft) {
                $left = mb_substr($left, 0, $maxLeft - 1) . '.';
                $leftLen = mb_strlen($left);
            }
            $spaces = 1;
        } else {
            $spaces = $width - $leftLen - $rightLen;
        }

        return $left . str_repeat(' ', $spaces) . $right;
    }

    private function separator(int $width, string $char = '-'): string
    {
        return str_repeat($char, $width);
    }

    private function truncate(string $text, int $width): string
    {
        if (mb_strlen($text) <= $width) return $text;
        return mb_substr($text, 0, $width - 1) . '.';
    }

    private function wordWrap(string $text, int $width): string
    {
        return wordwrap($text, $width, "\n", true);
    }

    /**
     * Imprimir una venta directamente (metodo comodín usado por VentaController)
     */
    public function imprimir(Venta $venta): string
    {
        $impresora = Impresora::activas()->first();
        if (!$impresora) {
            throw new \RuntimeException('No hay impresoras activas configuradas.');
        }
        return $this->imprimirDocumento('venta', $venta->id, $impresora);
    }

    /**
     * Imprimir una orden de restaurante (POS)
     */
    public function printOrden(Orden $orden): string
    {
        $impresora = Impresora::activas()->first();
        if (!$impresora) {
            throw new \RuntimeException('No hay impresoras activas configuradas.');
        }

        $paperWidth = $impresora->papel_tamano === '58mm' ? self::PAPER_58MM : self::PAPER_80MM;
        $texto = $this->renderOrdenTicket($orden, $paperWidth);

        HistorialImpresion::create([
            'imprimible_type' => get_class($orden),
            'imprimible_id' => $orden->id,
            'impresora_id' => $impresora->id,
            'user_id' => auth()->id(),
            'tipo_documento' => 'orden',
            'documento_numero' => $orden->ncf ?? '#' . str_pad($orden->id, 6, '0', STR_PAD_LEFT),
            'formato' => 'ticket',
            'copias' => 1,
            'papel_tamano' => $impresora->papel_tamano ?? '80mm',
            'exitoso' => true,
            'error_mensaje' => null,
            'tamanio_bytes' => strlen($texto),
        ]);

        if ($impresora->tipo_conexion === 'pdf') {
            return $this->enviarAPdf($impresora, $texto, "orden_{$orden->id}");
        } elseif (class_exists('\Mike42\Escpos\Printer')) {
            $this->imprimirConEscpos($impresora, $texto);
            return "Orden #{$orden->id} impresa correctamente en {$impresora->nombre}";
        } else {
            return $this->enviarATexto($impresora, $texto);
        }
    }

    /**
     * Renderizar ticket de orden de restaurante
     */
    public function renderOrdenTicket(Orden $orden, int $paperWidth = self::PAPER_80MM): string
    {
        $orden->load(['cliente', 'usuario', 'detalles.producto', 'pagos']);
        $charsPerLine = $this->getCharsPerLine($paperWidth);
        $output = '';

        $output .= $this->center(config('app.name', 'Sistema'), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->center('*** ORDEN DE COCINA ***', $charsPerLine) . "\n";
        $output .= $this->center($orden->ncf ?? ('#' . str_pad($orden->id, 6, '0', STR_PAD_LEFT)), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine) . "\n";

        $fechaOrden = $orden->created_at instanceof \DateTimeInterface
            ? $orden->created_at
            : \Carbon\Carbon::parse($orden->created_at);
        $output .= $this->leftRight('Hora:', $fechaOrden->format('d/m/Y H:i'), $charsPerLine) . "\n";
        $output .= $this->leftRight('Mesero:', substr($orden->usuario?->name ?? 'N/A', 0, 20), $charsPerLine) . "\n";

        if ($orden->cliente) {
            $output .= $this->leftRight('Cliente:', substr($orden->cliente->nombre ?? 'N/A', 0, 20), $charsPerLine) . "\n";
        }

        $output .= $this->separator($charsPerLine) . "\n";
        $output .= "PRODUCTOS:\n";
        $output .= $this->separator($charsPerLine) . "\n";

        foreach ($orden->detalles as $det) {
            $productName = $det->producto?->nombre ?? 'N/A';
            $output .= $this->truncate($productName, $charsPerLine - 2) . "\n";

            $qtyLine = $det->cantidad . ' x ';
            $priceFormatted = 'RD$' . number_format($det->precio_unitario, 2);
            $output .= $this->leftRight($qtyLine, $priceFormatted, $charsPerLine) . "\n";

            if ($det->subtotal > 0) {
                $output .= $this->leftRight('', 'Subtotal: RD$' . number_format($det->subtotal, 2), $charsPerLine) . "\n";
            }

            if ($det->notas) {
                $output .= $this->leftRight('  Nota:', $det->notas, $charsPerLine) . "\n";
            }

            if ($det->curso) {
                $output .= $this->leftRight('  Curso:', $det->curso, $charsPerLine) . "\n";
            }

            $output .= "\n";
        }

        $output .= $this->separator($charsPerLine) . "\n";
        $output .= $this->leftRight('Subtotal:', 'RD$' . number_format($orden->subtotal, 2), $charsPerLine) . "\n";
        if ($orden->impuestos > 0) {
            $output .= $this->leftRight('ITBIS:', 'RD$' . number_format($orden->impuestos, 2), $charsPerLine) . "\n";
        }
        if ($orden->descuento > 0) {
            $output .= $this->leftRight('Descuento:', '-RD$' . number_format($orden->descuento, 2), $charsPerLine) . "\n";
        }
        if ($orden->propina > 0) {
            $output .= $this->leftRight('Propina:', 'RD$' . number_format($orden->propina, 2), $charsPerLine) . "\n";
        }
        $output .= $this->separator($charsPerLine, '=') . "\n";
        $output .= $this->leftRight('TOTAL:', 'RD$' . number_format($orden->total, 2), $charsPerLine) . "\n";
        $output .= $this->separator($charsPerLine, '=') . "\n";

        if ($orden->notas) {
            $output .= "\n";
            $output .= $this->center('Notas:', $charsPerLine) . "\n";
            $output .= $this->wordWrap($orden->notas, $charsPerLine) . "\n";
        }

        $output .= "\n";
        $output .= $this->center('*** IMPRESO POR SISTEMA ***', $charsPerLine) . "\n";
        $output .= "\n\n\n";

        return $output;
    }
}
