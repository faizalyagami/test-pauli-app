{{-- resources/views/applicant/result.blade.php --}}
@extends('layouts.app')

@section('title', 'Test Result - ' . $session->test->test_name)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card bg-success text-white mb-4">
            <div class="card-body text-center">
                <i class="fas fa-trophy fa-4x mb-3"></i>
                <h2>Test Completed!</h2>
                <p class="mb-0">Congratulations, {{ $session->applicant->full_name }}!</p>
                <p>You have successfully completed the {{ $session->test->test_name }} test.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <h6>Your Score</h6>
                <div class="display-1 text-primary">{{ number_format($scoreData['correct']) }}</div>
                <p>out of {{ number_format($scoreData['total_attempted']) }} answered</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <h6>Accuracy</h6>
                <div class="display-1 text-success">{{ number_format($scoreData['accuracy'], 1) }}%</div>
                <p>{{ number_format($scoreData['correct']) }} correct answers</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <h6>Time Spent</h6>
                <div class="display-1 text-info">
                    @if($session->start_time && $session->end_time)
                    {{ $session->start_time->diffInMinutes($session->end_time) }}
                    @else
                    0
                    @endif
                </div>
                <p>minutes</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Performance by Time Interval</h5>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12 text-center">
        <a href="{{ route('applicant.certificate', $session) }}" class="btn btn-success btn-lg">
            <i class="fas fa-certificate"></i> Download Certificate
        </a>
        <a href="{{ route('applicant.history') }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-history"></i> View History
        </a>
        <a href="{{ route('applicant.tests') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-play"></i> Take Another Test
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const performanceData = @json($answersByLine);

    new Chart(document.getElementById('performanceChart'), {
        type: 'line',
        data: {
            labels: performanceData.map(item => `Garis ${item.line_marker}`),
            datasets: [{
                    label: 'Total Answers',
                    data: performanceData.map(item => item.total),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Correct Answers',
                    data: performanceData.map(item => item.correct),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Answers'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time Intervals (3 minutes each)'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection