{{-- resources/views/tester/tests/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Tests')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total Tests</h5>
                <h2>{{ number_format($tests->total()) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Active Tests</h5>
                <h2>{{ number_format($tests->where('is_active', true)->count()) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Inactive Tests</h5>
                <h2>{{ number_format($tests->where('is_active', false)->count()) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Total Sessions</h5>
                <h2>{{ number_format($tests->sum('test_sessions_count')) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list me-2"></i> Test List</h5>
                <div class="card-tools">
                    <a href="{{ route('tester.tests.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i> Create New Test
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Test Code</th>
                                <th>Test Name</th>
                                <th class="text-center">Duration</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Structure</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Sessions</th>
                                <th class="text-center">Actions</th>
                             </thead>
                        <tbody>
                            @forelse($tests as $test)
                            <tr>
                                <td><code>{{ $test->test_code }}</code></td>
                                <td>
                                    <strong>{{ $test->test_name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($test->description ?? 'No description', 50) }}</small>
                                </td>
                                <td class="text-center"><span class="badge bg-info">{{ $test->duration_minutes }} min</span></td>
                                <td class="text-center"><span class="badge bg-secondary">{{ number_format($test->total_questions) }}</span></td>
                                <td class="text-center"><span class="badge bg-dark">{{ $test->total_columns }} × {{ $test->rows_per_column }}</span></td>
                                <td class="text-center">
                                    @if($test->is_active)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="badge bg-info">{{ $test->test_sessions_count ?? 0 }}</span></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tester.tests.edit', $test) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="generateQuestions({{ $test->id }})" class="btn btn-info" title="Generate Questions">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button onclick="toggleTestStatus({{ $test->id }}, {{ $test->is_active ? 'false' : 'true' }})"
                                            class="btn btn-{{ $test->is_active ? 'secondary' : 'success' }}"
                                            title="{{ $test->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $test->is_active ? 'ban' : 'check-circle' }}"></i>
                                        </button>
                                        <!-- FORM DELETE - PASTIKAN INI -->
                                        <form action="{{ route('tester.tests.destroy', $test) }}" method="POST" style="display: inline-block;" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus test {{ $test->test_name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No tests found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateQuestions(testId) {
    Swal.fire({
        title: 'Generate Questions?',
        html: 'This will generate random questions and overwrite existing ones.<br><strong>This action cannot be undone!</strong>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, generate!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generating...',
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
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Questions generated successfully!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to generate questions', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Network error. Please try again.', 'error');
            });
        }
    });
}

function toggleTestStatus(testId, activate) {
    const action = activate ? 'activate' : 'deactivate';
    Swal.fire({
        title: `${action === 'activate' ? 'Activate' : 'Deactivate'} Test?`,
        text: `Are you sure you want to ${action} this test?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: action === 'activate' ? '#28a745' : '#dc3545',
        confirmButtonText: `Yes, ${action}!`
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/tester/tests/${testId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_active: activate })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', `Test has been ${action}d.`, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', 'Failed to update status', 'error');
                }
            })
            .catch(() => Swal.fire('Error!', 'Error updating status', 'error'));
        }
    });
}
</script>
@endsection