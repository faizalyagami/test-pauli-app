{{-- resources/views/tester/sessions/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Test Sessions')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total Sessions</h5>
                <h2>{{ number_format($stats['total']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>In Progress</h5>
                <h2>{{ number_format($stats['in_progress']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Completed</h5>
                <h2>{{ number_format($stats['completed']) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Evaluated</h5>
                <h2>{{ number_format($stats['evaluated']) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> All Test Sessions</h5>
                <div class="card-tools">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>

            <div class="collapse" id="filterCollapse">
                <div class="card-body bg-light">
                    <form method="GET" class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="all">All</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Test</label>
                            <select name="test_id" class="form-select">
                                <option value="">All Tests</option>
                                @foreach($tests as $test)
                                <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                    {{ $test->test_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filter
                            </button>
                            <a href="{{ route('tester.sessions') }}" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Participant</th>
                                <th>Test</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                            <tr>
                                <td>#{{ $session->id }}</td>
                                <td>
                                    <strong>{{ $session->applicant->full_name }}</strong><br>
                                    <small class="text-muted">{{ $session->applicant->participant_numb }}</small>
                                </td>
                                <td>{{ $session->test->test_name }}</td>
                                <td>{{ $session->start_time ? $session->start_time->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>{{ $session->end_time ? $session->end_time->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>
                                    @if($session->start_time && $session->end_time)
                                    {{ $session->start_time->diffInMinutes($session->end_time) }} minutes
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
                                <td>
                                    @if($session->score)
                                    <strong class="text-success">{{ number_format($session->score) }}</strong>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tester.sessions.show', $session) }}" class="btn btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($session->status === 'in_progress')
                                        <a href="{{ route('tester.sessions.monitor', $session) }}" class="btn btn-warning" title="Monitor">
                                            <i class="fas fa-tv"></i>
                                        </a>
                                        <button onclick="cancelSession({{ $session->id }})" class="btn btn-danger" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                        @if($session->status === 'completed')
                                        <a href="{{ route('pauli-test.result', $session->id) }}" class="btn btn-success" title="View Result">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <button onclick="printResult({{ $session->id }})" class="btn btn-secondary" title="Print">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No test sessions found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function cancelSession(sessionId) {
        if (confirm('Are you sure you want to cancel this test session?')) {
            fetch(`/tester/sessions/${sessionId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to cancel session');
                    }
                });
        }
    }

    function printResult(sessionId) {
        window.open(`/pauli-test/result/${sessionId}/print`, '_blank');
    }
</script>
@endsection