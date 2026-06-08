<?php

namespace App\Support;

use App\Models\EcfDocumento;
use App\Models\Producto;
use App\Models\SecuenciaEcf;
use App\Models\CertificadoDigital;

class AlertasSistema
{
    public static function todas(): array
    {
        $alertas = [];

        $alertas = array_merge($alertas, self::stockBajo());
        $alertas = array_merge($alertas, self::secuenciasEcf());
        $alertas = array_merge($alertas, self::certificados());
        $alertas = array_merge($alertas, self::ecfPendientes());

        usort($alertas, fn($a, $b) => ($a['severidad'] <=> $b['severidad']));

        return $alertas;
    }

    private static function stockBajo(): array
    {
        $alertas = [];
        $productos = Producto::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->whereNotNull('stock_minimo')
            ->limit(10)
            ->get();

        foreach ($productos as $p) {
            $alertas[] = [
                'tipo' => 'stock_bajo',
                'severidad' => 2,
                'icono' => 'bi-exclamation-triangle',
                'color' => 'warning',
                'mensaje' => "Stock bajo: {$p->nombre} ({$p->stock} / {$p->stock_minimo})",
                'link' => route('productos.edit', $p),
                'link_text' => 'Ver producto',
            ];
        }

        $sinStock = Producto::where('stock', '<=', 0)->limit(10)->get();
        foreach ($sinStock as $p) {
            $alertas[] = [
                'tipo' => 'sin_stock',
                'severidad' => 1,
                'icono' => 'bi-x-octagon',
                'color' => 'danger',
                'mensaje' => "Sin stock: {$p->nombre}",
                'link' => route('productos.edit', $p),
                'link_text' => 'Ver producto',
            ];
        }

        return $alertas;
    }

    private static function secuenciasEcf(): array
    {
        $alertas = [];
        $secuencias = SecuenciaEcf::where('activo', true)->get();

        foreach ($secuencias as $s) {
            $usadas = $s->actual;
            $disponibles = $s->hasta - $usadas;

            if ($disponibles <= 0) {
                $alertas[] = [
                    'tipo' => 'secuencia_agotada',
                    'severidad' => 1,
                    'icono' => 'bi-x-circle',
                    'color' => 'danger',
                    'mensaje' => "Secuencia {$s->tipo_ecf} agotada ({$s->nombre})",
                    'link' => route('secuencias-ecf.edit', $s),
                    'link_text' => 'Configurar',
                ];
            } elseif ($disponibles <= 50) {
                $alertas[] = [
                    'tipo' => 'secuencia_baja',
                    'severidad' => 2,
                    'icono' => 'bi-exclamation-triangle',
                    'color' => 'warning',
                    'mensaje' => "Secuencia {$s->tipo_ecf} por agotarse ({$disponibles} disponibles)",
                    'link' => route('secuencias-ecf.edit', $s),
                    'link_text' => 'Revisar',
                ];
            } elseif ($disponibles <= 200) {
                $alertas[] = [
                    'tipo' => 'secuencia_media',
                    'severidad' => 3,
                    'icono' => 'bi-info-circle',
                    'color' => 'info',
                    'mensaje' => "Secuencia {$s->tipo_ecf}: {$disponibles} disponibles",
                    'link' => route('secuencias-ecf.edit', $s),
                    'link_text' => 'Revisar',
                ];
            }
        }

        return $alertas;
    }

    private static function certificados(): array
    {
        $alertas = [];
        $certificados = CertificadoDigital::all();

        foreach ($certificados as $c) {
            if (!$c->valido_hasta) continue;

            $diasRestantes = now()->diffInDays($c->valido_hasta, false);

            if ($diasRestantes <= 0) {
                $alertas[] = [
                    'tipo' => 'certificado_vencido',
                    'severidad' => 1,
                    'icono' => 'bi-shield-exclamation',
                    'color' => 'danger',
                    'mensaje' => "Certificado digital vencido: {$c->nombre}",
                    'link' => route('certificados-digitales.edit', $c),
                    'link_text' => 'Renovar',
                ];
            } elseif ($diasRestantes <= 30) {
                $alertas[] = [
                    'tipo' => 'certificado_por_vencer',
                    'severidad' => 2,
                    'icono' => 'bi-shield',
                    'color' => 'warning',
                    'mensaje' => "Certificado por vencer: {$c->nombre} ({$diasRestantes} días)",
                    'link' => route('certificados-digitales.edit', $c),
                    'link_text' => 'Revisar',
                ];
            }
        }

        return $alertas;
    }

    private static function ecfPendientes(): array
    {
        $alertas = [];
        $pendientes = EcfDocumento::whereIn('estado', ['borrador', 'generado', 'firmado', 'rechazado'])
            ->limit(5)
            ->get();

        $count = $pendientes->count();
        if ($count > 0) {
            $alertas[] = [
                'tipo' => 'ecf_pendientes',
                'severidad' => 2,
                'icono' => 'bi-cloud-upload',
                'color' => 'warning',
                'mensaje' => "{$count} e-CF pendientes de proceso o reenvío",
                'link' => route('ecf.index', ['estado' => 'borrador,firmado,rechazado']),
                'link_text' => 'Revisar',
            ];
        }

        return $alertas;
    }
}
