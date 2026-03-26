{{-- resources/views/applicant/partials/available-tests.blade.php --}}
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-play-circle me-2"></i> Available Tests</h5>
    </div>
    <div class="card-body">
        @forelse($availableTests->take(3) as $test)
        <div class="border-bottom mb-3 pb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $test->test_name }}</h6>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i> {{ $test->duration_minutes }} min |
                        <i class="fas fa-question-circle me-1"></i> {{ number_format($test->total_questions) }} questions
                    </small>
                </div>
                <a href="{{ route('applicant.tests.start', $test) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-play"></i> Start
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-4">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <p class="text-muted">No available tests. All tests completed!</p>
        </div>
        @endforelse

        @if($availableTests->count() > 3)
        <div class="text-center mt-3">
            <a href="{{ route('applicant.tests') }}" class="btn btn-link">View All ({{ $availableTests->count() }} tests)</a>
        </div>
        @endif
    </div>
</div>