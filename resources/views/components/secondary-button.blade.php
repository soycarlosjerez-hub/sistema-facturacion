<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-secondary fw-semibold rounded-pill']) }}>
    {{ $slot }}
</button>
