<?php

namespace App\Http\Controllers;

use App\Services\OwnerRecoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerBootstrapController extends Controller
{
    /**
     * Mostrar el estado del Owner Bootstrap.
     */
    public function status(): View
    {
        $status = app(OwnerRecoveryService::class)->getStatus();

        return view('owner.bootstrap-status', compact('status'));
    }

    /**
     * Reconstruir el Owner en la BD.
     */
    public function recover(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = app(OwnerRecoveryService::class)->rebuildOwner($request->string('password'));

        if ($result['success']) {
            return redirect()->route('owner.dashboard')
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Cambiar la contraseña del Owner.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = app(OwnerRecoveryService::class)->changePassword(
            $request->string('current_password'),
            $request->string('password'),
            $request->string('password_confirmation')
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', $result['message']);
        }

        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }
}
