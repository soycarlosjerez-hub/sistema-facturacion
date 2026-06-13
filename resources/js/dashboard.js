import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.body.classList.contains('dark-mode');
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    // Live clock
    function updateClock() {
        const el = document.getElementById('live-clock');
        if (el) {
            const d = new Date();
            el.textContent = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        }
    }
    setInterval(updateClock, 30000);

    // Ventas 30 días
    const ventasCanvas = document.getElementById('ventasChart');
    if (ventasCanvas) {
        const ventasCtx = ventasCanvas.getContext('2d');
        const grad = ventasCtx.createLinearGradient(0, 0, 0, 320);
        grad.addColorStop(0, isDark ? 'rgba(56,189,248,0.5)' : 'rgba(13,110,253,0.35)');
        grad.addColorStop(1, 'rgba(56,189,248,0)');

        new Chart(ventasCtx, {
            type: 'line',
            data: {
                labels: window.dashboardData?.chartLabels ?? [],
                datasets: [{
                    label: 'Ventas',
                    data: window.dashboardData?.chartData ?? [],
                    borderColor: isDark ? '#38bdf8' : '#0d6efd',
                    backgroundColor: grad,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: isDark ? '#38bdf8' : '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#fff',
                        titleColor: isDark ? '#f8fafc' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                    y: { grid: { color: gridColor, borderDash: [5, 5] }, ticks: { color: textColor, font: { size: 10 } }, beginAtZero: true }
                }
            }
        });
    }

    // Ventas por hora
    const horasCanvas = document.getElementById('horasChart');
    if (horasCanvas) {
        const horasCtx = horasCanvas.getContext('2d');
        new Chart(horasCtx, {
            type: 'bar',
            data: {
                labels: window.dashboardData?.hourlyLabels ?? [],
                datasets: [{
                    data: window.dashboardData?.hourlyData ?? [],
                    backgroundColor: isDark ? 'rgba(56,189,248,0.6)' : 'rgba(13,110,253,0.7)',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9 } } },
                    y: { grid: { color: gridColor, borderDash: [5, 5] }, ticks: { color: textColor, font: { size: 9 } }, beginAtZero: true }
                }
            }
        });
    }

    // Métodos de pago
    const paymentCanvas = document.getElementById('paymentChart');
    if (paymentCanvas) {
        const paymentCtx = paymentCanvas.getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: window.dashboardData?.paymentLabels ?? [],
                datasets: [{
                    data: window.dashboardData?.paymentData ?? [],
                    backgroundColor: window.dashboardData?.paymentColors ?? ['#22c55e', '#6366f1', '#f59e0b', '#38bdf8'],
                    borderWidth: 2,
                    borderColor: isDark ? '#0f172a' : '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#fff',
                        titleColor: isDark ? '#f8fafc' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                    }
                }
            }
        });
    }
});
