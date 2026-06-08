<?php

namespace App\Http\Controllers;

use App\Models\CertificadoDigital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificadoDigitalController extends Controller
{
    public function index()
    {
        $certificados = CertificadoDigital::orderBy('fecha_vencimiento', 'desc')->get();
        $stats = [
            'total' => $certificados->count(),
            'vigentes' => $certificados->filter(fn($c) => $c->vigente())->count(),
            'por_vencer' => $certificados->filter(fn($c) => $c->vigente() && $c->diasParaVencer() <= 30)->count(),
            'vencidos' => $certificados->filter(fn($c) => !$c->vigente())->count(),
        ];
        return view('certificados-digitales.index', compact('certificados', 'stats'));
    }

    public function create()
    {
        return view('certificados-digitales.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'rnc_emisor' => 'required|string|max:20',
            'rnc_titular' => 'required|string|max:20',
            'archivo' => 'required|file|mimes:p12,pfx|max:2048',
            'password' => 'required|string|min:1',
            'serial_number' => 'nullable|string|max:100',
            'emisor_cert' => 'nullable|string|max:255',
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'required|date|after:today',
            'notas' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('archivo')->store('certificados', 'local');
        $fullPath = Storage::disk('local')->path($path);

        $cert = new CertificadoDigital($data);
        $cert->archivo_path = $fullPath;
        $cert->password = $data['password'];
        $cert->activo = $request->boolean('activo', true);
        $cert->save();

        unset($data['password']);

        return redirect()->route('certificados-digitales.index')
            ->with('success', 'Certificado digital registrado correctamente.');
    }

    public function show(CertificadoDigital $certificado)
    {
        $cert = $certificado;
        $cert->load('documentos');
        return view('certificados-digitales.show', compact('cert'));
    }

    public function edit(CertificadoDigital $certificado)
    {
        $cert = $certificado;
        return view('certificados-digitales.edit', compact('cert'));
    }

    public function update(Request $request, CertificadoDigital $certificado)
    {
        $cert = $certificado;
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'rnc_emisor' => 'required|string|max:20',
            'rnc_titular' => 'required|string|max:20',
            'serial_number' => 'nullable|string|max:100',
            'emisor_cert' => 'nullable|string|max:255',
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'required|date',
            'notas' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('archivo')) {
            $request->validate(['archivo' => 'file|mimes:p12,pfx|max:2048']);
            $path = $request->file('archivo')->store('certificados', 'local');
            $cert->archivo_path = Storage::disk('local')->path($path);
        }
        if ($request->filled('password')) {
            $cert->password = $request->password;
        }

        $data['activo'] = $request->boolean('activo', true);
        $cert->fill($data);
        $cert->save();

        return redirect()->route('certificados-digitales.index')
            ->with('success', 'Certificado actualizado.');
    }

    public function destroy(CertificadoDigital $certificado)
    {
        if ($certificado->documentos()->exists()) {
            return back()->with('error', 'No se puede eliminar: el certificado tiene documentos firmados.');
        }
        if ($certificado->archivo_path && file_exists($certificado->archivo_path)) {
            @unlink($certificado->archivo_path);
        }
        $certificado->delete();
        return back()->with('success', 'Certificado eliminado.');
    }

    public function toggle(CertificadoDigital $certificado)
    {
        $certificado->update(['activo' => !$certificado->activo]);
        return back()->with('success', 'Estado del certificado actualizado.');
    }
}
