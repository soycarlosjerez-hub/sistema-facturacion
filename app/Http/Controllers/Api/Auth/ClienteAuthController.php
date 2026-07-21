<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\BusinessInstance;
use App\Models\ClientApiToken;
use App\Models\Cliente;
use App\Notifications\ClienteResetPassword;
use App\Notifications\ClienteVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClienteAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'         => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:clientes,email',
            'telefono'       => 'required|string|max:20|unique:clientes,telefono',
            'password'       => 'required|string|min:12|confirmed',
            'tenant_id'      => 'nullable|exists:business_instances,id',
        ]);

        $tenantId = $validated['tenant_id'] ?? null;

        if (!$tenantId) {
            $authUser = $request->user();
            if ($authUser) {
                $tenantId = $authUser->business_instance_id ?? $authUser->tenant_id ?? null;
            }
            if (!$tenantId) {
                $clientToken = $request->attributes->get('client_api_token');
                if ($clientToken && $clientToken->cliente) {
                    $tenantId = $clientToken->cliente->tenant_id;
                }
            }
        }

        if (!$tenantId) {
            $first = BusinessInstance::orderBy('id')->first();
            if (!$first) {
                return response()->json(['message' => 'No hay instancias disponibles.'], 400);
            }
            $tenantId = $first->id;
        }

        $validated['tenant_id'] = $tenantId;

        $validated['tipo_cliente'] = 'consumo';
        $validated['tipo_documento'] = '1';
        $validated['activo'] = true;
        $validated['acceso_api'] = true;

        $cliente = Cliente::create($validated);

        $this->sendEmailVerification($cliente);

        $token = $cliente->createToken('auth-token');

        return response()->json([
            'message'     => 'Cliente registrado. Revisa tu email para verificar la cuenta.',
            'cliente'     => $this->resource($cliente),
            'access_token' => $token->plain_text,
            'token_type'   => 'Bearer',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $cliente = Cliente::where('email', $request->email)->first();

        if (!$cliente || !Hash::check($request->password, $cliente->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        if (!$cliente->acceso_api) {
            return response()->json(['message' => 'Acceso API no habilitado para esta cuenta.'], 403);
        }

        if (is_null($cliente->email_verified_at)) {
            return response()->json([
                'message' => 'Email no verificado. Revisa tu bandeja de entrada.',
                'email'   => $cliente->email,
            ], 403);
        }

        $token = $cliente->createToken($request->header('User-Agent', 'api'));

        return response()->json([
            'message'      => 'Inicio de sesión exitoso.',
            'cliente'      => $this->resource($cliente),
            'access_token'  => $token->plain_text,
            'token_type'    => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('client_api_token');
        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'Sesión cerrada exitosamente.']);
    }

    public function me(Request $request): JsonResponse
    {
        $cliente = $request->user();

        return response()->json([
            'cliente' => $this->resource($cliente),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $cliente = $request->user();

        $validated = $request->validate([
            'nombre'   => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|max:255|unique:clientes,email,' . $cliente->id,
            'telefono' => 'sometimes|string|max:20|unique:clientes,telefono,' . $cliente->id,
            'direccion'=> 'sometimes|string|max:500',
            'ciudad'   => 'sometimes|string|max:100',
            'provincia'=> 'sometimes|string|max:100',
        ]);

        $cliente->update($validated);

        return response()->json([
            'message' => 'Perfil actualizado.',
            'cliente' => $this->resource($cliente),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:12|confirmed',
        ]);

        $cliente = $request->user();

        if (!Hash::check($request->current_password, $cliente->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 422);
        }

        $cliente->update(['password' => $request->new_password]);

        ClientApiToken::where('cliente_id', $cliente->id)->delete();

        $token = $cliente->createToken($request->header('User-Agent', 'api'));

        return response()->json([
            'message'      => 'Contraseña actualizada. Se cerraron las demás sesiones.',
            'access_token'  => $token->plain_text,
            'token_type'    => 'Bearer',
        ]);
    }

    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($cliente->email))) {
            return response()->json(['message' => 'Enlace de verificación inválido.'], 400);
        }

        if ($cliente->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email ya verificado.']);
        }

        $cliente->markEmailAsVerified();

        return response()->json(['message' => 'Email verificado exitosamente.']);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:clientes,email']);

        $cliente = Cliente::where('email', $request->email)->first();

        if ($cliente->hasVerifiedEmail()) {
            return response()->json(['message' => 'El email ya está verificado.']);
        }

        $this->sendEmailVerification($cliente);

        return response()->json(['message' => 'Enlace de verificación reenviado.']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:clientes,email']);

        $cliente = Cliente::where('email', $request->email)->first();
        $token = Str::random(60);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $cliente->email],
            ['email' => $cliente->email, 'token' => Hash::make($token), 'created_at' => now()]
        );

        $cliente->notify(new ClienteResetPassword($token, $cliente->email));

        return response()->json(['message' => 'Enlace de restablecimiento enviado a tu email.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email|exists:clientes,email',
            'token'    => 'required|string',
            'password' => 'required|string|min:12|confirmed',
        ]);

        $record = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'Token de restablecimiento inválido o expirado.'], 400);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Token expirado. Solicita un nuevo restablecimiento.'], 400);
        }

        $cliente = Cliente::where('email', $request->email)->first();
        $cliente->update(['password' => $request->password]);

        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        ClientApiToken::where('cliente_id', $cliente->id)->delete();

        return response()->json(['message' => 'Contraseña restablecida exitosamente.']);
    }

    private function sendEmailVerification(Cliente $cliente): void
    {
        $hash = sha1($cliente->email);
        $url = url('/api/auth/cliente/verify-email/' . $cliente->id . '/' . $hash);

        $cliente->notify(new ClienteVerifyEmail($url));
    }

    private function resource(Cliente $cliente): array
    {
        return [
            'id'                 => $cliente->id,
            'nombre'             => $cliente->nombre,
            'email'              => $cliente->email,
            'telefono'           => $cliente->telefono,
            'direccion'          => $cliente->direccion,
            'ciudad'             => $cliente->ciudad,
            'provincia'          => $cliente->provincia,
            'email_verified'     => $cliente->hasVerifiedEmail(),
            'created_at'         => $cliente->created_at,
            'tenant_id'          => $cliente->tenant_id,
        ];
    }
}
