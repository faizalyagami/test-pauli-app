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

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-list"></i> All Test Sessions</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>

                {{-- FILTER --}}
                <div class="collapse" id="filterCollapse">
                    <div class="card-body bg-light">
                        <form method="GET" class="row" id="filterForm">
                            <div class="col-md-3 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-select">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>
                                        Scheduled</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                                        In Progress</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                    <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>
                                        Evaluated</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Test</label>
                                <select name="test_id" class="form-select">
                                    <option value="">All Tests</option>
                                    @foreach ($tests as $test)
                                        <option value="{{ $test->id }}"
                                            {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                            {{ $test->test_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>From</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply
                                </button>
                                <a href="{{ route('tester.sessions') }}" class="btn btn-secondary">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="card-body">
                    {{-- Per Page Dropdown --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="perPage" class="text-muted mb-0">Show:</label>
                            <select id="perPage" class="form-select form-select-sm" style="width: auto;">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-muted">entries</span>
                        </div>

                        @if ($sessions->total() > 0)
                            <div class="text-muted small">
                                Showing {{ $sessions->firstItem() }} to {{ $sessions->lastItem() }}
                                of {{ $sessions->total() }} results
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Participant</th>
                                    <th>Test</th>
                                    <th>Start</th>
                                    <th>End</th>
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
                                            <small class="text-muted">
                                                {{ $session->applicant->participant_numb }}
                                            </small>
                                        </td>

                                        <td>{{ $session->test->test_name }}</td>

                                        <td>
                                            {{ $session->start_time ? $session->start_time->format('d/m/Y H:i:s') : '-' }}
                                        </td>

                                        <td>
                                            {{ $session->end_time ? $session->end_time->format('d/m/Y H:i:s') : '-' }}
                                        </td>

                                        <td>
                                            @if ($session->start_time && $session->end_time)
                                                {{ $session->start_time->diffInMinutes($session->end_time) }} min
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $colors = [
                                                    'scheduled' => 'secondary',
                                                    'in_progress' => 'warning',
                                                    'completed' => 'success',
                                                    'evaluated' => 'info',
                                                ];
                                            @endphp

                                            <span class="badge bg-{{ $colors[$session->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($session->score)
                                                <strong class="text-success">
                                                    {{ number_format($session->score) }}
                                                </strong>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('tester.sessions.show', $session) }}"
                                                    class="btn btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if ($session->status === 'in_progress')
                                                    <a href="{{ route('tester.sessions.monitor', $session) }}"
                                                        class="btn btn-warning">
                                                        <i class="fas fa-tv"></i>
                                                    </a>

                                                    <button class="btn btn-danger"
                                                        onclick="cancelSession({{ $session->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif

                                                @if ($session->status === 'completed')
                                                    <a href="{{ route('pauli-test.result', $session->id) }}"
                                                        class="btn btn-success">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>

                                                    <button class="btn btn-secondary"
                                                        onclick="printResult({{ $session->id }})">
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
                                            <div class="text-muted">No test sessions found</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-4">
                        @if ($sessions->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $sessions->links('pagination::bootstrap-5') }}
                            </div>
                            <div class="text-center text-muted mt-2 small">
                                Page {{ $sessions->currentPage() }} of {{ $sessions->lastPage() }}
                                ({{ $sessions->total() }} total records)
                            </div>
                        @elseif($sessions->total() > 0)
                            <div class="text-center text-muted">
                                <small>Page 1 of 1 ({{ $sessions->total() }} total records)</small>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function cancelSession(id) {
            if (!confirm('Cancel this session?')) return;

            fetch(`/tester/sessions/${id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) location.reload();
                    else alert('Failed');
                });
        }

        function printResult(id) {
            window.open(`/pauli-test/result/${id}/print`, '_blank');
        }

        // Handle per page change
        document.getElementById('perPage')?.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.set('page', '1'); // Reset to first page
            window.location.href = url.toString();
        });

        // Preserve filter values when form submits
        document.getElementById('filterForm')?.addEventListener('submit', function(e) {
            const perPage = document.getElementById('perPage')?.value;
            if (perPage && perPage !== '20') {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'per_page';
                input.value = perPage;
                this.appendChild(input);
            }
        });
    </script>

    {{-- Debug info (remove in production) --}}
    {{-- @if (config('app.debug'))
        <div class="row mt-3">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted">
                            Debug: Total {{ $sessions->total() }} |
                            Per Page {{ $sessions->perPage() }} |
                            Current Page {{ $sessions->currentPage() }} |
                            Last Page {{ $sessions->lastPage() }} |
                            Has Pages: {{ $sessions->hasPages() ? 'Yes' : 'No' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}
@endsection
