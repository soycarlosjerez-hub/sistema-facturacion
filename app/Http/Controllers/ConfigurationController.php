<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::allCached();
        $businessInstance = Auth::user()->businessInstance;
        
        return view('configuracion.index', compact('settings', 'businessInstance'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'tipo_negocio']);

        // SMTP settings can only be modified by owner/root
        if (!Auth::user()->hasRole('owner') && !Auth::user()->hasRole('root')) {
            $mailKeys = ['mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];
            foreach ($mailKeys as $key) {
                unset($data[$key]);
            }
        }

        // Validation removed: all fields are optional.
        // $validator = Validator::make($data, []);
        // if ($validator->fails()) {
        //     return back()->withErrors($validator)->withInput();
        // }

        // Encrypt mail password, preserve existing if left blank
        if (isset($data['mail_password']) && empty($data['mail_password'])) {
            unset($data['mail_password']);
        } elseif (isset($data['mail_password']) && !empty($data['mail_password'])) {
            $data['mail_password'] = Crypt::encryptString($data['mail_password']);
        }

        // Remove null or empty values to avoid DB constraint errors
        $filteredData = array_filter($data, function($v) {
            return $v !== null && $v !== '';
        });

        $tenantId = SystemSetting::tenantId();
        foreach ($filteredData as $clave => $valor) {
            SystemSetting::updateOrCreate(
                ['clave' => $clave, 'tenant_id' => $tenantId],
                ['valor' => $valor]
            );
        }

        SystemSetting::flush();

        return redirect()->route('configuracion.index')
            ->with('success', 'Configuración actualizada correctamente.');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $settings = SystemSetting::allCached();

        // Temporarily apply SMTP config for this send
        $mailer = $settings['mail_mailer'] ?? 'log';
        config([
            'mail.default' => $mailer,
            'mail.mailers.smtp.host' => $settings['mail_host'] ?? '',
            'mail.mailers.smtp.port' => $settings['mail_port'] ?? 587,
            'mail.mailers.smtp.username' => $settings['mail_username'] ?? null,
            'mail.mailers.smtp.password' => $settings['mail_password'] ? Crypt::decryptString($settings['mail_password']) : null,
            'mail.mailers.smtp.encryption' => $settings['mail_encryption'] !== 'null' ? $settings['mail_encryption'] : null,
            'mail.from.address' => $settings['mail_from_address'] ?? 'no-reply@facturacion.local',
            'mail.from.name' => $settings['mail_from_name'] ?? 'Sistema de Facturación',
        ]);

        try {
            Mail::raw('Este es un correo de prueba desde <strong>' . config('app.name') . '</strong>.<br><br>Si recibes este mensaje, la configuración SMTP funciona correctamente.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Prueba de Configuración SMTP - ' . config('app.name'));
            });

            return back()->with('success', 'Correo de prueba enviado exitosamente a ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar correo de prueba: ' . $e->getMessage());
        }
    }
}