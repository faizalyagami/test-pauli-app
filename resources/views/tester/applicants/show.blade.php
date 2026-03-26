@extends('layouts.app')

@section('title', 'Applicant Details - ' . $applicant->full_name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i> Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-4x text-white"></i>
                    </div>
                    <h4 class="mt-2">{{ $applicant->full_name }}</h4>
                    <p class="text-muted">
                        <code>{{ $applicant->participant_numb }}</code>
                    </p>
                </div>

                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Date of Birth</th>
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
                        <th>Phone</th>
                        <td>{{ $applicant->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $applicant->user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>{{ $applicant->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Education</th>
                        <td>{{ $applicant->education_background ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Institution</th>
                        <td>{{ $applicant->institution ?? '-' }}</td>
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
                    <tr>
                        <th>Registered Date</th>
                        <td>{{ $applicant->registration_date ? $applicant->registration_date->format('d F Y') : '-' }}</td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('tester.applicants.edit', $applicant) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('tester.applicants.destroy', $applicant) }}" method="POST" class="d-inline" id="delete-form-{{ $applicant->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteApplicant({{ $applicant->id }}, '{{ $applicant->full_name }}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    <a href="{{ route('tester.applicants') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i> Test Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h6>Total Tests</h6>
                            <h3 class="text-primary">{{ $stats['total_tests'] }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h6>Completed</h6>
                            <h3 class="text-success">{{ $stats['completed_tests'] }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h6>Average Score</h6>
                            <h3 class="text-info">{{ number_format($stats['average_score'], 0) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-2">
                            <h6>Best Score</h6>
                            <h3 class="text-warning">{{ number_format($stats['best_score'], 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-history me-2"></i> Test History</h5>
            </div>
            <div class="card-body">
                @if($testSessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Test Name</th>
                                <th>Date</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($testSessions as $session)
                            <tr>
                                <td>
                                    <strong>{{ $session->test->test_name }}</strong><br>
                                    <small class="text-muted">{{ $session->test->test_code }}</small>
                                </td>
                                <td>
                                    {{ $session->created_at->format('d/m/Y H:i') }}<br>
                                    <small>{{ $session->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($session->start_time && $session->end_time)
                                    {{ $session->start_time->diffInMinutes($session->end_time) }} min
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @php
                                    $statusColors = [
                                    'scheduled' => 'secondary',
                                    'in_progress' => 'warning',
                                    'completed' => 'success',
                                    'evaluated' => 'info'
                                    ];
                                    $color = $statusColors[$session->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($session->score)
                                    <strong class="text-success">{{ number_format($session->score) }}</strong>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tester.sessions.show', $session) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($session->status === 'completed')
                                        <a href="{{ route('pauli-test.result', $session->id) }}" class="btn btn-success">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No test history found for this applicant.</p>
                    <a href="{{ route('tester.tests') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-play"></i> Assign Test
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function deleteApplicant(id, name) {
        Swal.fire({
            title: 'Delete Applicant?',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endsection