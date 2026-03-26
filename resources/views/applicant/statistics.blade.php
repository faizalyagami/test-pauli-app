{{-- resources/views/applicant/statistics.blade.php --}}
@extends('layouts.app')

@section('title', 'My Statistics')

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
                                <i class="fas fa-chart-line me-2"></i> My Statistics
                            </h4>
                            <p class="mb-0 opacity-75">Track your performance and progress over time</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-bar fa-3x opacity-50"></i>
                        </div>
                    </div>
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
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['total_tests'] ?? 0) }}</h2>
                            <small class="opacity-75">tests taken</small>
                        </div>
                        <i class="fas fa-file-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card bg-success text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Completed Tests</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['completed_tests'] ?? 0) }}</h2>
                            <small class="opacity-75">finished</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card bg-info text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Average Score</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['average_score'] ?? 0, 0) }}</h2>
                            <small class="opacity-75">points average</small>
                        </div>
                        <i class="fas fa-star fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card stat-card bg-warning text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Best Score</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['best_score'] ?? 0, 0) }}</h2>
                            <small class="opacity-75">personal best</small>
                        </div>
                        <i class="fas fa-trophy fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie me-2 text-primary"></i> Overall Accuracy
                    </h6>
                </div>
                <div class="card-body">
                    @php
                    $totalAnswers = $stats['total_answers'] ?? 0;
                    $correctAnswers = $stats['correct_answers'] ?? 0;
                    $accuracyPercentage = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;
                    @endphp

                    @if($totalAnswers > 0)
                    <canvas id="accuracyChart" height="250"></canvas>
                    <div class="text-center mt-3">
                        <h3 class="text-success">{{ number_format($accuracyPercentage, 1) }}%</h3>
                        <p class="text-muted">Overall Accuracy</p>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $accuracyPercentage }}%"></div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <small class="text-success">Correct: {{ number_format($correctAnswers) }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-danger">Wrong: {{ number_format($totalAnswers - $correctAnswers) }}</small>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No data available</p>
                        <small class="text-muted">Complete a test to see statistics</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line me-2 text-primary"></i> Performance Trend (Last 30 Days)
                    </h6>
                </div>
                <div class="card-body">
                    @if($performanceOverTime->count() > 0)
                    <canvas id="performanceChart" height="250"></canvas>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No data available</p>
                        <small class="text-muted">Complete some tests to see trends</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Test History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-history me-2 text-primary"></i> Test History
                    </h6>
                    <a href="{{ route('applicant.history') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr class="small text-uppercase text-muted">
                                    <th class="py-3 px-3">Date</th>
                                    <th class="py-3 px-3">Test</th>
                                    <th class="py-3 px-3 text-center">Score</th>
                                    <th class="py-3 px-3 text-center">Accuracy</th>
                                    <th class="py-3 px-3 text-center">Time</th>
                                    <th class="py-3 px-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $recentSessions = \App\Models\TestSession::where('applicant_id', $applicant->id)
                                ->with('test')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                                @endphp
                                @forelse($recentSessions as $session)
                                @php
                                $total = $session->answers()->count();
                                $correct = $session->answers()->where('is_correct', true)->count();
                                $accuracy = $total > 0 ? ($correct / $total) * 100 : 0;
                                $colorClass = $accuracy >= 80 ? 'success' : ($accuracy >= 60 ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td class="px-3">
                                        <i class="far fa-calendar-alt text-muted me-1"></i>
                                        {{ $session->created_at->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $session->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="px-3">
                                        <strong>{{ $session->test->test_name }}</strong><br>
                                        <small class="text-muted">{{ $session->test->test_code }}</small>
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-success px-3 py-2">{{ number_format($session->score) }}</span>
                                    </td>
                                    <td class="px-3 text-center">
                                        <div class="progress mx-auto" style="height: 30px; width: 100px;">
                                            <div class="progress-bar bg-{{ $colorClass }} d-flex align-items-center justify-content-center fw-semibold"
                                                style="width: {{ $accuracy }}%">
                                                {{ number_format($accuracy, 0) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 text-center">
                                        @if($session->start_time && $session->end_time)
                                        {{ $session->start_time->diffInMinutes($session->end_time) }} min
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="px-3 text-center">
                                        <a href="{{ route('applicant.history.show', $session) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-chart-line"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No test history found</p>
                                        <small class="text-muted">Take your first test to see results</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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

    .progress {
        border-radius: 20px;
        background-color: #e9ecef;
    }

    .progress-bar {
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .table> :not(caption)>*>* {
        vertical-align: middle;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Accuracy Chart
    const totalAnswers = {
        {
            $stats['total_answers'] ?? 0
        }
    };
    const correctAnswers = {
        {
            $stats['correct_answers'] ?? 0
        }
    };
    const wrongAnswers = totalAnswers - correctAnswers;

    if (totalAnswers > 0) {
        new Chart(document.getElementById('accuracyChart'), {
            type: 'doughnut',
            data: {
                labels: ['Correct', 'Wrong'],
                datasets: [{
                    data: [correctAnswers, wrongAnswers],
                    backgroundColor: ['#27ae60', '#e74c3c'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            },
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    }
                }
            }
        });
    }

    // Performance Trend Chart
    <?php
    $dateLabels = json_encode($performanceOverTime->pluck('date'));
    $scoreData = json_encode($performanceOverTime->pluck('score'));
    ?>

    var dateLabels = <?php echo $dateLabels; ?>;
    var scoreData = <?php echo $scoreData; ?>;

    if (dateLabels.length > 0) {
        new Chart(document.getElementById('performanceChart'), {
            type: 'line',
            data: {
                labels: dateLabels,
                datasets: [{
                    label: 'Test Score',
                    data: scoreData,
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
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' points';
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
                            text: 'Score'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Test Date'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
@endpush