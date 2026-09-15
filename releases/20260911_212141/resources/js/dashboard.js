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

    // ─── Owner Dashboard Charts ───────────────────────────────────

    if (!window.ownerDashboard) return;

    // 1. MRR Trend — Line Chart
    const mrrCanvas = document.getElementById('mrrTrendChart');
    if (mrrCanvas) {
        const mrrCtx = mrrCanvas.getContext('2d');
        const mrrGradient = mrrCtx.createLinearGradient(0, 0, 0, 220);
        mrrGradient.addColorStop(0, 'rgba(139, 92, 246, 0.25)');
        mrrGradient.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

        new Chart(mrrCtx, {
            type: 'line',
            data: {
                labels: window.ownerDashboard.mrrLabels,
                datasets: [{
                    label: 'MRR Cobrado',
                    data: window.ownerDashboard.mrrData,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    borderColor: '#8b5cf6',
                    backgroundColor: mrrGradient,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#8b5cf6',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,.95)',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: ctx => 'MRR: RD$ ' + ctx.parsed.y.toLocaleString('es-DO', {minimumFractionDigits: 2})
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { size: 10 }, callback: v => 'RD$ ' + (v >= 1000 ? (v/1000).toFixed(1) + 'K' : v) }
                    },
                    x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9 } } }
                }
            }
        });
    }

    // 2. Revenue Trend — Bar Chart
    const revCanvas = document.getElementById('revenueTrendChart');
    if (revCanvas) {
        const revCtx = revCanvas.getContext('2d');
        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: window.ownerDashboard.revenueLabels,
                datasets: [{
                    label: 'Ingresos Cobrados',
                    data: window.ownerDashboard.revenueData,
                    backgroundColor: 'rgba(16, 185, 129, .75)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,.95)',
                        callbacks: {
                            label: ctx => 'Ingresos: RD$ ' + ctx.parsed.y.toLocaleString('es-DO', {minimumFractionDigits: 2})
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { size: 10 }, callback: v => 'RD$ ' + (v >= 1000 ? (v/1000).toFixed(0) + 'K' : v) },
                        beginAtZero: true
                    },
                    x: { grid: { display: false }, ticks: { color: textColor, font: { size: 9 } } }
                }
            }
        });
    }

    // 3. Errors by Level — Doughnut Chart
    const errCanvas = document.getElementById('errorsChart');
    if (errCanvas) {
        const ed = window.ownerDashboard.errorData;
        new Chart(errCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Crítico', 'Error', 'Advertencia', 'Info'],
                datasets: [{
                    data: [ed[0] || 0, ed[1] || 0, ed[2] || 0, ed[3] || 0],
                    backgroundColor: ['#ef4444', '#dc2626', '#f59e0b', '#3b82f6'],
                    borderWidth: 2,
                    borderColor: isDark ? '#0f172a' : '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true, pointStyleWidth: 8, color: textColor, font: { size: 10 } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,.95)',
                        callbacks: {
                            label: ctx => ctx.label + ': ' + (ctx.parsed || 0)
                        }
                    }
                }
            }
        });
    }

    // 4. Instance Health — Donut Chart
    const healthCanvas = document.getElementById('instanceHealthChart');
    if (healthCanvas) {
        const hd = window.ownerDashboard.healthData;
        const healthLabels = ['Activas', 'En Prueba', 'Bloqueadas', 'Churn Risk', 'Pendientes', 'Archivadas'];
        const healthData = [hd.active, hd.trial, hd.blocked, hd.churnRisk, hd.pending, hd.archived];
        const healthColors = ['#10b981', '#3b82f6', '#ef4444', '#f59e0b', '#94a3b8', '#64748b'];

        new Chart(healthCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: healthLabels,
                datasets: [{
                    data: healthData,
                    backgroundColor: healthColors,
                    borderWidth: 2,
                    borderColor: isDark ? '#0f172a' : '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 10, usePointStyle: true, pointStyleWidth: 8, color: textColor, font: { size: 10 } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,.95)',
                        callbacks: {
                            label: ctx => ctx.label + ': ' + ctx.parsed
                        }
                    }
                }
            }
        });
    }

    // 5. Plan Distribution — Horizontal Bar Chart
    const planCanvas = document.getElementById('planDistributionChart');
    if (planCanvas) {
        const pd = window.ownerDashboard.planDistribution;
        const planNames = Object.keys(pd);
        const planValues = Object.values(pd);
        const planColors = ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899', '#84cc16'];

        new Chart(planCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: planNames,
                datasets: [{
                    data: planValues,
                    backgroundColor: planNames.map((_, i) => planColors[i % planColors.length]),
                    borderRadius: 4,
                    borderSkipped: false,
                    barThickness: 18,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,.95)',
                        callbacks: {
                            label: ctx => 'Instancias: ' + ctx.parsed.x
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { size: 10 } },
                        beginAtZero: true
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { size: 10 } }
                    }
                }
            }
        });
    }
});
