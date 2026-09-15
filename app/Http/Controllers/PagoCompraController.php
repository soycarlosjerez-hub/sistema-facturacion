<?php

namespace App\Http\Controllers;

use App\Models\PagoCompra;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoCompraController extends Controller
{
    public function index(Request $request)
    {
        $query = PagoCompra::with(['compra.proveedor'])
            ->latest();

        if ($request->filled('search')) {
            $query->whereHas('compra', function($q) use ($request) {
                $q->where('observaciones', 'like', "%{$request->search}%")
                  ->orWhereHas('proveedor', fn($q) => $q->where('nombre', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        $pagos = $query->paginate(20);
        return view('pagos-compras.index', compact('pagos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'compra_id' => 'required|exists:compras,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,fiado',
            'nota' => 'nullable|string|max:500',
        ]);

        $compra = Compra::findOrFail($validated['compra_id']);
        $totalPagado = PagoCompra::where('compra_id', $compra->id)->sum('monto');
        $saldo = round($compra->total - $totalPagado, 2);

        if ($validated['monto'] > $saldo) {
            return back()->withErrors(['monto' => "El monto no puede ser mayor al saldo pendiente (RD{$saldo})"]);
        }

        DB::transaction(function () use ($validated) {
            PagoCompra::create([
                'compra_id' => $validated['compra_id'],
                'monto' => $validated['monto'],
                'metodo_pago' => $validated['metodo_pago'],
                'nota' => $validated['nota'] ?? null,
                'fecha_pago' => now(),
            ]);
        });

        return redirect()->route('pagos-compras.index')
            ->with('success', "Pago de RD{$validated['monto']} registrado para la compra #{$validated['compra_id']}");
    }

    public function show(PagoCompra $pagoCompra)
    {
        $pagoCompra->load(['compra.proveedor']);
        $totalPagado = PagoCompra::where('compra_id', $pagoCompra->compra_id)->sum('monto');
        $totalCompra = $pagoCompra->compra->total ?? 0;
        return view('pagos-compras.show', compact('pagoCompra', 'totalPagado', 'totalCompra'));
    }

    public function cancelar(PagoCompra $pagoCompra)
    {
        $pagoCompra->delete();
        return redirect()->back()->with('success', 'Pago cancelado correctamente.');
    }

    public function getTotalPagado($compra_id)
    {
        return PagoCompra::where('compra_id', $compra_id)->sum('monto');
    }
}
