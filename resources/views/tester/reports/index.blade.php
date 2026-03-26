{{-- resources/views/tester/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Test Reports')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="fas fa-chart-line me-2"></i> Test Reports
                            </h4>
                            <p class="mb-0 opacity-75">Comprehensive analysis of all test results and performance metrics</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-bar fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-filter me-2 text-primary"></i> Filter Reports
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('tester.reports') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> Start Date
                                </label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}"
                                    max="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i> End Date
                                </label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}"
                                    max="{{ now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">
                                    <i class="fas fa-file-alt me-1"></i> Test
                                </label>
                                <select name="test_id" class="form-select">
                                    <option value="">All Tests</option>
                                    @foreach($tests as $test)
                                    <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                        {{ $test->test_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">
                                    <i class="fas fa-user me-1"></i> Participant
                                </label>
                                <select name="applicant_id" class="form-select">
                                    <option value="">All Participants</option>
                                    @foreach($applicants as $applicant)
                                    <option value="{{ $applicant->id }}" {{ request('applicant_id') == $applicant->id ? 'selected' : '' }}>
                                        {{ $applicant->full_name }} ({{ $applicant->participant_numb }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i> Apply Filter
                                        </button>
                                        <a href="{{ route('tester.reports') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-sync me-1"></i> Reset
                                        </a>
                                    </div>
                                    <div>
                                        <a href="{{ route('tester.reports.export-excel', request()->query()) }}"
                                            class="btn btn-success">
                                            <i class="fas fa-file-excel me-1"></i> Export Excel
                                        </a>
                                        <a href="{{ route('tester.reports.export-pdf', request()->query()) }}"
                                            class="btn btn-danger ms-2">
                                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card bg-primary text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Total Tests</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($summary['total_sessions']) }}</h2>
                            <small class="opacity-75">completed sessions</small>
                        </div>
                        <i class="fas fa-chalkboard-user fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card bg-success text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Average Score</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($summary['average_score'], 0) }}</h2>
                            <small class="opacity-75">out of 300 points</small>
                        </div>
                        <i class="fas fa-star fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card bg-info text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Total Answers</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($summary['total_answers']) }}</h2>
                            <small class="opacity-75">questions answered</small>
                        </div>
                        <i class="fas fa-list-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card bg-warning text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Overall Accuracy</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($summary['accuracy'], 1) }}%</h2>
                            <small class="opacity-75">correct answers</small>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-bar me-2 text-primary"></i> Performance by Test
                    </h6>
                </div>
                <div class="card-body">
                    @if($performanceByTest->count() > 0)
                    <canvas id="performanceByTestChart" style="height: 300px; width: 100%;"></canvas>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No data available for performance by test</p>
                        <small class="text-muted">Complete some tests to see statistics</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line me-2 text-primary"></i> Performance Trend (Last 30 Days)
                    </h6>
                </div>
                <div class="card-body">
                    @if($performanceByDate->count() > 0)
                    <canvas id="performanceTrendChart" style="height: 300px; width: 100%;"></canvas>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No data available for performance trend</p>
                        <small class="text-muted">Complete some tests to see trends</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-table me-2 text-primary"></i> Test Results Details
                        </h6>
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">
                            <i class="fas fa-database me-1"></i> {{ $sessions->total() }} total records
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr class="small text-uppercase text-muted">
                                    <th class="py-3 px-3">Date</th>
                                    <th class="py-3 px-3">Participant</th>
                                    <th class="py-3 px-3">Test</th>
                                    <th class="py-3 px-3 text-center">Score</th>
                                    <th class="py-3 px-3 text-center">Accuracy</th>
                                    <th class="py-3 px-3 text-center">Time</th>
                                    <th class="py-3 px-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                <tr>
                                    <td class="px-3">
                                        <i class="far fa-calendar-alt text-muted me-1"></i>
                                        <strong>{{ $session->created_at->format('d/m/Y') }}</strong><br>
                                        <small class="text-muted">{{ $session->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-secondary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $session->applicant->full_name }}</strong><br>
                                                <small class="text-muted">{{ $session->applicant->participant_numb }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3">
                                        <span class="badge bg-info px-2 py-1 rounded-pill">{{ $session->test->test_code }}</span><br>
                                        <small class="text-muted">{{ Str::limit($session->test->test_name, 30) }}</small>
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-success px-3 py-2 rounded-pill fs-6 fw-semibold">{{ number_format($session->score) }}</span>
                                    </td>
                                    <td class="px-3">
                                        @php
                                        $total = $session->answers()->count();
                                        $correct = $session->answers()->where('is_correct', true)->count();
                                        $accuracy = $total > 0 ? ($correct / $total) * 100 : 0;
                                        $colorClass = $accuracy >= 80 ? 'success' : ($accuracy >= 60 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="progress mx-auto" style="height: 30px; width: 100px;">
                                            <div class="progress-bar bg-{{ $colorClass }} d-flex align-items-center justify-content-center fw-semibold"
                                                style="width: {{ $accuracy }}%">
                                                {{ number_format($accuracy, 0) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 text-center">
                                        @if($session->start_time && $session->end_time)
                                        <i class="fas fa-hourglass-half text-muted me-1"></i>
                                        {{ $session->start_time->diffInMinutes($session->end_time) }} min
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('pauli-test.result', $session->id) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="View Result">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                            <button onclick="printResult({{ $session->id }})"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Print">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <h6 class="text-muted">No test results found</h6>
                                        <p class="text-muted small mb-0">Try adjusting your filters or complete some tests first</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sessions->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-center">
                        {{ $sessions->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
        border-radius: 1rem;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .avatar-sm {
        width: 35px;
        height: 35px;
        background: #f8f9fa;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .table> :not(caption)>*>* {
        vertical-align: middle;
    }

    .progress {
        border-radius: 20px;
        background-color: #e9ecef;
    }

    .progress-bar {
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .btn-outline-info {
        border-color: #e9ecef;
        color: #6c757d;
    }

    .btn-outline-info:hover {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: white;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    @media (max-width: 768px) {
        .stat-card h2 {
            font-size: 1.5rem;
        }

        .table-responsive {
            font-size: 0.75rem;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
    }

    @media (max-width: 576px) {
        .stat-card .card-body {
            padding: 1rem;
        }

        .progress {
            width: 80px !important;
            height: 25px !important;
        }

        .progress-bar {
            font-size: 9px;
        }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Performance by Test Chart Data
    <?php
    $testLabels = json_encode($performanceByTest->pluck('test.test_name'));
    $testScores = json_encode($performanceByTest->pluck('avg_score'));
    ?>

    var testLabels = <?php echo $testLabels; ?>;
    var testScores = <?php echo $testScores; ?>;

    // Create Performance by Test Chart
    if (testLabels.length > 0) {
        const ctx1 = document.getElementById('performanceByTestChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: testLabels,
                datasets: [{
                    label: 'Average Score',
                    data: testScores,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.toFixed(1) + ' points';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 300,
                        title: {
                            display: true,
                            text: 'Average Score',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Test Name',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }

    // Performance Trend Chart Data
    <?php
    $dateLabels = json_encode($performanceByDate->pluck('date'));
    $dateScores = json_encode($performanceByDate->pluck('avg_score'));
    $dateCounts = json_encode($performanceByDate->pluck('total'));
    ?>

    var dateLabels = <?php echo $dateLabels; ?>;
    var dateScores = <?php echo $dateScores; ?>;
    var dateCounts = <?php echo $dateCounts; ?>;

    // Create Performance Trend Chart
    if (dateLabels.length > 0) {
        const ctx2 = document.getElementById('performanceTrendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: dateLabels,
                datasets: [{
                        label: 'Average Score',
                        data: dateScores,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y',
                        pointBackgroundColor: 'rgb(75, 192, 192)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Number of Tests',
                        data: dateCounts,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                        pointBackgroundColor: 'rgb(255, 99, 132)',
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
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw;
                                if (context.dataset.label === 'Average Score') {
                                    return label + ': ' + value.toFixed(1) + ' points';
                                }
                                return label + ': ' + value + ' tests';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 300,
                        title: {
                            display: true,
                            text: 'Average Score',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    y1: {
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Tests',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }

    function printResult(sessionId) {
        window.open('/pauli-test/result/' + sessionId + '/print', '_blank');
    }
</script>
@endpush
@endsection