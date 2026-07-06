<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservacionController extends Controller
{
    public function index()
    {
        $query = Reservacion::with('mesa', 'user')->deSucursal();

        // DEBUG: Log query info
        \Illuminate\Support\Facades\Log::debug('Reservacion::index', [
            'user_id' => Auth::id(),
            'business_instance_id' => Auth::user()?->business_instance_id,
            'session_sucursal_id' => session('sucursal_id'),
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'total_with_scopes' => Reservacion::count(),
            'total_without_scopes' => Reservacion::withoutGlobalScopes()->count(),
        ]);

        if ($busqueda = request('busqueda')) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('cliente_nombre', 'like', "%{$busqueda}%")
                  ->orWhere('cliente_telefono', 'like', "%{$busqueda}%")
                  ->orWhereHas('mesa', function ($q2) use ($busqueda) {
                      $q2->where('nombre', 'like', "%{$busqueda}%")
                         ->orWhere('numero', 'like', "%{$busqueda}%");
                  });
            });
        }

        if ($estado = request('estado')) {
            $query->where('estado', $estado);
        }

        $reservaciones = $query->orderBy('fecha_hora')->paginate(20);
        $mesas = Mesa::deSucursal()->orderBy('numero')->get();
        return view('restaurante.reservaciones', compact('reservaciones', 'mesas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mesa_id'          => 'required|exists:mesas,id',
            'cliente_nombre'   => 'required|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email'    => 'nullable|email|max:200',
            'personas'         => 'required|integer|min:1',
            'fecha_hora'       => 'required|date',
            'notas'            => 'nullable|string|max:500',
        ]);

        $mesa = Mesa::findOrFail($data['mesa_id']);
        if ($mesa->estado !== 'disponible') {
            return back()->with('error', 'La mesa seleccionada no está disponible.');
        }

        $data['user_id'] = Auth::id();
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;

        DB::beginTransaction();
        try {
            Reservacion::create($data);
            $mesa->update(['estado' => 'reservada']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la reservación: ' . $e->getMessage());
        }

        return redirect()->route('restaurante.reservaciones.index')->with('success', 'Reservación creada.');
    }

    public function update(Request $request, Reservacion $reservacion)
    {
        $data = $request->validate([
            'mesa_id'          => 'required|exists:mesas,id',
            'cliente_nombre'   => 'required|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email'    => 'nullable|email|max:200',
            'personas'         => 'required|integer|min:1',
            'fecha_hora'       => 'required|date',
            'notas'            => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $mesaAnterior = $reservacion->mesa;
            $nuevaMesa = Mesa::findOrFail($data['mesa_id']);

            if ($nuevaMesa->id !== $mesaAnterior->id && $nuevaMesa->estado !== 'disponible') {
                return back()->with('error', 'La mesa seleccionada no está disponible.');
            }

            $reservacion->update($data);

            if ($nuevaMesa->id !== $mesaAnterior->id) {
                $otrasReservas = Reservacion::where('mesa_id', $mesaAnterior->id)
                    ->where('id', '!=', $reservacion->id)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->exists();
                if (!$otrasReservas && $mesaAnterior->estado === 'reservada') {
                    $mesaAnterior->update(['estado' => 'disponible']);
                }
                $nuevaMesa->update(['estado' => 'reservada']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la reservación: ' . $e->getMessage());
        }

        return redirect()->route('restaurante.reservaciones.index')->with('success', 'Reservación actualizada.');
    }

    public function estado(Request $request, Reservacion $reservacion)
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmada,cancelada,cumplida']);

        DB::beginTransaction();
        try {
            $reservacion->update(['estado' => $request->estado]);
            $mesa = $reservacion->mesa;

            if (in_array($request->estado, ['cancelada', 'cumplida'])) {
                $otrasReservas = Reservacion::where('mesa_id', $mesa->id)
                    ->where('id', '!=', $reservacion->id)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->exists();
                if (!$otrasReservas && !$mesa->ordenActiva && $mesa->estado === 'reservada') {
                    $mesa->update(['estado' => 'disponible']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }

        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(Reservacion $reservacion)
    {
        DB::beginTransaction();
        try {
            $mesa = $reservacion->mesa;
            $reservacion->delete();

            $otrasReservas = Reservacion::where('mesa_id', $mesa->id)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->exists();
            if (!$otrasReservas && !$mesa->ordenActiva && $mesa->estado === 'reservada') {
                $mesa->update(['estado' => 'disponible']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('restaurante.reservaciones.index')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }

        return redirect()->route('restaurante.reservaciones.index')->with('success', 'Reservación eliminada.');
    }
}
