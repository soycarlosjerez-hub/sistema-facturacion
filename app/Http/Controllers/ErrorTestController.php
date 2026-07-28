<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\ErrorMailer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageLogged;
use Throwable;

class ErrorTestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function index()
    {
        $settings = [
            'mail_host'         => SystemSetting::get('mail_host', ''),
            'mail_port'         => SystemSetting::get('mail_port', '465'),
            'mail_username'     => SystemSetting::get('mail_username', ''),
            'mail_encryption'   => SystemSetting::get('mail_encryption', 'ssl'),
            'mail_from_address' => SystemSetting::get('mail_from_address', ''),
            'mail_from_name'    => SystemSetting::get('mail_from_name', ''),
            'error_alert_email' => SystemSetting::get('error_alert_email', ''),
        ];

        $smtpOk = !empty($settings['mail_host']);
        $alertOk = !empty($settings['error_alert_email']);

        // Últimos 10 logs de error
        $recentErrors = DB::table('instance_error_logs')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('owner.error-test', compact('settings', 'smtpOk', 'alertOk', 'recentErrors'));
    }

    public function testSmtp(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            ErrorMailer::applyGlobalSmtp();

            \Mail::raw('Esta es una prueba de conexión SMTP enviada desde la página de prueba de errores. Si recibiste este correo, la configuración funciona correctamente.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('[PRUEBA] Conexión SMTP - Sistema de Facturación');
            });

            return redirect()->route('owner.error-test')->with('success', "Correo de prueba SMTP enviado a {$request->test_email}. Revisa tu bandeja de entrada.");
        } catch (Exception $e) {
            return redirect()->route('owner.error-test')->with('error', "Error SMTP: " . $e->getMessage());
        }
    }

    public function simulateException(Request $request)
    {
        $type = $request->input('type', 'division');

        switch ($type) {
            case 'null':
                throw new \RuntimeException('Simulación: Acceso a propiedad de objeto nulo.');
            case 'array':
                throw new \InvalidArgumentException('Simulación: Índice de array no definido.');
            case 'class':
                throw new \BadMethodCallException('Simulación: Método no existe en clase.');
            case 'division':
            default:
                throw new \RuntimeException('Simulación: División por cero o error crítico simulado.');
        }
    }

    public function testLog(Request $request)
    {
        try {
            ErrorMailer::applyGlobalSmtp();

            $testMessage = 'PRUEBA_DE_TEST: Este es un mensaje de error generado manualmente desde la página de prueba. Si recibes el correo, el sistema de alertas funciona.';

            Cache::forget('error_alert_log:' . md5('PRUEBA_DE_TEST'));

            Log::channel('single')->error($testMessage);

            Event::dispatch(new MessageLogged('error', $testMessage, []));

            return redirect()->route('owner.error-test')->with('success', "Mensaje de log de prueba enviado. Espera unos segundos y revisa tu correo.");
        } catch (Exception $e) {
            return redirect()->route('owner.error-test')->with('error', "Error enviando log de prueba: " . $e->getMessage());
        }
    }

    public function triggerErrorFromDb(Request $request)
    {
        $email = $request->input('email', SystemSetting::get('error_alert_email', ''));

        if (empty($email)) {
            return redirect()->route('owner.error-test')->with('error', 'Primero configura error_alert_email en Configuración SMTP.');
        }

        try {
            ErrorMailer::applyGlobalSmtp();

            $log = DB::table('instance_error_logs')->insertGetId([
                'tenant_id' => null,
                'level' => 'error',
                'title' => 'PRUEBA_DE_TEST: Error manual desde página de pruebas',
                'message' => 'Este error fue creado manualmente para probar el sistema de alertas por correo. Puedes ignorarlo.',
                'context' => json_encode(['test' => true, 'manual_trigger' => true]),
                'source' => 'error_test_page',
                'user_id' => auth()->id(),
                'file' => 'error_test_page',
                'line' => 1,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Disparar el mail directamente
            \Mail::to($email)
                ->send(
                    new \App\Mail\ErrorAlertMail(
                        level: 'error',
                        title: 'PRUEBA_DE_TEST: Error Manual',
                        errorMessage: "Error manual ID: {$log->id}\nCreado para probar el sistema de alertas.",
                        exceptionClass: 'ManualTest',
                        file: 'error_test_page',
                        line: 1,
                        ipAddress: $request->ip(),
                        userAgent: $request->userAgent(),
                        context: ['test' => true, 'manual_trigger' => true],
                        source: 'error_test_page',
                        createdAt: now()->format('Y-m-d H:i:s'),
                        tenantName: null,
                    )
                );

            return redirect()->route('owner.error-test')->with('success', "Error de prueba creado (ID: {$log}). Revisa tu correo {$email}.");
        } catch (Exception $e) {
            return redirect()->route('owner.error-test')->with('error', "Error creando prueba: " . $e->getMessage());
        }
    }
}
