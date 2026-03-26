{{-- resources/views/applicant/partials/recent-activities.blade.php --}}
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-history me-2"></i> Recent Activities</h5>
    </div>
    <div class="card-body">
        @forelse($recentActivities as $session)
        <div class="border-bottom mb-3 pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">{{ $session->test->test_name }}</h6>
                    <small class="text-muted">
                        <i class="far fa-calendar-alt me-1"></i> {{ $session->created_at->format('d M Y H:i') }}
                    </small>
                </div>
                <div>
                    @if($session->status === 'completed')
                    <span class="badge bg-success">Completed</span>
                    <a href="{{ route('applicant.history.show', $session) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-chart-line"></i>
                    </a>
                    @elseif($session->status === 'in_progress')
                    <span class="badge bg-warning">In Progress</span>
                    <a href="{{ route('pauli-test.start', [$applicant, $session->test]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-play"></i> Continue
                    </a>
                    @else
                    <span class="badge bg-secondary">{{ ucfirst($session->status) }}</span>
                    @endif
                </div>
            </div>
            @if($session->score)
            <div class="mt-2">
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-success" style="width: {{ ($session->score / $session->test->total_questions) * 100 }}%"></div>
                </div>
                <small class="text-muted">Score: {{ number_format($session->score) }} / {{ number_format($session->test->total_questions) }}</small>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">No recent activities. Start your first test!</p>
        </div>
        @endforelse
    </div>
</div>