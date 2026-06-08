<?php

namespace App\Http\Controllers;

use App\Models\PaymentProcessor;
use Illuminate\Http\Request;

class PaymentProcessorController extends Controller
{
    public function index()
    {
        $procesadores = PaymentProcessor::orderBy('nombre')->paginate(10);
        return view('payment-processors.index', compact('procesadores'));
    }

    public function create()
    {
        return view('payment-processors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'tipo'                => 'required|string|in:tarjeta,transferencia,otro',
            'comision_porcentaje' => 'required|numeric|min:0|max:100',
            'comision_fija'       => 'required|numeric|min:0',
            'api_key'             => 'nullable|string|max:255',
            'api_secret'          => 'nullable|string',
            'api_endpoint'        => 'nullable|url|max:500',
            'api_environment'     => 'nullable|string|in:sandbox,production',
            'config_json'         => 'nullable|json',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $data['api_environment'] ??= 'sandbox';

        PaymentProcessor::create($data);

        return redirect()->route('payment-processors.index')
            ->with('success', 'Procesador de pago creado correctamente.');
    }

    public function edit(PaymentProcessor $paymentProcessor)
    {
        return view('payment-processors.edit', compact('paymentProcessor'));
    }

    public function update(Request $request, PaymentProcessor $paymentProcessor)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'tipo'                => 'required|string|in:tarjeta,transferencia,otro',
            'comision_porcentaje' => 'required|numeric|min:0|max:100',
            'comision_fija'       => 'required|numeric|min:0',
            'api_key'             => 'nullable|string|max:255',
            'api_secret'          => 'nullable|string',
            'api_endpoint'        => 'nullable|url|max:500',
            'api_environment'     => 'nullable|string|in:sandbox,production',
            'config_json'         => 'nullable|json',
        ]);

        $data['activo'] = $request->boolean('activo');
        $data['api_environment'] ??= 'sandbox';

        if (empty($data['api_secret'])) {
            unset($data['api_secret']);
        }

        $paymentProcessor->update($data);

        return redirect()->route('payment-processors.index')
            ->with('success', 'Procesador de pago actualizado.');
    }

    public function destroy(PaymentProcessor $paymentProcessor)
    {
        if ($paymentProcessor->pagos()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene pagos asociados.');
        }
        $paymentProcessor->delete();
        return redirect()->route('payment-processors.index')
            ->with('success', 'Procesador eliminado.');
    }
}
