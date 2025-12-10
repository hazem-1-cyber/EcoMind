// Toggle sidebar on mobile
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Initialize charts
    initLineChart();
    initDonutChart();
});

// Line Chart
function initLineChart() {
    const ctx = document.getElementById('lineChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h'],
            datasets: [{
                label: 'Actions Écologiques',
                data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 40],
                borderColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) {
                        return '#013220';
                    }
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, '#013220');
                    gradient.addColorStop(1, '#A8E6CF');
                    return gradient;
                },
                backgroundColor: 'rgba(1, 50, 32, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#A8E6CF',
                pointBorderColor: '#013220',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#013220',
                pointHoverBorderColor: '#A8E6CF',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#A8E6CF',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10,
                        color: '#013220',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(1, 50, 32, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#013220',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Donut Chart
function initDonutChart() {
    const ctx = document.getElementById('donutChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Réalisé', 'En cours', 'Planifié'],
            datasets: [{
                data: [60, 25, 15],
                backgroundColor: [
                    '#013220',
                    '#A8E6CF',
                    '#A8E6CF'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#A8E6CF',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        },
        plugins: [{
            id: 'centerText',
            beforeDraw: function(chart) {
                const ctx = chart.ctx;
                const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                
                ctx.save();
                ctx.font = 'bold 24px Segoe UI';
                ctx.fillStyle = '#013220';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('80%', centerX, centerY - 10);
                
                ctx.font = '14px Segoe UI';
                ctx.fillStyle = '#013220';
                ctx.fillText('Transactions', centerX, centerY + 15);
                ctx.restore();
            }
        }]
    });
}
