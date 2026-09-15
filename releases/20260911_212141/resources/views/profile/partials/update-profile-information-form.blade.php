<form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-6">
    @csrf
    @method('patch')

    <div>
        <label class="ui-label" for="name">{{ __('Name') }}</label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-person"></i></span>
            <input id="name" name="name" type="text" class="ui-input" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Tu nombre" />
        </div>
        @error('name')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="ui-label" for="email">{{ __('Email') }}</label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-envelope"></i></span>
            <input id="email" name="email" type="email" class="ui-input" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="correo@ejemplo.com" />
        </div>
        @error('email')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="fs-6 mt-2 text-secondary">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline fs-6 text-secondary hover:text-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium fs-6 text-success">
                        <i class="bi bi-check-circle me-1"></i>{{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div class="d-flex align-items-center gap-4 mt-4">
        <button type="submit" class="ui-btn ui-btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('Save') }}</button>

        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="saved-indicator"
            >
                <i class="bi bi-check-circle-fill"></i>{{ __('Saved.') }}
            </p>
        @endif
    </div>
</form>
