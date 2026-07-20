<?php

namespace App\Http\Controllers;

use App\Mail\ReservacionCanceladaMail;
use App\Mail\ReservacionConfirmadaMail;
use App\Mail\ReservacionRecibidaMail;
use App\Models\Mesa;
use App\Models\Reservacion;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservacionController extends Controller
{
    public function index()
    {
        $query = Reservacion::with('mesa', 'user', 'cliente')->deSucursal();

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
            'cliente_id'       => 'nullable|exists:clientes,id',
            'cliente_nombre'   => 'required|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email'    => 'nullable|email|max:200',
            'personas'         => 'required|integer|min:1',
            'fecha_hora'       => 'required|date',
            'notas'            => 'nullable|string|max:500',
        ]);

        $mesa = Mesa::with('ordenActiva')->findOrFail($data['mesa_id']);
        if ($mesa->estado !== 'disponible') {
            return back()->with('error', 'La mesa seleccionada no está disponible.');
        }
        if ($mesa->ordenActiva) {
            return back()->with('error', 'La mesa tiene una orden activa.');
        }

        $overlap = Reservacion::where('mesa_id', $data['mesa_id'])
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('fecha_hora', '>=', now()->subHour())
            ->where('id', '!=', $data['mesa_id'])
            ->exists();
        if ($overlap) {
            return back()->with('error', 'La mesa ya tiene una reservación en ese horario.');
        }

        $data['user_id'] = Auth::id();
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;

        DB::beginTransaction();
        try {
            $reservacion = Reservacion::create($data);
            $mesa->update(['estado' => 'reservada']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la reservación: ' . $e->getMessage());
        }

        if (!empty($data['cliente_email'])) {
            $cc = SystemSetting::get('mail_from_address');
            Mail::to($data['cliente_email'])
                ->cc($cc ?: null)
                ->send(new ReservacionRecibidaMail($reservacion));
        }

        return redirect()->route('restaurante.reservaciones.index')->with('success', 'Reservación creada.');
    }

    public function update(Request $request, Reservacion $reservacion)
    {
        $data = $request->validate([
            'mesa_id'          => 'required|exists:mesas,id',
            'cliente_id'       => 'nullable|exists:clientes,id',
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
            $nuevaMesa = Mesa::with('ordenActiva')->findOrFail($data['mesa_id']);

            if ($nuevaMesa->id !== $mesaAnterior->id) {
                if ($nuevaMesa->estado !== 'disponible') {
                    DB::rollBack();
                    return back()->with('error', 'La mesa seleccionada no está disponible.');
                }
                if ($nuevaMesa->ordenActiva) {
                    DB::rollBack();
                    return back()->with('error', 'La mesa destino tiene una orden activa.');
                }

                $overlap = Reservacion::where('mesa_id', $nuevaMesa->id)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->where('fecha_hora', '>=', now()->subHour())
                    ->where('id', '!=', $reservacion->id)
                    ->exists();
                if ($overlap) {
                    DB::rollBack();
                    return back()->with('error', 'La mesa destino ya tiene una reservación en ese horario.');
                }
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

        $reservacion->load('cliente');
        $email = $reservacion->cliente_email;
        if (!empty($email)) {
            $cc = SystemSetting::get('mail_from_address');
            
            if ($request->estado === 'confirmada') {
                Mail::to($email)
                    ->cc($cc ?: null)
                    ->send(new ReservacionConfirmadaMail($reservacion));
            } elseif ($request->estado === 'cancelada') {
                Mail::to($email)
                    ->cc($cc ?: null)
                    ->send(new ReservacionCanceladaMail($reservacion));
            }
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
