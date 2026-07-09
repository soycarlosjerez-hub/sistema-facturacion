<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservacionResource;
use App\Mail\ReservacionCanceladaMail;
use App\Mail\ReservacionConfirmadaMail;
use App\Mail\ReservacionRecibidaMail;
use App\Models\Reservacion;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservacion::with(['cliente', 'mesa'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->mesa_id, fn ($q) => $q->where('mesa_id', $request->mesa_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->fecha, fn ($q) => $q->whereDate('fecha_hora', $request->fecha));

        return ReservacionResource::collection($query->orderBy('fecha_hora', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:200',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email' => 'nullable|email|max:200',
            'mesa_id' => 'required|exists:mesas,id',
            'fecha_hora' => 'required|date',
            'personas' => 'required|integer|min:1',
            'estado' => 'required|string|max:20',
            'notas' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['tenant_id'] = auth()->user()->business_instance_id ?? null;

        if (empty($validated['cliente_telefono']) && !empty($validated['cliente_id'])) {
            $cliente = \App\Models\Cliente::find($validated['cliente_id']);
            $validated['cliente_telefono'] = $cliente?->telefono;
        }

        $reservacion = Reservacion::create($validated);

        if (!empty($validated['cliente_id'])) {
            $updates = [];
            if (!empty($validated['cliente_nombre'])) {
                $updates['nombre'] = $validated['cliente_nombre'];
            }
            if (!empty($validated['cliente_email'])) {
                $updates['email'] = $validated['cliente_email'];
            }
            if (!empty($validated['cliente_telefono'])) {
                $updates['telefono'] = $validated['cliente_telefono'];
            }

            if (!empty($updates)) {
                \App\Models\Cliente::where('id', $validated['cliente_id'])
                    ->where('tenant_id', $validated['tenant_id'])
                    ->update($updates);
            }
        }

        $email = $validated['cliente_email'] ?? $reservacion->cliente?->email;
        if (!empty($email)) {
            $cc = SystemSetting::get('mail_from_address');
            Mail::to($email)
                ->cc($cc ?: null)
                ->send(new ReservacionRecibidaMail($reservacion));
        }

        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'user']));
    }

    public function show(Reservacion $reservacion)
    {
        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'user']));
    }

    public function update(Request $request, Reservacion $reservacion)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'sometimes|string|max:200',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email' => 'nullable|email|max:200',
            'mesa_id' => 'sometimes|exists:mesas,id',
            'fecha_hora' => 'sometimes|date',
            'personas' => 'sometimes|integer|min:1',
            'estado' => 'sometimes|string|max:20',
            'notas' => 'nullable|string',
        ]);

        $estadoAnterior = $reservacion->estado;
        $reservacion->update($validated);

        if (!empty($validated['cliente_id'])) {
            $updates = [];
            if (!empty($validated['cliente_nombre'])) {
                $updates['nombre'] = $validated['cliente_nombre'];
            }
            if (!empty($validated['cliente_email'])) {
                $updates['email'] = $validated['cliente_email'];
            }
            if (!empty($validated['cliente_telefono'])) {
                $updates['telefono'] = $validated['cliente_telefono'];
            }

            if (!empty($updates)) {
                \App\Models\Cliente::where('id', $validated['cliente_id'])
                    ->where('tenant_id', $reservacion->tenant_id)
                    ->update($updates);
            }
        }

        $reservacion->refresh();
        $email = $reservacion->cliente_email ?? $reservacion->cliente?->email;
        $cc = SystemSetting::get('mail_from_address');

        if (!empty($email)) {
            $nuevoEstado = $validated['estado'] ?? null;
            if ($nuevoEstado === 'confirmada' && $nuevoEstado !== $estadoAnterior) {
                Mail::to($email)
                    ->cc($cc ?: null)
                    ->send(new ReservacionConfirmadaMail($reservacion));
            } elseif ($nuevoEstado === 'cancelada' && $nuevoEstado !== $estadoAnterior) {
                Mail::to($email)
                    ->cc($cc ?: null)
                    ->send(new ReservacionCanceladaMail($reservacion));
            }
        }

        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'user']));
    }

    public function destroy(Reservacion $reservacion)
    {
        $reservacion->delete();
        return response()->json(['message' => 'Reservación eliminada.']);
    }
}
