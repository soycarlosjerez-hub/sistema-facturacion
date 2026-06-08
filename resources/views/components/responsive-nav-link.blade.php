@props(['active'])

@php
$classes = ($active ?? false) ? 'd-block w-100 nav-link active py-2' : 'd-block w-100 nav-link py-2';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
