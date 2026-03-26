{{-- resources/views/applicant/partials/performance-chart.blade.php --}}
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-chart-line me-2"></i> Performance Trend</h5>
    </div>
    <div class="card-body">
        <canvas id="performanceTrendChart" height="300"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const trendData = @json($testSessions - > where('status', 'completed') - > values());

    if (trendData.length > 0) {
        new Chart(document.getElementById('performanceTrendChart'), {
            type: 'line',
            data: {
                labels: trendData.map(item => item.created_at.format('d M Y')),
                datasets: [{
                    label: 'Test Score',
                    data: trendData.map(item => item.score),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Score'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Test Date'
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('performanceTrendChart').parentNode.innerHTML =
            '<div class="text-center py-5"><i class="fas fa-chart-line fa-3x text-muted mb-3"></i><p class="text-muted">Complete a test to see your performance trend</p></div>';
    }
</script>
@endpush