{{-- resources/views/tester/sessions/partials/performance-charts.blade.php --}}
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i> Performance by Line (Every 3 Minutes)</h6>
            </div>
            <div class="card-body">
                <canvas id="linePerformanceChart" height="300"></canvas>
                <div class="table-responsive mt-3">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Line #</th>
                                <th>Time</th>
                                <th>Total Answers</th>
                                <th>Correct Answers</th>
                                <th>Accuracy</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($answersByLine as $index => $line)
                            <tr>
                                <td class="text-center"><strong>{{ $line->line_marker }}</strong></td>
                                <td>{{ $index * 3 }} - {{ ($index + 1) * 3 }} minutes</td>
                                <td>{{ $line->total }}</td>
                                <td>{{ $line->correct }}</td>
                                <td>
                                    @php
                                    $accuracy = $line->total > 0 ? ($line->correct / $line->total) * 100 : 0;
                                    $barColor = $accuracy >= 80 ? 'success' : ($accuracy >= 60 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $barColor }}"
                                            style="width: {{ $accuracy }}%">
                                            {{ number_format($accuracy, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($accuracy >= 80)
                                    <span class="badge bg-success">Excellent</span>
                                    @elseif($accuracy >= 60)
                                    <span class="badge bg-warning">Good</span>
                                    @elseif($accuracy >= 40)
                                    <span class="badge bg-info">Average</span>
                                    @else
                                    <span class="badge bg-danger">Poor</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Performance by Column</h6>
            </div>
            <div class="card-body">
                <canvas id="columnPerformanceChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    const lineCtx = document.getElementById('linePerformanceChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: @json($answersByLine - > pluck('line_marker') - > map(function($item) {
                return "Garis $item";
            })),
            datasets: [{
                    label: 'Total Answers',
                    data: @json($answersByLine - > pluck('total')),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Correct Answers',
                    data: @json($answersByLine - > pluck('correct')),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 10
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#ddd'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    title: {
                        display: true,
                        text: 'Number of Answers',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Time Intervals (3 minutes each)',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });

    // Column Chart
    const columnCtx = document.getElementById('columnPerformanceChart').getContext('2d');
    new Chart(columnCtx, {
        type: 'bar',
        data: {
            labels: @json($answersByColumn - > pluck('column_number')),
            datasets: [{
                    label: 'Total Answers',
                    data: @json($answersByColumn - > pluck('total')),
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: 'rgb(153, 102, 255)',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Correct Answers',
                    data: @json($answersByColumn - > pluck('correct')),
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                    borderColor: 'rgb(255, 159, 64)',
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            let total = context.dataset.label === 'Total Answers' ? value :
                                @json($answersByColumn - > pluck('total'))[context.dataIndex];
                            let percentage = context.dataset.label === 'Correct Answers' ?
                                (value / total * 100).toFixed(1) : '';
                            return label + ': ' + value + (percentage ? ' (' + percentage + '%)' : '');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    title: {
                        display: true,
                        text: 'Number of Answers',
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Column Number',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
</script>
@endpush