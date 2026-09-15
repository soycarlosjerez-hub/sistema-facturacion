<script>
window.dashboardData = {
    chartLabels: @json($chartData['labels']),
    chartData: @json($chartData['data']),
    hourlyLabels: @json($hourlyData['labels']),
    hourlyData: @json($hourlyData['data']),
    paymentLabels: @json($paymentMethod['labels']),
    paymentData: @json($paymentMethod['data']),
    paymentColors: @json($paymentMethod['colors']),
};
</script>

@push('scripts')
    @vite(['resources/js/dashboard.js'])
@endpush
