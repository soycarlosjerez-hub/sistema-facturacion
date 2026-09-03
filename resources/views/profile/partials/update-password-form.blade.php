<form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-6">
    @csrf
    @method('put')

    <div>
        <label class="ui-label" for="update_password_current_password">{{ __('Current Password') }}</label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-lock"></i></span>
            <input id="update_password_current_password" name="current_password" type="password" class="ui-input" autocomplete="current-password" placeholder="Contraseña actual" />
        </div>
        @error('current_password')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="ui-label" for="update_password_password">{{ __('New Password') }}</label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-key"></i></span>
            <input id="update_password_password" name="password" type="password" class="ui-input" autocomplete="new-password" placeholder="Nueva contraseña" />
        </div>
        @error('password')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="ui-label" for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-key-fill"></i></span>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="ui-input" autocomplete="new-password" placeholder="Confirma la contraseña" />
        </div>
        @error('password_confirmation')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
    </div>

    <div class="d-flex align-items-center gap-4 mt-4">
        <button type="submit" class="ui-btn ui-btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('Save') }}</button>

        @if (session('status') === 'password-updated')
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
