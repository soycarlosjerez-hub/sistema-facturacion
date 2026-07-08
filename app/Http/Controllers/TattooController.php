<?php

namespace App\Http\Controllers;

use App\Models\TattooAppointment;
use App\Models\TattooArtist;
use App\Models\TattooDesign;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TattooController extends Controller
{
    public function index()
    {
        $artistas = TattooArtist::activos()->orderBy('nombre_completo')->get();
        $citasHoy = TattooAppointment::hoy()->with('cliente', 'artista', 'diseno')->orderBy('fecha_hora_inicio')->get();
        $proximas = TattooAppointment::proximas()->with('cliente', 'artista')->orderBy('fecha_hora_inicio')->limit(5)->get();
        $disenos = TattooDesign::activos()->with('artist')->orderBy('popular', 'desc')->limit(12)->get();
        $clientes = Cliente::orderBy('nombre')->get();

        $stats = [
            'hoy_pendientes' => TattooAppointment::hoy()->whereIn('estado', ['pendiente', 'confirmada'])->count(),
            'hoy_completadas' => TattooAppointment::hoy()->where('estado', 'completada')->count(),
            'hoy_ingresos' => TattooAppointment::hoy()->where('estado', 'completada')->sum('total_final'),
            'artistas_activos' => TattooArtist::activos()->count(),
        ];

        return view('tattoo.index', compact(
            'artistas', 'citasHoy', 'proximas', 'disenos', 'clientes', 'stats'
        ));
    }

    public function cambiarEstado(Request $request, TattooAppointment $cita)
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmada,en_progreso,completada,cancelada,no_show']);

        DB::beginTransaction();
        try {
            $cita->update(['estado' => $request->estado]);

            if ($request->estado === 'completada' && !$cita->fecha_hora_fin) {
                $cita->update(['fecha_hora_fin' => now()]);
            }

            DB::commit();
            return back()->with('success', "Cita #{$cita->id} actualizada a: {$request->estado}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }
}
