<?php

namespace App\Http\Controllers;

use App\Models\BusinessInstance;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SolicitudPendienteController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $instance = BusinessInstance::where(function ($query) use ($user) {
            $query->where('owner_user_id', $user->id)
                ->orWhere('id', $user->business_instance_id);
        })
            ->where('aprobado', false)
            ->with('businessType')
            ->first();

        if (! $instance) {
            return redirect()->route('dashboard')->with('info', 'No tienes una solicitud pendiente.');
        }

        return view('solicitud-pendiente.index', compact('instance'));
    }
}
