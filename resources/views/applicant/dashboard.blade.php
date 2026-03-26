@extends('layouts.app')

@section('title', 'Applicant Dashboard')

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
                            <p class="mb-0 opacity-75">Welcome back, {{ $applicant->full_name }}!</p>
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
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Total Tests</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['total_tests']) }}</h2>
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
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['completed_tests']) }}</h2>
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
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['average_score'], 0) }}</h2>
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
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['best_score'], 0) }}</h2>
                            <small class="opacity-75">personal best</small>
                        </div>
                        <i class="fas fa-trophy fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Tests Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-play-circle me-2 text-primary"></i> Available Tests
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($availableTests as $test)
                    <div class="border-bottom mb-3 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $test->test_name }}</h6>
                                <p class="text-muted small mb-1">{{ $test->description ?? 'No description available' }}</p>
                                <div class="d-flex gap-3">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i> {{ $test->duration_minutes }} minutes
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-question-circle me-1"></i> {{ number_format($test->total_questions) }} questions
                                    </small>
                                </div>
                            </div>
                            <a href="{{ route('applicant.tests.start', $test) }}"
                                class="btn btn-primary btn-sm"
                                onclick="return confirm('Start test? Time will begin immediately.')">
                                <i class="fas fa-play me-1"></i> Start
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p class="text-muted mb-0">No available tests</p>
                        <small class="text-muted">You have completed all available tests</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-history me-2 text-primary"></i> Recent Activities
                    </h6>
                    <a href="{{ route('applicant.history') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr class="small text-uppercase text-muted">
                                    <th class="py-3 px-3">Test Name</th>
                                    <th class="py-3 px-3">Date</th>
                                    <th class="py-3 px-3 text-center">Score</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                    <th class="py-3 px-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $session)
                                <tr>
                                    <td class="px-3">
                                        <strong>{{ $session->test->test_name }}</strong><br>
                                        <small class="text-muted">{{ $session->test->test_code }}</small>
                                    </td>
                                    <td class="px-3">
                                        <i class="far fa-calendar-alt text-muted me-1"></i>
                                        {{ $session->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-3 text-center">
                                        @if($session->score)
                                        <span class="badge bg-success">{{ number_format($session->score) }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-{{ $session->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        @if($session->status == 'completed')
                                        <a href="{{ route('applicant.history.show', $session) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-chart-line"></i> View
                                        </a>
                                        @elseif($session->status == 'in_progress')
                                        <a href="{{ route('pauli-test.start', [$applicant, $session->test]) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-play"></i> Continue
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No recent activities</p>
                                        <small class="text-muted">Start your first test to see activities</small>
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
</style>
@endsection