<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentProcessorResource;
use App\Models\PaymentProcessor;
use Illuminate\Http\Request;

class PaymentProcessorController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentProcessor::query()
            ->when($request->activa, fn ($q) => $q->where('activa', true))
            ->when($request->search, fn ($q) => $q->where('nombre', 'like', '%' . $request->search . '%'));

        return PaymentProcessorResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50',
            'activa' => 'boolean',
            'configuracion' => 'nullable|array',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $processor = PaymentProcessor::create($validated);

        return new PaymentProcessorResource($processor);
    }

    public function show(PaymentProcessor $paymentProcessor)
    {
        return new PaymentProcessorResource($paymentProcessor);
    }

    public function update(Request $request, PaymentProcessor $paymentProcessor)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'codigo' => 'sometimes|string|max:50',
            'activa' => 'boolean',
            'configuracion' => 'nullable|array',
        ]);

        $paymentProcessor->update($validated);

        return new PaymentProcessorResource($paymentProcessor);
    }

    public function destroy(PaymentProcessor $paymentProcessor)
    {
        $paymentProcessor->delete();
        return response()->json(['message' => 'Procesador de pago eliminado.']);
    }
}
