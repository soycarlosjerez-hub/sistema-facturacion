@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label d-block fw-medium fs-6 text-dark']) }}>
    {{ $value ?? $slot }}
</label>
