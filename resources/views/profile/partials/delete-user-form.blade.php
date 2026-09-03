<section class="space-y-6">
    <p class="text-secondary fs-6 mb-4">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <button type="button" class="ui-btn ui-btn-danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        <i class="bi bi-trash3 me-1"></i>{{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="p-5">
            <h2 class="fs-5 font-weight-bold mb-3 text-dark">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="fs-6 text-secondary mb-4">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div>
                <label class="ui-label" for="password">{{ __('Password') }}</label>
                <div class="ui-input-group">
                    <span class="ui-input-group-text"><i class="bi bi-lock"></i></span>
                    <input id="password" name="password" type="password" class="ui-input" placeholder="{{ __('Password') }}" />
                </div>
                @error('password')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end mt-4 gap-2">
                <button type="button" class="ui-btn ui-btn-ghost" x-on:click="$dispatch('close')">
                    <i class="bi bi-x-lg me-1"></i>{{ __('Cancel') }}
                </button>

                <button type="submit" class="ui-btn ui-btn-danger">
                    <i class="bi bi-trash3 me-1"></i>{{ __('Delete Account') }}
                </button>
            </div>
        </div>
    </x-modal>
</section>
