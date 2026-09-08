@extends('layouts.app')

@section('title', 'Tester Dashboard')

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
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </h4>
                                <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}! Here's what's happening
                                    with your tests today.</p>
                            </div>
                            <div>
                                <i class="fas fa-chart-line fa-3x opacity-50"></i>
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
                                <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Total Applicants</h6>
                                <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['total_applicants'] ?? 0) }}</h2>
                                <small class="opacity-75">registered participants</small>
                            </div>
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card stat-card bg-success text-white border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Total Tests</h6>
                                <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['total_tests'] ?? 0) }}</h2>
                                <small class="opacity-75">test sessions</small>
                            </div>
                            <i class="fas fa-file-alt fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card stat-card bg-info text-white border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Completed Tests</h6>
                                <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['completed_tests'] ?? 0) }}</h2>
                                <small class="opacity-75">finished sessions</small>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card stat-card bg-warning text-white border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Average Score</h6>
                                <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['average_score'] ?? 0, 0) }}</h2>
                                <small class="opacity-75">points average</small>
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
                            <i class="fas fa-chart-pie me-2 text-primary"></i> Test Status Distribution
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($testByStatus->count() > 0)
                            <canvas id="statusChart" style="height: 300px; width: 100%;"></canvas>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No data available for test status</p>
                                <small class="text-muted">Start some tests to see distribution</small>
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
                        @if (isset($performanceByDate) && $performanceByDate->count() > 0)
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

        <!-- Recent Activities Section -->
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-history me-2 text-primary"></i> Recent Test Sessions
                            </h6>
                            <a href="{{ route('tester.sessions') }}" class="btn btn-sm btn-link text-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light small text-uppercase text-muted">
                                    <tr>
                                        <th class="py-3 px-3">Date</th>
                                        <th class="py-3 px-3">Participant</th>
                                        <th class="py-3 px-3">Test</th>
                                        <th class="py-3 px-3 text-center">Score</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSessions as $session)
                                        <tr>
                                            <td class="px-3">
                                                <small>{{ $session->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td class="px-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div
                                                        class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-user text-secondary"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $session->applicant->full_name }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ $session->applicant->participant_numb }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3">
                                                <span
                                                    class="badge bg-info px-2 py-1 rounded-pill">{{ $session->test->test_code }}</span>
                                            </td>
                                            <td class="px-3 text-center">
                                                @if ($session->score)
                                                    <span
                                                        class="badge bg-success px-2 py-1 rounded-pill">{{ number_format($session->score) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="px-3 text-center">
                                                @php
                                                    $statusColors = [
                                                        'scheduled' => 'secondary',
                                                        'in_progress' => 'warning',
                                                        'completed' => 'success',
                                                        'evaluated' => 'info',
                                                    ];
                                                    $color = $statusColors[$session->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }} px-2 py-1 rounded-pill">
                                                    {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted mb-0">No recent test sessions</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-user-plus me-2 text-primary"></i> Recent Applicants
                            </h6>
                            <a href="{{ route('tester.applicants') }}" class="btn btn-sm btn-link text-primary">View
                                All</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light small text-uppercase text-muted">
                                    <tr>
                                        <th class="py-3 px-3">Participant</th>
                                        <th class="py-3 px-3">Registered Date</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                        <th class="py-3 px-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentApplicants ?? [] as $applicant)
                                        <tr>
                                            <td class="px-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div
                                                        class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-user text-secondary"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $applicant->full_name }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ $applicant->participant_numb }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3">
                                                <small>{{ $applicant->registration_date->format('d/m/Y') }}</small>
                                            </td>
                                            <td class="px-3 text-center">
                                                @php
                                                    $statusColors = [
                                                        'registered' => 'secondary',
                                                        'test_taken' => 'info',
                                                        'processed' => 'warning',
                                                        'accepted' => 'success',
                                                        'rejected' => 'danger',
                                                    ];
                                                    $color = $statusColors[$applicant->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }} px-2 py-1 rounded-pill">
                                                    {{ ucfirst(str_replace('_', ' ', $applicant->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-3 text-center">
                                                <a href="{{ route('tester.applicants.show', $applicant) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                                                <p class="text-muted mb-0">No recent applicants</p>
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

        .avatar-sm {
            width: 35px;
            height: 35px;
            background: #f8f9fa;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .stat-card h2 {
                font-size: 1.5rem;
            }
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            <?php
            $statusLabels = json_encode(array_keys($testByStatus->toArray()));
            $statusData = json_encode(array_values($testByStatus->toArray()));
            $statusColors = ['#6c757d', '#f39c12', '#27ae60', '#3498db'];
            $chartColors = json_encode(array_slice($statusColors, 0, count($testByStatus)));
            ?>
            var statusLabels = <?php echo $statusLabels; ?>;
            var statusData = <?php echo $statusData; ?>;
            if (statusLabels.length > 0) {
                new Chart(document.getElementById('statusChart'), {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels.map(l => l.replace('_', ' ')),
                        datasets: [{
                            data: statusData,
                            backgroundColor: <?php echo $chartColors; ?>,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
            <?php if (isset($performanceByDate) && $performanceByDate->count() > 0): ?>
            var dateLabels = <?php echo json_encode($performanceByDate->pluck('date')); ?>;
            var dateScores = <?php echo json_encode($performanceByDate->pluck('avg_score')); ?>;
            new Chart(document.getElementById('performanceTrendChart'), {
                type: 'line',
                data: {
                    labels: dateLabels,
                    datasets: [{
                        label: 'Average Score',
                        data: dateScores,
                        borderColor: '#27ae60',
                        backgroundColor: 'rgba(39,174,96,0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 300
                        }
                    }
                }
            });
            <?php endif; ?>
        </script>
    @endpush
@endsection
