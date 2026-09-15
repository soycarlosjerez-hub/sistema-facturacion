<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class AccountsReceivableService
{
    public function getAging(?int $tenantId = null): array
    {
        $query = Venta::whereIn('estado', ['pendiente', 'cuenta_abierta'])
            ->selectRaw('
                cliente_id,
                id as venta_id,
                created_at,
                total,
                (total - COALESCE((
                    SELECT SUM(pag.monto) FROM pagos pag WHERE pag.venta_id = ventas.id
                ), 0)) as saldo,
                DATEDIFF(NOW(), created_at) as antiguedad_dias
            ')
            ->from('ventas as ventas');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $sales = $query->get();

        $buckets = [
            'current' => ['count' => 0, 'amount' => 0.0],
            'days_31_60' => ['count' => 0, 'amount' => 0.0],
            'days_61_90' => ['count' => 0, 'amount' => 0.0],
            'days_90_plus' => ['count' => 0, 'amount' => 0.0],
        ];

        $byClient = [];

        foreach ($sales as $sale) {
            if ($sale->saldo <= 0) continue;

            $days = (int) $sale->antiguedad_dias;
            if ($days <= 30) {
                $buckets['current']['count']++;
                $buckets['current']['amount'] += (float) $sale->saldo;
            } elseif ($days <= 60) {
                $buckets['days_31_60']['count']++;
                $buckets['days_31_60']['amount'] += (float) $sale->saldo;
            } elseif ($days <= 90) {
                $buckets['days_61_90']['count']++;
                $buckets['days_61_90']['amount'] += (float) $sale->saldo;
            } else {
                $buckets['days_90_plus']['count']++;
                $buckets['days_90_plus']['amount'] += (float) $sale->saldo;
            }

            if (!isset($byClient[$sale->cliente_id])) {
                $byClient[$sale->cliente_id] = [
                    'cliente_id' => $sale->cliente_id,
                    'total_pendiente' => 0,
                    'current' => 0,
                    'days_31_60' => 0,
                    'days_61_90' => 0,
                    'days_90_plus' => 0,
                    'sales_count' => 0,
                ];
            }

            $byClient[$sale->cliente_id]['total_pendiente'] += (float) $sale->saldo;
            $byClient[$sale->cliente_id]['sales_count']++;

            if ($days <= 30) {
                $byClient[$sale->cliente_id]['current'] += (float) $sale->saldo;
            } elseif ($days <= 60) {
                $byClient[$sale->cliente_id]['days_31_60'] += (float) $sale->saldo;
            } elseif ($days <= 90) {
                $byClient[$sale->cliente_id]['days_61_90'] += (float) $sale->saldo;
            } else {
                $byClient[$sale->cliente_id]['days_90_plus'] += (float) $sale->saldo;
            }
        }

        // Load cliente names
        $clientIds = array_keys($byClient);
        $clientes = Cliente::whereIn('id', $clientIds)->get(['id', 'nombre', 'rnc_cedula']);
        $clienteMap = $clientes->keyBy('id');

        foreach ($byClient as &$data) {
            $cliente = $clienteMap->get($data['cliente_id']);
            if ($cliente) {
                $data['nombre'] = $cliente->nombre;
                $data['identificacion'] = $cliente->rnc_cedula;
            } else {
                $data['nombre'] = 'Cliente eliminado';
                $data['identificacion'] = '';
            }
        }

        uasort($byClient, fn ($a, $b) => $b['total_pendiente'] <=> $a['total_pendiente']);

        $totalAmount = array_sum(array_column($buckets, 'amount'));

        return [
            'buckets' => $buckets,
            'total_amount' => round($totalAmount, 2),
            'total_count' => array_sum(array_column($buckets, 'count')),
            'by_client' => array_values($byClient),
        ];
    }

    /**
     * Export aging report as HTML/PDF.
     */
    public function exportAgingPdf(array $data): \Illuminate\Http\Response
    {
        $aging = $data['aging'] ?? [];
        $summary = $data['summary'] ?? [];
        $totalAmount = $data['totalAmount'] ?? 0;
        $desde = $data['desde'] ?? now()->format('Y-m-d');
        $hasta = $data['hasta'] ?? now()->format('Y-m-d');

        $rows = '';
        foreach ($aging as $client) {
            $rows .= '<tr>';
            $rows .= '<td style="padding:6px 8px;border:1px solid #ddd;">' . e($client['nombre'] ?? 'N/A') . '</td>';
            $rows .= '<td style="padding:6px 8px;border:1px solid #ddd;font-family:monospace;font-size:11px;">' . e($client['identificacion'] ?? '-') . '</td>';
            $rows .= '<td style="padding:6px 8px;border:1px solid #ddd;font-size:12px;text-align:right;">' . number_format($client['current'] ?? 0, 2) . '</td>';
            $rows .= '<td style="padding:6px 8px;border:1px solid #ddd;font-size:12px;text-align:right;">' . number_format($client['days_31_60'] ?? 0, 2) . '</td>';
            $rows .= '<td style="padding:6px 8px;border:1px solid #ddd;font-size:12px;text-align:right;">' . number_format($client['days_61_90'] ?? 0, 2) . '</td>';
            $rows .= '<td style="padding:6px 8px;border:1px solid #ddd;font-size:12px;text-align:right;font-weight:bold;">' . number_format($client['total_pendiente'] ?? 0, 2) . '</td>';
            $rows .= '</tr>';
        }

        $html = '
        <!DOCTYPE html>
        <html><head><meta charset="utf-8">
        <title>Aging - Cuentas por Cobrar</title>
        <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2 { color: #333; border-bottom: 2px solid #6366f1; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f3f4f6; padding: 8px; text-align: left; font-size: 11px; border: 1px solid #ddd; }
        .text-end { text-align: right; }
        .total-row { background: #e8f0fe; font-weight: bold; }
        .summary-box { display: inline-block; padding: 10px 20px; margin: 5px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; min-width: 120px; }
        .summary-box .label { font-size: 10px; color: #6b7280; text-transform: uppercase; }
        .summary-box .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
        </style></head><body>
        <h2>Cuentas por Cobrar - Aging Report</h2>
        <p><strong>Período:</strong> ' . e($desde) . ' al ' . e($hasta) . '</p>
        <div>
            <div class="summary-box"><div class="label">Corriente (0-30d)</div><div class="value" style="color:#059669;">RD$ ' . number_format($summary['current']['amount'] ?? 0, 2) . '</div></div>
            <div class="summary-box"><div class="label">31-60d</div><div class="value" style="color:#2563eb;">RD$ ' . number_format($summary['days_31_60']['amount'] ?? 0, 2) . '</div></div>
            <div class="summary-box"><div class="label">61-90d</div><div class="value" style="color:#d97706;">RD$ ' . number_format($summary['days_61_90']['amount'] ?? 0, 2) . '</div></div>
            <div class="summary-box"><div class="label">Total Pendiente</div><div class="value">RD$ ' . number_format($totalAmount, 2) . '</div></div>
        </div>
        <table>
        <thead>
            <tr><th>Cliente</th><th>RNC/Cédula</th><th class="text-end">0-30d</th><th class="text-end">31-60d</th><th class="text-end">61-90d</th><th class="text-end">Total</th></tr>
        </thead>
        <tbody>' . $rows . '
        <tr class="total-row">
            <td style="padding:8px;border:1px solid #ddd;" colspan="2">Total General</td>
            <td style="padding:8px;border:1px solid #ddd;text-align:right;">' . number_format($summary['current']['amount'] ?? 0, 2) . '</td>
            <td style="padding:8px;border:1px solid #ddd;text-align:right;">' . number_format($summary['days_31_60']['amount'] ?? 0, 2) . '</td>
            <td style="padding:8px;border:1px solid #ddd;text-align:right;">' . number_format($summary['days_61_90']['amount'] ?? 0, 2) . '</td>
            <td style="padding:8px;border:1px solid #ddd;text-align:right;font-weight:bold;">' . number_format($totalAmount, 2) . '</td>
        </tr>
        </tbody></table>
        <p style="margin-top:30px;font-size:10px;color:#9ca3af;">Generado automáticamente por Erpipos ERP el ' . now()->format('d/m/Y H:i') . '</p>
        </body></html>
        ';

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="aging_' . date('Y-m-d') . '.html"',
        ]);
    }

    public function getOverdueClients(int $tenantId): array
    {
        $clients = DB::table('clientes')
            ->selectRaw('
                clientes.id,
                clientes.nombre,
                clientes.rnc_cedula,
                clientes.limite_credito,
                clientes.balance_pendiente,
                clientes.estado_credito,
                COUNT(v.id) as ventas_pendientes,
                MIN(v.created_at) as primera_ventas_fecha
            ')
            ->from('clientes')
            ->leftJoin('ventas', function($join) use ($tenantId) {
                $join->on('ventas.cliente_id', '=', 'clientes.id')
                     ->whereIn('ventas.estado', ['pendiente', 'cuenta_abierta'])
                     ->where('ventas.tenant_id', $tenantId);
            })
            ->where('clientes.tenant_id', $tenantId)
            ->where('clientes.balance_pendiente', '>', 0)
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.rnc_cedula',
                     'clientes.limite_credito', 'clientes.balance_pendiente', 'clientes.estado_credito')
            ->orderBy('clientes.balance_pendiente', 'desc')
            ->limit(100)
            ->get();

        return $clients->toArray();
    }
}
