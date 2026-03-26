{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

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
                                <i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard
                            </h4>
                            <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}! Manage your system here.</p>
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
                            <h6 class="mb-1 text-uppercase small fw-semibold opacity-75">Total Users</h6>
                            <h2 class="mb-0 fw-bold display-6">{{ number_format($stats['total_users'] ?? 0) }}</h2>
                            <small class="opacity-75">registered users</small>
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

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-users me-2 text-primary"></i> User Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="text-danger">{{ number_format($stats['total_admins'] ?? 0) }}</h3>
                            <small class="text-muted">Admins</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-warning">{{ number_format($stats['total_testers'] ?? 0) }}</h3>
                            <small class="text-muted">Testers</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-success">{{ number_format($stats['total_applicants'] ?? 0) }}</h3>
                            <small class="text-muted">Applicants</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line me-2 text-primary"></i> Recent Activity
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-user-plus me-2 text-primary"></i> Recent Users
                    </h6>
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-3">Name</th>
                                    <th class="py-3 px-3">Email</th>
                                    <th class="py-3 px-3">Role</th>
                                    <th class="py-3 px-3">Joined</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                <tr>
                                    <td class="px-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                @if($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}" width="32" height="32" class="rounded-circle">
                                                @else
                                                <i class="fas fa-user text-secondary"></i>
                                                @endif
                                            </div>
                                            <strong>{{ $user->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="px-3">{{ $user->email }}</td>
                                    <td class="px-3">
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'tester' ? 'warning' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-3">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">No users found</td>
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
        transition: transform 0.2s ease;
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
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('activityChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Test Sessions',
                data: [65, 59, 80, 81, 56, 55],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true
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
</script>
@endpush
@endsection