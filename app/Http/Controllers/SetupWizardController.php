<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\LavaderoServicio;
use App\Models\Lavador;
use App\Models\Mesa;
use App\Models\MesaCategoria;
use App\Models\MesaUbicacion;
use App\Models\NcfSequence;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\SystemSetting;
use App\Services\CajaService;
use App\Services\SetupWizardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SetupWizardController extends Controller
{
    public function __construct(
        protected SetupWizardService $wizard
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        $steps = $this->wizard->getSteps($user);
        $current = $this->wizard->firstPendingStep($steps);
        $canComplete = $this->wizard->canComplete($steps);
        $completedKeys = $this->wizard->completedStepKeys($steps);

        return view('setup.wizard', compact('steps', 'current', 'canComplete', 'completedKeys'));
    }

    public function processStep(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $step = $request->input('step');
        $rules = $this->rulesFor($step);

        $data = $request->validate($rules);
        $data['tenant_id'] = $user->business_instance_id;

        $this->createEntity($step, $data, $data['tenant_id']);

        return redirect()->route('setup.wizard');
    }

    public function complete(): RedirectResponse
    {
        $user = Auth::user();
        $steps = $this->wizard->getSteps($user);

        if (!$this->wizard->canComplete($steps)) {
            return back()->with('error', 'Completa todos los pasos obligatorios primero.');
        }

        $instance = $user->businessInstance;
        $instance->update(['setup_completed' => true]);

        session()->flash('setup_completed', true);

        return redirect()->route('setup.wizard');
    }

    public function restart(): RedirectResponse
    {
        $user = Auth::user();
        $role = $user->instanceRole;
        if (!$role || $role->name !== 'admin') {
            abort(403);
        }

        $user->businessInstance->update(['setup_completed' => false]);
        session()->forget('setup_completed');

        return redirect()->route('setup.wizard');
    }

    public function abrirCaja(): RedirectResponse
    {
        $user = Auth::user();
        $caja = Caja::where('tenant_id', $user->business_instance_id)
            ->where('activo', true)
            ->first();

        if (!$caja) {
            return redirect()->route('cajas.index')->with('error', 'No hay cajas activas. Crea una caja primero.');
        }

        try {
            app(CajaService::class)->abrir($caja->id, $user->id);
        } catch (\Exception $e) {
            return redirect()->route('cajas.index')->with('error', $e->getMessage());
        }

        return redirect()->route('ventas.create');
    }

    protected function rulesFor(string $step): array
    {
        return match ($step) {
            'parametros' => [
                'empresa_nombre'   => 'required|string|max:255',
                'empresa_telefono' => 'nullable|string|max:50',
                'moneda_simbolo'   => 'required|string|max:10',
                'impuesto_itbis'   => 'required|numeric|min:0',
            ],
            'sucursal' => [
                'nombre' => 'required|string|max:255',
                'codigo' => 'required|string|max:50',
            ],
            'caja' => [
                'nombre' => 'required|string|max:255',
                'sucursal_id' => 'required|exists:sucursales,id',
            ],
            'almacen' => [
                'nombre' => 'required|string|max:255',
            ],
            'categoria-producto' => [
                'nombre'      => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:500',
                'color'       => 'nullable|string|max:20',
            ],
            'producto' => [
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'itbis_porcentaje' => 'required|numeric|min:0',
                'stock' => 'nullable|numeric|min:0',
            ],
            'cliente' => [
                'nombre'       => 'required|string|max:255',
                'rnc_cedula'   => 'nullable|string|max:20',
                'tipo_cliente' => 'required|string|in:consumo,credito_fiscal,gubernamental,especial',
                'telefono'     => 'nullable|string|max:50',
            ],
            'proveedor' => [
                'nombre'   => 'required|string|max:255',
                'rnc'      => 'nullable|string|max:20',
                'telefono' => 'nullable|string|max:50',
                'email'    => 'nullable|email|max:255',
            ],
            'ncf' => [
                'tipo_comprobante' => 'required|string|max:10',
                'prefijo' => 'required|string|max:5',
                'desde' => 'required|integer|min:1',
                'hasta' => 'required|integer|min:1|gte:desde',
                'fecha_vencimiento' => 'required|date',
            ],
            'ubicacion-mesa' => [
                'nombre' => 'required|string|max:255',
            ],
            'categoria-mesa' => [
                'nombre' => 'required|string|max:255',
            ],
            'mesa' => [
                'numero'       => 'nullable|string|max:20',
                'nombre'       => 'nullable|string|max:255',
                'ubicacion_id' => 'required|exists:mesa_ubicaciones,id',
                'categoria_id' => 'required|exists:mesa_categorias,id',
                'capacidad'    => 'nullable|integer|min:1',
            ],
            'servicio-lavado' => [
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
            ],
            'lavador' => [
                'nombre' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:50',
            ],
            default => ['_error' => 'required'],
        };
    }

    protected function createEntity(string $step, array $data, int $tenantId)
    {
        if ($step === 'parametros') {
            foreach ($data as $key => $value) {
                if ($key === 'step' || $key === 'tenant_id') {
                    continue;
                }
                SystemSetting::updateOrCreate(
                    ['key' => $key, 'tenant_id' => $tenantId],
                    ['value' => $value ?? '']
                );
            }
            return true; 
        }

        return match ($step) {
            'sucursal' => Sucursal::create([
                'nombre' => $data['nombre'],
                'codigo' => $data['codigo'],
                'tenant_id' => $tenantId,
            ]),
            'caja' => Caja::create([
                'nombre' => $data['nombre'],
                'sucursal_id' => $data['sucursal_id'],
                'activo' => true,
                'tenant_id' => $tenantId,
            ]),
            'almacen' => Almacen::create([
                'nombre'    => $data['nombre'],
                'tenant_id' => $tenantId,
            ]),
            'categoria-producto' => Categoria::create([
                'nombre'      => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'color'       => $data['color'] ?? '#3b82f6',
                'activa'      => true,
                'tenant_id'   => $tenantId,
            ]),
            'producto' => Producto::create([
                'nombre' => $data['nombre'],
                'precio' => $data['precio'],
                'itbis_porcentaje' => $data['itbis_porcentaje'],
                'stock' => $data['stock'] ?? 0,
                'tenant_id' => $tenantId,
            ]),
            'cliente' => Cliente::create([
                'nombre'       => $data['nombre'],
                'rnc_cedula'   => $data['rnc_cedula'] ?? null,
                'rnc'          => $data['rnc_cedula'] ?? null,
                'tipo_cliente' => $data['tipo_cliente'],
                'tipo_documento'=> '1', // Por defecto cédula/rnc
                'telefono'     => $data['telefono'] ?? null,
                'tenant_id'    => $tenantId,
            ]),
            'proveedor' => Proveedor::create([
                'nombre'    => $data['nombre'],
                'rnc'       => $data['rnc'] ?? null,
                'telefono'  => $data['telefono'] ?? null,
                'email'     => $data['email'] ?? null,
                'tenant_id' => $tenantId,
            ]),
            'ncf' => NcfSequence::create([
                'nombre' => 'Secuencia ' . ($data['prefijo'] ?? 'NCF') . ' - Tipo ' . $data['tipo_comprobante'],
                'tipo_comprobante' => $data['tipo_comprobante'],
                'prefijo' => $data['prefijo'],
                'desde' => $data['desde'],
                'hasta' => $data['hasta'],
                'actual' => $data['desde'],
                'fecha_vencimiento' => $data['fecha_vencimiento'],
                'activo' => true,
                'tenant_id' => $tenantId,
            ]),
            'ubicacion-mesa' => MesaUbicacion::create([
                'nombre' => $data['nombre'],
                'tenant_id' => $tenantId,
            ]),
            'categoria-mesa' => MesaCategoria::create([
                'nombre' => $data['nombre'],
                'tenant_id' => $tenantId,
            ]),
            'mesa' => Mesa::create([
                'numero'       => $data['numero'] ?? str_pad(
                    Mesa::where('tenant_id', $tenantId)->count() + 1,
                    2, '0', STR_PAD_LEFT
                ),
                'nombre'       => $data['nombre'] ?? null,
                'ubicacion_id' => $data['ubicacion_id'],
                'categoria_id' => $data['categoria_id'],
                'capacidad'    => $data['capacidad'] ?? 4,
                'tenant_id'    => $tenantId,
            ]),
            'servicio-lavado' => LavaderoServicio::create([
                'nombre' => $data['nombre'],
                'precio' => $data['precio'],
                'tenant_id' => $tenantId,
            ]),
            'lavador' => Lavador::create([
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'] ?? '',
                'tenant_id' => $tenantId,
            ]),
            default => null,
        };
    }
}
