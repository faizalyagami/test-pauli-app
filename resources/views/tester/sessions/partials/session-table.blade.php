{{-- resources/views/tester/sessions/partials/session-table.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th width="5%">ID</th>
                <th width="20%">Participant</th>
                <th width="15%">Test</th>
                <th width="15%">Start Time</th>
                <th width="15%">End Time</th>
                <th width="8%">Duration</th>
                <th width="10%">Status</th>
                <th width="7%">Score</th>
                <th width="15%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
            <tr>
                <td class="text-center">#{{ $session->id }}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-2">
                            @if($session->applicant->user->avatar)
                            <img src="{{ Storage::url($session->applicant->user->avatar) }}"
                                class="rounded-circle" width="32" height="32">
                            @else
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px;">
                                <i class="fas fa-user fa-sm text-white"></i>
                            </div>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $session->applicant->full_name }}</strong><br>
                            <small class="text-muted">{{ $session->applicant->participant_numb }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-info">{{ $session->test->test_code }}</span><br>
                    <small>{{ Str::limit($session->test->test_name, 30) }}</small>
                </td>
                <td>
                    @if($session->start_time)
                    <i class="far fa-calendar-alt me-1"></i> {{ $session->start_time->format('d/m/Y') }}<br>
                    <i class="far fa-clock me-1"></i> {{ $session->start_time->format('H:i:s') }}
                    @else
                    -
                    @endif
                </td>
                <td>
                    @if($session->end_time)
                    <i class="far fa-calendar-alt me-1"></i> {{ $session->end_time->format('d/m/Y') }}<br>
                    <i class="far fa-clock me-1"></i> {{ $session->end_time->format('H:i:s') }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-center">
                    @if($session->start_time && $session->end_time)
                    {{ $session->start_time->diffInMinutes($session->end_time) }} min
                    @elseif($session->start_time)
                    {{ $session->start_time->diffInMinutes(now()) }} min
                    @else
                    -
                    @endif
                </td>
                <td class="text-center">
                    @php
                    $statusColors = [
                    'scheduled' => 'secondary',
                    'in_progress' => 'warning',
                    'completed' => 'success',
                    'evaluated' => 'info',
                    'cancelled' => 'danger'
                    ];
                    $statusIcons = [
                    'scheduled' => 'fa-calendar-alt',
                    'in_progress' => 'fa-spinner fa-spin',
                    'completed' => 'fa-check-circle',
                    'evaluated' => 'fa-clipboard-check',
                    'cancelled' => 'fa-times-circle'
                    ];
                    $color = $statusColors[$session->status] ?? 'secondary';
                    $icon = $statusIcons[$session->status] ?? 'fa-question';
                    @endphp
                    <span class="badge bg-{{ $color }}">
                        <i class="fas {{ $icon }} me-1"></i>
                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                    </span>
                </td>
                <td class="text-center">
                    @if($session->score)
                    <span class="badge bg-success fs-6">{{ number_format($session->score) }}</span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('tester.sessions.show', $session) }}"
                            class="btn btn-info" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($session->status === 'in_progress')
                        <a href="{{ route('tester.sessions.monitor', $session) }}"
                            class="btn btn-warning" title="Monitor">
                            <i class="fas fa-tv"></i>
                        </a>
                        <button onclick="cancelSession({{ $session->id }})"
                            class="btn btn-danger" title="Cancel">
                            <i class="fas fa-times"></i>
                        </button>
                        @endif

                        @if($session->status === 'completed')
                        <a href="{{ route('pauli-test.result', $session->id) }}"
                            class="btn btn-success" title="View Result">
                            <i class="fas fa-chart-line"></i>
                        </a>
                        <button onclick="printResult({{ $session->id }})"
                            class="btn btn-secondary" title="Print">
                            <i class="fas fa-print"></i>
                        </button>
                        @endif

                        @if($session->status === 'evaluated')
                        <a href="{{ route('pauli-test.result', $session->id) }}"
                            class="btn btn-primary" title="View Result">
                            <i class="fas fa-file-alt"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No test sessions found</p>
                    <small class="text-muted">Try adjusting your filters or create a new test session</small>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    function cancelSession(sessionId) {
        Swal.fire({
            title: 'Cancel Test Session?',
            text: 'This action cannot be undone. The participant will not be able to continue the test.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tester/sessions/${sessionId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Cancelled!', 'Test session has been cancelled.', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('Error!', 'Failed to cancel session.', 'error');
                        }
                    });
            }
        });
    }

    function printResult(sessionId) {
        window.open(`/pauli-test/result/${sessionId}/print`, '_blank');
    }
</script>
@endpush