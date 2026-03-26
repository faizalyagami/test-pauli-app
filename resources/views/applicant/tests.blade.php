{{-- resources/views/applicant/tests.blade.php --}}
@extends('layouts.app')

@section('title', 'Available Tests')

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
                                <i class="fas fa-file-alt me-2"></i> Available Tests
                            </h4>
                            <p class="mb-0 opacity-75">Select a test to begin your assessment</p>
                        </div>
                        <div>
                            <i class="fas fa-play-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse($tests as $test)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <div class="test-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-chart-line fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">{{ $test->test_name }}</h5>
                                    <p class="text-muted mb-0 small">{{ $test->test_code }}</p>
                                </div>
                            </div>

                            <p class="text-muted mb-3">{{ $test->description ?? 'No description available. This test measures your concentration, accuracy, and work endurance.' }}</p>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Duration</small>
                                            <strong>{{ $test->duration_minutes }} minutes</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-question-circle text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Questions</small>
                                            <strong>{{ number_format($test->total_questions) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-columns text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Columns</small>
                                            <strong>{{ $test->total_columns }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-arrow-down text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Rows per Column</small>
                                            <strong>{{ $test->rows_per_column }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('applicant.tests.start', $test) }}"
                                class="btn btn-primary btn-lg px-4"
                                onclick="return confirmTestStart('{{ $test->test_name }}', {{ $test->duration_minutes }})">
                                <i class="fas fa-play me-2"></i> Start Test
                            </a>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i> Once started, timer cannot be paused
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                    <h4 class="mb-2">No Available Tests</h4>
                    <p class="text-muted mb-0">You have completed all available tests.</p>
                    <p class="text-muted">Check back later for new assessments.</p>
                    <a href="{{ route('applicant.dashboard') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-home me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Test Instructions Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Test Instructions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-2"></i> How to Take the Test</h6>
                            <ul class="text-muted">
                                <li>Click "Start Test" to begin immediately</li>
                                <li>Timer will start counting down from {{ $tests->first()->duration_minutes ?? 30 }} minutes</li>
                                <li>Add numbers from top to bottom (number + number below)</li>
                                <li>Write the result (only the last digit if result is tens)</li>
                                <li>Draw a line under your work every "LINE" command (every 3 minutes)</li>
                                <li>Skip columns if needed by clicking "Skip Column" button</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-lightbulb text-warning me-2"></i> Tips for Best Results</h6>
                            <ul class="text-muted">
                                <li>Work as quickly and accurately as possible</li>
                                <li>Don't spend too much time on difficult questions</li>
                                <li>If you make a mistake, overwrite it (don't erase)</li>
                                <li>Keep your focus throughout the test</li>
                                <li>Ensure you have a stable internet connection</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .test-icon {
        transition: transform 0.3s ease;
    }

    .card:hover .test-icon {
        transform: scale(1.1);
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>

@push('scripts')
<script>
    function confirmTestStart(testName, duration) {
        return confirm(`Are you ready to start "${testName}"?\n\nDuration: ${duration} minutes\n\nOnce started, the timer cannot be paused. Make sure you have enough time to complete the test.\n\nClick OK to begin.`);
    }
</script>
@endpush
@endsection