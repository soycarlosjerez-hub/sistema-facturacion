<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LibroRetencionesConsolidadoExport implements WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new LibroRetencionesSheet($this->data, 'Compras'),
            new LibroRetencionesSheet($this->data, 'Ventas'),
        ];
    }
}

class LibroRetencionesSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected array $data;
    protected string $sheetName;

    public function __construct(array $data, string $sheetName)
    {
        $this->data = $data;
        $this->sheetName = $sheetName;
    }

    public function collection()
    {
        if ($this->sheetName === 'Compras') {
            return $this->buildComprasRows();
        }

        return $this->buildVentasRows();
    }

    protected function buildComprasRows()
    {
        $rows = [];
        $i = 1;

        foreach ($this->data['compras'] as $compra) {
            $rows[] = [
                '#'            => $i++,
                'Fecha'        => $compra->fecha?->format('Y-m-d') ?? '',
                'Proveedor'    => $compra->proveedor?->nombre ?? 'N/A',
                'RNC'          => $compra->proveedor?->rnc ?? 'N/A',
                'Base Imponible'  => number_format((float) $compra->subtotal, 2, '.', ''),
                'ITBIS'        => number_format((float) $compra->itbis_total, 2, '.', ''),
                'Ret ISR'      => number_format((float) $compra->retencion_isr, 2, '.', ''),
                'Ret ITBIS'    => number_format((float) $compra->retencion_itbis, 2, '.', ''),
                'Total Retenido' => number_format((float) ($compra->retencion_isr + $compra->retencion_itbis), 2, '.', ''),
                'Comprobante'  => 'C-' . str_pad($compra->id, 5, '0', STR_PAD_LEFT),
            ];
        }

        return collect($rows);
    }

    protected function buildVentasRows()
    {
        $rows = [];
        $i = 1;

        foreach ($this->data['ventas'] as $venta) {
            $rows[] = [
                '#'            => $i++,
                'Fecha'        => $venta->created_at?->format('Y-m-d') ?? '',
                'Cliente'      => $venta->cliente?->nombre ?? 'Consumidor Final',
                'RNC/Cédula'   => $venta->cliente?->rnc_cedula ?? '00000000000',
                'Total'        => number_format((float) $venta->total, 2, '.', ''),
                'Ret ISR'      => number_format((float) $venta->retencion_isr, 2, '.', ''),
                'Ret ITBIS'    => number_format((float) $venta->retencion_itbis, 2, '.', ''),
                'Total Retenido' => number_format((float) ($venta->retencion_isr + $venta->retencion_itbis), 2, '.', ''),
                'NCF'          => $venta->ncf ?? 'S/N',
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        if ($this->sheetName === 'Compras') {
            return [
                '#', 'Fecha', 'Proveedor', 'RNC',
                'Base Imponible', 'ITBIS',
                'Ret ISR', 'Ret ITBIS', 'Total Retenido', 'Comprobante',
            ];
        }

        return [
            '#', 'Fecha', 'Cliente', 'RNC/Cédula',
            'Total', 'Ret ISR', 'Ret ITBIS', 'Total Retenido', 'NCF',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo del encabezado
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'border' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Bordes en todo el rango de datos
        $ultimaFila = $sheet->getHighestRow();
        $ultimaCol = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $ultimaCol . $ultimaFila)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Alinear números a la derecha
        $numCols = ['E', 'F', 'G', 'H', 'I']; // Base Imponible, ITBIS, Ret ISR, Ret ITBIS, Total Retenido
        if ($this->sheetName === 'Ventas') {
            $numCols = ['E', 'F', 'G', 'H']; // Total, Ret ISR, Ret ITBIS, Total Retenido
        }
        foreach ($numCols as $col) {
            $sheet->getStyle($col . '2:' . $col . $ultimaFila)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);
        }

        // Auto-fit column widths
        foreach (range('A', $ultimaCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return $this->sheetName;
    }
}
