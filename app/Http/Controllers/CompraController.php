<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Http\Requests\UpdateCompraRequest;
use App\Models\Almacen;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\TipoCompra;
use App\Services\Ecf\EcfService;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $query = Compra::with([
            'proveedor:id,nombre,rnc,rnc_cedula',
            'almacen:id,nombre',
            'tipoCompra:id,nombre',
            'detalles.producto:id,nombre',
        ]);

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($request->filled('proveedor')) {
            $termino = trim($request->proveedor);
            $query->whereHas('proveedor', function ($q) use ($termino) {
                $q->where('nombre', 'like', '%' . $termino . '%')
                  ->orWhere('rnc_cedula', 'like', '%' . $termino . '%')
                  ->orWhere('rnc', 'like', '%' . $termino . '%');
            });
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $compras = $query
            ->orderByDesc('fecha')
            ->paginate(10)
            ->appends($request->all());

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();
        $tiposCompra = TipoCompra::orderBy('nombre')->get();
        $almacenes = $this->almacenesSegunSucursal();

        return view('compras.create', compact('proveedores', 'productos', 'tiposCompra', 'almacenes'));
    }

    public function show(Compra $compra)
    {
        $compra->load('detalles.producto', 'proveedor', 'almacen', 'tipoCompra', 'user');
        return view('compras.show', compact('compra'));
    }

    public function store(StoreCompraRequest $request)
    {
        try {
            $compra = $this->purchaseService->createPurchase($request->validated(), $request->validated('productos'));
            $message = $this->purchaseService->buildSuccessMessage($compra, 'registrada');

            return redirect()->route('compras.show', $compra)->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function edit(Compra $compra)
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();
        $detalles = $compra->detalles()->with('producto')->get();
        $tiposCompra = TipoCompra::orderBy('nombre')->get();
        $almacenes = $this->almacenesSegunSucursal();

        return view('compras.edit', compact('proveedores', 'productos', 'detalles', 'tiposCompra', 'almacenes'));
    }

    public function update(UpdateCompraRequest $request, Compra $compra)
    {
        try {
            $result = $this->purchaseService->updatePurchase($compra, $request->validated(), $request->validated('productos') ?? []);

            if ($result === null) {
                return redirect()->route('compras.index')
                    ->with('success', 'Compra eliminada porque no tiene productos.');
            }

            $message = $this->purchaseService->buildSuccessMessage($compra, 'actualizada');

            return redirect()->route('compras.show', $compra)->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    public function destroyDetalle(Compra $compra, DetalleCompra $detalle)
    {
        if ($detalle->compra_id !== $compra->id) {
            return back()->with('error', 'El detalle no pertenece a esta compra.');
        }

        try {
            $this->purchaseService->removeDetail($compra, $detalle);

            if (! $compra->detalles()->exists()) {
                $compra->delete();
                return redirect()->route('compras.index')
                    ->with('success', 'Detalle eliminado. La compra se eliminó por no tener más productos.');
            }

            return redirect()->route('compras.edit', $compra)->with('success', 'Producto eliminado de la compra.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el detalle: ' . $e->getMessage());
        }
    }

    public function destroy(Compra $compra)
    {
        try {
            $this->purchaseService->deletePurchase($compra);
            return redirect()->route('compras.index')->with('success', 'Compra eliminada y stock revertido.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    public function generarE41(Compra $compra)
    {
        if ($compra->ecf_documento_id) {
            return back()->with('error', 'Esta compra ya tiene un e-CF E41 asociado.');
        }
        if (!$compra->puede_generar_ecf) {
            return back()->with('error', 'El proveedor debe tener un RNC registrado para generar e-CF E41.');
        }

        try {
            $ecfService = app(EcfService::class);
            $ecf = $ecfService->generarE41($compra);
            return redirect()->route('ecf.show', $ecf)
                ->with('success', 'e-CF E41 generado exitosamente para la compra.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar e-CF: ' . $e->getMessage());
        }
    }

    private function almacenesSegunSucursal()
    {
        $query = Almacen::orderBy('nombre');
        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }
        return $query->get();
    }
}
