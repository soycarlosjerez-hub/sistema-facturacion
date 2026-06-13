<?php

namespace App\Http\Controllers;

use App\Models\WaitlistEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaitlistController extends Controller
{
    public function index()
    {
        $entries = WaitlistEntry::with('user')
            ->deSucursal()
            ->orderByRaw("FIELD(estado, 'esperando', 'llamando', 'sentado', 'cancelado')")
            ->orderBy('created_at')
            ->get();
        return response()->json(['entries' => $entries]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_nombre'   => 'required|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'personas'         => 'required|integer|min:1',
            'notas'            => 'nullable|string|max:500',
        ]);

        $data['sucursal_id'] = session('sucursal_id');
        $data['user_id'] = Auth::id();
        $data['estado'] = 'esperando';

        $entry = WaitlistEntry::create($data);
        return response()->json(['success' => true, 'entry' => $entry]);
    }

    public function updateEstado(Request $request, WaitlistEntry $entry)
    {
        $estado = $request->validate(['estado' => 'required|in:esperando,llamando,sentado,cancelado']);
        $entry->update($estado);
        return response()->json(['success' => true]);
    }

    public function destroy(WaitlistEntry $entry)
    {
        $entry->delete();
        return response()->json(['success' => true]);
    }
}
