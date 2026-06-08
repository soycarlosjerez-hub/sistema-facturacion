<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductosBajosStockExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Producto::where('stock', '<=', 5)->get(['nombre', 'stock']);
    }
}
