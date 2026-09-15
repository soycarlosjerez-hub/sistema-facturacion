{{--
    Reusable Premium Glassmorphism Modal Component
    
    Usage:
    @include('partials.modal-generic', [
        'id' => 'myModal',
        'title' => 'Title',
        'icon' => 'bi-exclamation',
        'color' => 'primary',       // primary|danger|warning|success|info
        'size' => 'md',             // sm|md|lg|xl (default md)
        'footer' => true,           // show footer (default true)
        'formAction' => route('xxx'),// optional: wraps content in form
        'formMethod' => 'POST',     // optional: POST|PUT|PATCH|DELETE
        'closeOnBackdrop' => true,  // optional: default true
        'closeOnEsc' => true,       // optional: default true
    ])
        <!-- Modal body content -->
    @endinclude
--}}

@php
    $colors = [
        'primary' => [
            'gradient' => 'linear-gradient(135deg, #3b82f6, #2563eb, #3b82f6)',
            'hex' => '#3b82f6',
            'dark_hex' => '#60a5fa',
        ],
        'danger' => [
            'gradient' => 'linear-gradient(135deg, #ef4444, #dc2626, #ef4444)',
            'hex' => '#ef4444',
            'dark_hex' => '#f87171',
        ],
        'warning' => [
            'gradient' => 'linear-gradient(135deg, #f59e0b, #d97706, #f59e0b)',
            'hex' => '#f59e0b',
            'dark_hex' => '#fbbf24',
        ],
        'success' => [
            'gradient' => 'linear-gradient(135deg, #22c55e, #16a34a, #22c55e)',
            'hex' => '#22c55e',
            'dark_hex' => '#4ade80',
        ],
        'info' => [
            'gradient' => 'linear-gradient(135deg, #06b6d4, #0891b2, #06b6d4)',
            'hex' => '#06b6d4',
            'dark_hex' => '#22d3ee',
        ],
    ];
    
    $colorData = $colors[$color ?? 'primary'] ?? $colors['primary'];
    $sizes = ['sm' => 'modal-sm', 'md' => '', 'lg' => 'modal-lg', 'xl' => 'modal-xl'];
    $sizeClass = $sizes[$size ?? 'md'] ?? '';
    $formMethod = strtoupper($formMethod ?? '');
@endphp

<style>
/* Modal Animations */
@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(30px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes bubbleFloat {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.15; }
    25% { transform: translate(10px, -15px) scale(1.1); opacity: 0.2; }
    50% { transform: translate(-5px, -25px) scale(0.95); opacity: 0.12; }
    75% { transform: translate(15px, -10px) scale(1.05); opacity: 0.18; }
}

/* Modal Container */
.modal-content.reusable-modal {
    border: none;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
    animation: modalSlideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Animated Gradient Header */
.modal-header.reusable-header {
    background: {{ $colorData['gradient'] }};
    background-size: 200% 200%;
    animation: gradientShift 4s ease infinite;
    border: none;
    padding: 1.25rem 1.5rem;
    position: relative;
    overflow: hidden;
}

.modal-header.reusable-header .modal-title {
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    position: relative;
    z-index: 2;
}

.modal-header.reusable-header .modal-title i {
    font-size: 1.3rem;
    opacity: 0.9;
}

.modal-header.reusable-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    position: relative;
    z-index: 2;
    transition: opacity 0.2s;
}

.modal-header.reusable-header .btn-close:hover {
    opacity: 1;
}

/* Decorative Bubbles in Header */
.modal-header.reusable-header .modal-bubble {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    pointer-events: none;
    animation: bubbleFloat 6s ease-in-out infinite;
}

.modal-header.reusable-header .modal-bubble:nth-child(1) {
    width: 80px; height: 80px;
    top: -20px; right: 10%;
    animation-duration: 7s;
}

.modal-header.reusable-header .modal-bubble:nth-child(2) {
    width: 50px; height: 50px;
    bottom: -10px; right: 30%;
    animation-duration: 5s;
    animation-delay: 1s;
}

.modal-header.reusable-header .modal-bubble:nth-child(3) {
    width: 35px; height: 35px;
    top: 5px; right: 50%;
    animation-duration: 8s;
    animation-delay: 2s;
}

/* Frosted Glass Body */
.modal-body.reusable-body {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    padding: 1.75rem;
    border: none;
}

/* Accent Strip on Left Side */
.reusable-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: {{ $colorData['gradient'] }};
    border-radius: 1rem 0 0 1rem;
    z-index: 10;
}

/* Footer Buttons */
.modal-footer.reusable-footer {
    border: none;
    padding: 1rem 1.75rem 1.25rem;
    gap: 0.75rem;
}

.modal-footer.reusable-footer .btn-cancel {
    border: 2px solid #e2e8f0;
    color: #64748b;
    font-weight: 600;
    border-radius: 0.75rem;
    padding: 0.6rem 1.5rem;
    transition: all 0.2s;
    background: transparent;
}

.modal-footer.reusable-footer .btn-cancel:hover {
    border-color: #cbd5e1;
    color: #475569;
    background: #f8fafc;
}

.modal-footer.reusable-footer .btn-submit {
    background: {{ $colorData['gradient'] }};
    background-size: 200% 200%;
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 0.75rem;
    padding: 0.6rem 1.5rem;
    transition: all 0.2s;
    box-shadow: 0 4px 14px {{ $colorData['hex'] }}33;
}

.modal-footer.reusable-footer .btn-submit:hover {
    box-shadow: 0 6px 20px {{ $colorData['hex'] }}55;
    transform: translateY(-1px);
}

/* Dark Mode Overrides */
body.dark-mode .modal-content.reusable-modal {
    background: #0f172a;
}

body.dark-mode .modal-body.reusable-body {
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

body.dark-mode .modal-footer.reusable-footer .btn-cancel {
    border-color: #334155;
    color: #94a3b8;
    background: transparent;
}

body.dark-mode .modal-footer.reusable-footer .btn-cancel:hover {
    border-color: #475569;
    color: #cbd5e1;
    background: #1e293b;
}

body.dark-mode .modal-body.reusable-body .form-control,
body.dark-mode .modal-body.reusable-body .form-select,
body.dark-mode .modal-body.reusable-body textarea {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}

body.dark-mode .modal-body.reusable-body .form-control:focus,
body.dark-mode .modal-body.reusable-body .form-select:focus,
body.dark-mode .modal-body.reusable-body textarea:focus {
    background: #1e293b;
    border-color: {{ $colorData['dark_hex'] }};
    color: #e2e8f0;
    box-shadow: 0 0 0 3px {{ $colorData['hex'] }}22;
}

body.dark-mode .modal-body.reusable-body label {
    color: #cbd5e1;
}

body.dark-mode .modal-body.reusable-body .form-text {
    color: #64748b;
}

body.dark-mode .modal-body.reusable-body .alert {
    background: rgba(30, 41, 59, 0.6);
    border-color: #334155;
}
</style>

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label"
     data-bs-backdrop="{{ $closeOnBackdrop ?? true ? 'static' : 'false' }}"
     data-bs-keyboard="{{ $closeOnEsc ?? true ? 'true' : 'false' }}">
    <div class="modal-dialog {{ $sizeClass }} modal-dialog-centered">
        <div class="modal-content reusable-modal">
            
            @if($formAction)
                <form action="{{ $formAction }}" method="POST" id="{{ $id }}Form">
                    @csrf
                    @if(in_array($formMethod, ['PUT', 'PATCH', 'DELETE']))
                        @method($formMethod)
                    @endif
            @endif
            
            <div class="modal-header reusable-header">
                <div class="modal-bubble"></div>
                <div class="modal-bubble"></div>
                <div class="modal-bubble"></div>
                <h5 class="modal-title" id="{{ $id }}Label">
                    <i class="bi {{ $icon ?? 'bi-info-circle' }}"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body reusable-body">
                {{ $slot }}
            </div>
            
            @if($footer ?? true)
            <div class="modal-footer reusable-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-submit" id="{{ $id }}Submit">
                    <i class="bi bi-check-lg me-1"></i>Guardar
                </button>
            </div>
            @endif
            
            @if($formAction)
                </form>
            @endif
        </div>
    </div>
</div>

@push('modal-scripts')
<script>
(function() {
    var modalEl = document.getElementById('{{ $id }}');
    if (!modalEl) return;
    
    modalEl.addEventListener('shown.bs.modal', function() {
        var firstInput = modalEl.querySelector('input:not([type="hidden"]), select, textarea');
        if (firstInput) firstInput.focus();
    });
})();
</script>
@endpush
