{{-- resources/views/tester/sessions/completed.blade.php --}}
@extends('layouts.app')

@section('title', 'Completed Sessions')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-check-circle"></i> Completed Test Sessions</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Participant</th>
                        <th>Test</th>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Accuracy</th>
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
                        <td>{{ $session->end_time ? $session->end_time->format('d/m/Y H:i') : '-' }}</td>
                        <td><strong class="text-success">{{ number_format($session->score) }}</strong></td>
                        <td>
                            @php
                            $total = $session->answers()->count();
                            $correct = $session->answers()->where('is_correct', true)->count();
                            $accuracy = $total > 0 ? ($correct / $total) * 100 : 0;
                            @endphp
                            {{ number_format($accuracy, 1) }}%
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('tester.sessions.show', $session) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pauli-test.result', $session->id) }}" class="btn btn-success">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                                <button onclick="printResult({{ $session->id }})" class="btn btn-secondary">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No completed sessions found</p>
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

<script>
    function printResult(sessionId) {
        window.open(`/pauli-test/result/${sessionId}/print`, '_blank');
    }
</script>
@endsection