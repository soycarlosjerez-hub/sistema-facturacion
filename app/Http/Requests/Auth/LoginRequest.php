<?php

namespace App\Http\Requests\Auth;

use App\Auth\OwnerBootstrappedUser;
use App\Services\OwnerBootstrapService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * The bootstrap owner service.
     */
    protected ?OwnerBootstrapService $ownerBootstrap = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Order of operations:
     *  1. Try standard DB auth (Auth::attempt)
     *  2. If DB auth fails and email matches owner config, try Bootstrap auth
     *  3. If bootstrap auth succeeds, log in the OwnerBootstrappedUser manually
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $email = $this->string('email');
        $password = $this->string('password');

        // Intentar autenticaciÃ³n estÃ¡ndar (BD)
        if (Auth::attempt(['email' => $email, 'password' => $password], $this->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey());

            return;
        }

        // Si la autenticaciÃ³n estÃ¡ndar fallÃ³, verificar si es Owner Bootstrap
        $ownerBootstrap = app(OwnerBootstrapService::class);

        if ($ownerBootstrap->isBootstrapEmail($email)) {
            $result = $ownerBootstrap->authenticate($email, $password);

            if (($result['success'] ?? false) && ! ($result['locked'] ?? false)) {
                // Verificar si el Owner ya existe en BD
                $existing = $ownerBootstrap->getOwnerFromDatabase();

                if ($existing) {
                    // El Owner existe en BD: verificar con las credenciales
                    // que se pasaron (ya validadas contra .env). Esto permite
                    // login si la BD tiene la misma contraseña o si fue
                    // sincronizada. Si la BD tiene un hash distinto, se
                    // intenta anyway porque .env y BD pueden estar
                    // desincronizados temporalmente.
                    Auth::login($existing, $this->boolean('remember'));
                    RateLimiter::clear($this->throttleKey());

                    return;
                }

                // Owner Bootstrap: no existe en BD, crear usuario "fantasma"
                $bootUser = new OwnerBootstrappedUser(
                    id: -1,
                    name: $ownerBootstrap->getName(),
                    email: $ownerBootstrap->getEmail(),
                    role: $ownerBootstrap->getRole(),
                );

                Auth::setUser($bootUser);
                RateLimiter::clear($this->throttleKey());

                return;
            }

            // Bootstrap auth fallÃ³ (credenciales incorrectas)
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // No es owner bootstrap: autenticaciÃ³n fallÃ³
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
