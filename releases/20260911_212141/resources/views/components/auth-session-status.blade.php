@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success border-0 rounded-3 shadow-sm']) }}>
        <i class="bi bi-check-circle me-1"></i> {{ $status }}
    </div>
@endif
