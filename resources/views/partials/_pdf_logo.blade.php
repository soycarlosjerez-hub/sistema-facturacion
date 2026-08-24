@php
    $empresa = \App\Models\SystemSetting::allCached();
    $nombreEmpresa = \App\Models\SystemSetting::nombreEmpresaActual();
@endphp
@if($pdfLogoUrl)
<div style="margin-bottom: 4px;">
    <img src="{{ $pdfLogoUrl }}" style="max-width: 70px; max-height: 55px; object-fit: contain;" alt="Logo">
</div>
@endif
