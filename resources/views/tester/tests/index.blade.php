{{-- resources/views/tester/tests/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Tests')

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
                                <i class="fas fa-file-alt me-2"></i> Manage Tests
                            </h4>
                            <p class="mb-0 opacity-75">Create, edit, and manage Pauli Test configurations</p>
                        </div>
                        <div>
                            <i class="fas fa-plus-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-uppercase small fw-semibold opacity-75">TOTAL TESTS</p>
                            <h2 class="mb-0 fw-bold display-4">{{ number_format($tests->total()) }}</h2>
                            <small class="opacity-75">available tests</small>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-uppercase small fw-semibold opacity-75">ACTIVE TESTS</p>
                            <h2 class="mb-0 fw-bold display-4">{{ number_format($tests->where('is_active', true)->count()) }}</h2>
                            <small class="opacity-75">currently active</small>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-uppercase small fw-semibold opacity-75">INACTIVE TESTS</p>
                            <h2 class="mb-0 fw-bold display-4">{{ number_format($tests->where('is_active', false)->count()) }}</h2>
                            <small class="opacity-75">currently inactive</small>
                        </div>
                        <i class="fas fa-ban fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 text-uppercase small fw-semibold opacity-75">TOTAL SESSIONS</p>
                            <h2 class="mb-0 fw-bold display-4">{{ number_format($tests->sum('test_sessions_count')) }}</h2>
                            <small class="opacity-75">test attempts</small>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Button -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <a href="{{ route('tester.tests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Create New Test
                </a>
            </div>
        </div>
    </div>

    <!-- Test List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-list me-2 text-primary"></i> Test List
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr class="small text-uppercase text-muted">
                                    <th class="py-3 px-3">Test Code</th>
                                    <th class="py-3 px-3">Test Name</th>
                                    <th class="py-3 px-3 text-center">Duration</th>
                                    <th class="py-3 px-3 text-center">Questions</th>
                                    <th class="py-3 px-3 text-center">Structure</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                    <th class="py-3 px-3 text-center">Sessions</th>
                                    <th class="py-3 px-3 text-center">Actions</th>
                                    \\
                            </thead>
                            <tbody>
                                @forelse($tests as $test)
                                <tr>
                                    <td class="px-3">
                                        <code class="bg-light px-2 py-1 rounded">{{ $test->test_code }}</code>
                                    </td>
                                    <td class="px-3">
                                        <div>
                                            <strong>{{ $test->test_name }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($test->description ?? 'No description', 50) }}</small>
                                        </div>
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="fas fa-clock me-1"></i> {{ $test->duration_minutes }} min
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="fas fa-question-circle me-1"></i> {{ number_format($test->total_questions) }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-dark px-3 py-2">
                                            <i class="fas fa-columns me-1"></i> {{ $test->total_columns }} × {{ $test->rows_per_column }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        @if($test->is_active)
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Active
                                        </span>
                                        @else
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i> Inactive
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="fas fa-users me-1"></i> {{ $test->test_sessions_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tester.tests.edit', $test) }}"
                                                class="btn btn-outline-warning"
                                                title="Edit Test">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="generateQuestions({{ $test->id }})"
                                                class="btn btn-outline-info"
                                                title="Generate Questions">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <button onclick="toggleTestStatus({{ $test->id }}, {{ $test->is_active ? 'false' : 'true' }})"
                                                class="btn btn-outline-{{ $test->is_active ? 'secondary' : 'success' }}"
                                                title="{{ $test->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $test->is_active ? 'ban' : 'check-circle' }}"></i>
                                            </button>
                                            <button onclick="deleteTest({{ $test->id }}, '{{ $test->test_name }}')"
                                                class="btn btn-outline-danger"
                                                title="Delete Test">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No tests found</p>
                                        <small class="text-muted">Click "Create New Test" to add your first test</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($tests->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-center">
                        {{ $tests->links() }}
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
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card .card-body {
        padding: 1.5rem;
    }

    .stat-card h2 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        transition: all 0.2s ease;
    }

    .btn-group-sm .btn:hover {
        transform: translateY(-1px);
    }

    .badge {
        font-weight: 500;
        font-size: 0.75rem;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
    }

    .table> :not(caption)>*>* {
        vertical-align: middle;
    }

    code {
        font-size: 0.85rem;
        font-weight: 600;
        background: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }

    @media (max-width: 768px) {
        .stat-card h2 {
            font-size: 1.75rem;
        }

        .stat-card .card-body {
            padding: 1rem;
        }

        .table-responsive {
            font-size: 0.75rem;
        }

        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
        }

        .badge {
            font-size: 0.7rem;
            padding: 0.35rem 0.5rem;
        }
    }
</style>

@push('scripts')
<script>
    function generateQuestions(testId) {
        Swal.fire({
            title: 'Generate Questions?',
            html: 'This will generate random questions and overwrite existing ones.<br><strong>This action cannot be undone!</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, generate!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generating...',
                    text: 'Please wait while questions are being generated.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/tester/tests/${testId}/generate-questions`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Questions generated successfully!',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error!', 'Failed to generate questions', 'error');
                        }
                    }).catch(() => {
                        Swal.fire('Error!', 'Error generating questions', 'error');
                    });
            }
        });
    }

    function toggleTestStatus(testId, activate) {
        const action = activate ? 'activate' : 'deactivate';
        const actionText = action === 'activate' ? 'Activate' : 'Deactivate';
        const confirmColor = action === 'activate' ? '#28a745' : '#dc3545';

        Swal.fire({
            title: `${actionText} Test?`,
            text: `Are you sure you want to ${action} this test?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${action}!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tester/tests/${testId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            is_active: activate
                        })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: `Test has been ${action}d.`,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error!', 'Failed to update test status', 'error');
                        }
                    }).catch(() => {
                        Swal.fire('Error!', 'Error updating test status', 'error');
                    });
            }
        });
    }

    function deleteTest(testId, testName) {
        Swal.fire({
            title: 'Delete Test?',
            html: `Are you sure you want to delete <strong>${testName}</strong>?<br><span class="text-danger">This action cannot be undone and will delete all associated data!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while test is being deleted.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/tester/tests/${testId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Test has been deleted.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error!', 'Failed to delete test', 'error');
                        }
                    }).catch(() => {
                        Swal.fire('Error!', 'Error deleting test', 'error');
                    });
            }
        });
    }
</script>
@endpush