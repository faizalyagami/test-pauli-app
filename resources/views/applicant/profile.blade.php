{{-- resources/views/applicant/profile.blade.php --}}
@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
                    @else
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                        <i class="fas fa-user fa-5x text-white"></i>
                    </div>
                    @endif
                </div>
                <h4>{{ $applicant->full_name }}</h4>
                <p class="text-muted">
                    <i class="fas fa-id-card me-1"></i> {{ $applicant->participant_numb }}
                </p>
                <p class="text-muted">
                    <i class="fas fa-envelope me-1"></i> {{ $user->email }}
                </p>
                <a href="{{ route('applicant.profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Profile
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Member Since</th>
                        <td>{{ $applicant->created_at->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Last Login</th>
                        <td>{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d F Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @php
                            $statusColors = [
                            'registered' => 'secondary',
                            'test_taken' => 'info',
                            'processed' => 'warning',
                            'accepted' => 'success',
                            'rejected' => 'danger'
                            ];
                            $color = $statusColors[$applicant->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $applicant->status)) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th width="30%">Full Name</th>
                        <td><strong>{{ $applicant->full_name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td>{{ $applicant->date_of_birth ? $applicant->date_of_birth->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td>
                            @if($applicant->gender == 'male')
                            <i class="fas fa-mars text-primary"></i> Male
                            @elseif($applicant->gender == 'female')
                            <i class="fas fa-venus text-danger"></i> Female
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Phone Number</th>
                        <td>{{ $applicant->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>{{ $applicant->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Education Background</th>
                        <td>{{ $applicant->education_background ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Institution</th>
                        <td>{{ $applicant->institution ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-1"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection