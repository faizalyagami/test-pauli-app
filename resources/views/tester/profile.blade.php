{{-- resources/views/tester/profile.blade.php --}}
@extends('layouts.app')

@section('title', 'My Profile')

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
                                <i class="fas fa-user-circle me-2"></i> My Profile
                            </h4>
                            <p class="mb-0 opacity-75">Manage your account information and preferences</p>
                        </div>
                        <div>
                            <i class="fas fa-user-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}"
                            class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                        @else
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 120px; height: 120px;">
                            <i class="fas fa-user-tie fa-4x text-secondary"></i>
                        </div>
                        @endif
                    </div>
                    <h4>{{ Auth::user()->name }}</h4>
                    <p class="text-muted">
                        <i class="fas fa-envelope me-1"></i> {{ Auth::user()->email }}
                    </p>
                    <p class="text-muted">
                        <span class="badge bg-info">{{ ucfirst(Auth::user()->role) }}</span>
                    </p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#avatarModal">
                        <i class="fas fa-camera me-1"></i> Change Avatar
                    </button>
                </div>
            </div>

            <!-- Account Stats Card -->
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line me-2 text-primary"></i> Account Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-primary">{{ number_format($stats['total_tests'] ?? 0) }}</h5>
                            <small class="text-muted">Tests Managed</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success">{{ number_format($stats['completed_tests'] ?? 0) }}</h5>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-12 mt-3">
                            <h5 class="text-warning">{{ number_format($stats['average_score'] ?? 0, 0) }}</h5>
                            <small class="text-muted">Average Score</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Profile Information -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Profile Information
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-muted">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-muted">Role</label>
                                <input type="text" class="form-control" value="{{ ucfirst(Auth::user()->role) }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-muted">Member Since</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->created_at->format('d F Y') }}" disabled>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-key me-2 text-primary"></i> Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-bolt me-2 text-primary"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <a href="{{ route('tester.tests.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus me-1"></i> Create Test
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('tester.applicants.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-plus me-1"></i> Add Participant
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('tester.reports') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-chart-bar me-1"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="file" name="avatar" class="form-control" accept="image/*" required>
                    <small class="text-muted">Max size: 2MB. Format: JPG, PNG</small>
                </div>
                <div class="modal-footer">
                    @if(Auth::user()->avatar)
                    <a href="{{ route('profile.remove-avatar') }}" class="btn btn-danger" onclick="return confirm('Remove avatar?')">
                        <i class="fas fa-trash me-1"></i> Remove
                    </a>
                    @endif
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endsection