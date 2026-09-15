<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\EcfDocumento;
use App\Models\PlantillaImpresion;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

class PlantillaPdfGenerator
{
    public function __construct(
        protected PlantillaVariablesService $variablesService
    ) {}

    public function generarVenta(Venta $venta, ?PlantillaImpresion $template = null)
    {
        if (! $template) {
            $template = $venta->plantilla
                ?? PlantillaImpresion::query()
                    ->where('modulo', 'ventas')
                    ->where('tenant_id', $venta->tenant_id)
                    ->where('activo', true)
                    ->where('es_default', true)
                    ->where(function ($q) {
                        $q->whereNull('tipo_doc_target')
                            ->orWhere('tipo_doc_target', 0);
                    })
                    ->first();
        }

        if (! $template) {
            return Pdf::loadView('ventas.pdf', compact('venta'))
                ->setPaper('a4', 'portrait');
        }

        $variables = $this->variablesService->paraVenta($venta);

        $view = 'plantillas.pdf-render';
        $templateConfig = $template->loadDefaults();

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'portrait';

        $sizes = [
            'a4' => [210, 297],
            'letter' => [216, 279],
            'ticket_80' => [80, 200],
            'ticket_58' => [58, 150],
        ];

        if (isset($sizes[$paper])) {
            [$width, $height] = $sizes[$paper];
        } else {
            $paper = 'a4';
            [$width, $height] = $sizes['a4'];
        }

        $pdf = Pdf::loadView($view, compact('venta', 'template', 'variables', 'templateConfig'))
            ->setPaper($paper, $orientation);

        if ($paper === 'ticket_80' || $paper === 'ticket_58') {
            $pdf->setPaper([0, 0, $width, $height], $orientation);
        }

        return $pdf;
    }

    public function generarEcf(EcfDocumento $ecf, ?PlantillaImpresion $template = null, ?string $qrUrl = null)
    {
        if (! $template) {
            $template = PlantillaImpresion::query()
                ->where('modulo', 'ventas')
                ->where(function ($q) {
                    $q->where('tipo_doc_target', 2)
                        ->orWhereNull('tipo_doc_target');
                })
                ->where('tenant_id', $ecf->tenant_id)
                ->where('activo', true)
                ->orderByRaw('tipo_doc_target = 2 DESC')
                ->orderBy('es_default', 'desc')
                ->first();
        }

        if (! $template) {
            return Pdf::loadView('ventas.ecf-pdf', compact('ecf', 'qrUrl'))
                ->setPaper('letter', 'portrait');
        }

        $variables = $this->variablesService->paraEcf($ecf, $qrUrl);

        $view = 'plantillas.pdf-render-ecf';
        $templateConfig = $template->loadDefaults();

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'portrait';

        $sizes = [
            'a4' => [210, 297],
            'letter' => [216, 279],
            'ticket_80' => [80, 200],
            'ticket_58' => [58, 150],
        ];

        if (isset($sizes[$paper])) {
            [$width, $height] = $sizes[$paper];
        } else {
            $paper = 'a4';
            [$width, $height] = $sizes['a4'];
        }

        $pdf = Pdf::loadView($view, compact('ecf', 'template', 'variables', 'templateConfig'))
            ->setPaper($paper, $orientation);

        if ($paper === 'ticket_80' || $paper === 'ticket_58') {
            $pdf->setPaper([0, 0, $width, $height], $orientation);
        }

        return $pdf;
    }

    public function generarCompra(Compra $compra, ?PlantillaImpresion $template = null)
    {
        if (! $template) {
            $template = $compra->plantilla
                ?? PlantillaImpresion::query()
                    ->where('modulo', 'compras')
                    ->where('tenant_id', $compra->tenant_id)
                    ->where('activo', true)
                    ->where('es_default', true)
                    ->first();
        }

        if (! $template) {
            return Pdf::loadView('compras.pdf', compact('compra'))
                ->setPaper('a4', 'portrait');
        }

        $variables = $this->variablesService->paraCompra($compra);

        $view = 'plantillas.pdf-render-compra';
        $templateConfig = $template->loadDefaults();

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'portrait';

        $sizes = [
            'a4' => [210, 297],
            'letter' => [216, 279],
            'ticket_80' => [80, 200],
            'ticket_58' => [58, 150],
        ];

        if (isset($sizes[$paper])) {
            [$width, $height] = $sizes[$paper];
        } else {
            $paper = 'a4';
            [$width, $height] = $sizes['a4'];
        }

        $pdf = Pdf::loadView($view, compact('compra', 'template', 'variables', 'templateConfig'))
            ->setPaper($paper, $orientation);

        if ($paper === 'ticket_80' || $paper === 'ticket_58') {
            $pdf->setPaper([0, 0, $width, $height], $orientation);
        }

        return $pdf;
    }

    public function previewVenta(
        ?PlantillaImpresion $template = null,
        ?array $demoData = null
    ) {
        if (! $template) {
            $template = PlantillaImpresion::query()
                ->where('modulo', 'ventas')
                ->where('activo', true)
                ->where('es_default', true)
                ->first();
        }

        $variables = $demoData ?: $this->variablesService->datosDemo();
        $templateConfig = $template ? $template->loadDefaults() : [];

        return view('plantillas.preview', compact('template', 'variables', 'templateConfig'));
    }

    public function previewPdfVenta(?PlantillaImpresion $template = null)
    {
        if (! $template) {
            $template = PlantillaImpresion::query()
                ->where('modulo', 'ventas')
                ->where('activo', true)
                ->where('es_default', true)
                ->first();
        }

        $variables = $this->variablesService->datosDemo();
        $templateConfig = $template ? $template->loadDefaults() : [];

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'portrait';

        $sizes = [
            'a4' => [210, 297],
            'letter' => [216, 279],
            'ticket_80' => [80, 200],
            'ticket_58' => [58, 150],
        ];

        if (isset($sizes[$paper])) {
            [$width, $height] = $sizes[$paper];
        } else {
            $paper = 'a4';
            [$width, $height] = $sizes['a4'];
        }

        $pdf = Pdf::loadView('plantillas.pdf-preview', compact('template', 'variables', 'templateConfig'))
            ->setPaper($paper, $orientation);

        if ($paper === 'ticket_80' || $paper === 'ticket_58') {
            $pdf->setPaper([0, 0, $width, $height], $orientation);
        }

        return $pdf;
    }

    public function previewPdfEcf(?PlantillaImpresion $template = null)
    {
        if (! $template) {
            $template = PlantillaImpresion::query()
                ->where('modulo', 'ventas')
                ->where('tipo_doc_target', 2)
                ->where('activo', true)
                ->where('es_default', true)
                ->first();
        }

        $variables = $this->variablesService->datosEcf();
        $templateConfig = $template ? $template->loadDefaults() : [];

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'portrait';

        $sizes = [
            'a4' => [210, 297],
            'letter' => [216, 279],
            'ticket_80' => [80, 200],
            'ticket_58' => [58, 150],
        ];

        if (isset($sizes[$paper])) {
            [$width, $height] = $sizes[$paper];
        } else {
            $paper = 'a4';
            [$width, $height] = $sizes['a4'];
        }

        $pdf = Pdf::loadView('plantillas.pdf-preview-ecf', compact('template', 'variables', 'templateConfig'))
            ->setPaper($paper, $orientation);

        if ($paper === 'ticket_80' || $paper === 'ticket_58') {
            $pdf->setPaper([0, 0, $width, $height], $orientation);
        }

        return $pdf;
    }

    public function previewPdfCompra(?PlantillaImpresion $template = null)
    {
        if (! $template) {
            $template = PlantillaImpresion::query()
                ->where('modulo', 'compras')
                ->where('activo', true)
                ->where('es_default', true)
                ->first();
        }

        $variables = $this->variablesService->datosDemo();
        $templateConfig = $template ? $template->loadDefaults() : [];

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'portrait';

        $pdf = Pdf::loadView('plantillas.pdf-preview-compra', compact('template', 'variables', 'templateConfig'))
            ->setPaper($paper, $orientation);

        return $pdf;
    }

    public function generarHistorial($ventas, array $filtros = [], ?PlantillaImpresion $template = null)
    {
        if (! $template) {
            $template = PlantillaImpresion::query()
                ->where('modulo', 'historial_ventas')
                ->where('activo', true)
                ->where('es_default', true)
                ->first();
        }

        if (! $template) {
            return Pdf::loadView('ventas.all-pdf', compact('ventas'))
                ->setPaper('a4', 'landscape');
        }

        $variables = $this->variablesService->paraHistorial($ventas, $filtros);
        $templateConfig = $template->loadDefaults();

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'landscape';

        $sizes = [
            'a4' => [210, 297],
            'letter' => [216, 279],
            'ticket_80' => [80, 200],
            'ticket_58' => [58, 150],
        ];

        if (isset($sizes[$paper])) {
            [$width, $height] = $sizes[$paper];
        } else {
            $paper = 'a4';
            [$width, $height] = $sizes['a4'];
        }

        $pdf = Pdf::loadView('plantillas.pdf-render-historial', compact('variables', 'templateConfig'))
            ->setPaper($paper, $orientation);

        if ($paper === 'ticket_80' || $paper === 'ticket_58') {
            $pdf->setPaper([0, 0, $width, $height], $orientation);
        }

        return $pdf;
    }

    public function previewPdfHistorial(?PlantillaImpresion $template = null)
    {
        if (! $template) {
            $template = PlantillaImpresion::query()
                ->where('modulo', 'historial_ventas')
                ->where('activo', true)
                ->where('es_default', true)
                ->first();
        }

        $variables = $this->variablesService->datosHistorial();
        $templateConfig = $template ? $template->loadDefaults() : [];

        $paper = $templateConfig['formato_papel'] ?? 'a4';
        $orientation = $templateConfig['orientation'] ?? 'landscape';

        $sizes = [
            'a4' => [210, 297],
            'letter' => [216, 279],
            'ticket_80' => [80, 200],
            'ticket_58' => [58, 150],
        ];

        if (isset($sizes[$paper])) {
            [$width, $height] = $sizes[$paper];
        } else {
            $paper = 'a4';
            [$width, $height] = $sizes['a4'];
        }

        $pdf = Pdf::loadView('plantillas.pdf-render-historial', compact('variables', 'templateConfig', 'pdfLogoUrl'))
            ->setPaper($paper, $orientation);

        if ($paper === 'ticket_80' || $paper === 'ticket_58') {
            $pdf->setPaper([0, 0, $width, $height], $orientation);
        }

        return $pdf;
    }
}
