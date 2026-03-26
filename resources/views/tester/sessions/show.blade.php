{{-- resources/views/tester/sessions/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Session Detail - ' . $session->applicant->full_name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user"></i> Participant Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Full Name</th>
                        <td>{{ $session->applicant->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Participant Number</th>
                        <td><code>{{ $session->applicant->participant_numb }}</code></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td>{{ $session->applicant->date_of_birth ? $session->applicant->date_of_birth->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $session->applicant->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Education</th>
                        <td>{{ $session->applicant->education_background ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Test Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Test Name</th>
                        <td>{{ $session->test->test_name }}</td>
                    </tr>
                    <tr>
                        <th>Test Code</th>
                        <td><code>{{ $session->test->test_code }}</code></td>
                    </tr>
                    <tr>
                        <th>Duration</th>
                        <td>{{ $session->test->duration_minutes }} minutes</td>
                    </tr>
                    <tr>
                        <th>Total Questions</th>
                        <td>{{ number_format($session->test->total_questions) }}</td>
                    </tr>
                    <tr>
                        <th>Started At</th>
                        <td>{{ $session->start_time ? $session->start_time->format('d/m/Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ended At</th>
                        <td>{{ $session->end_time ? $session->end_time->format('d/m/Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Skipped Columns</th>
                        <td><span class="badge bg-warning">{{ $session->skipped_columns }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Score Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h6>Total Answered</h6>
                            <h2 class="text-primary">{{ number_format($scoreData['total_attempted']) }}</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h6>Correct Answers</h6>
                            <h2 class="text-success">{{ number_format($scoreData['correct']) }}</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h6>Accuracy</h6>
                            <h2 class="text-info">{{ number_format($scoreData['accuracy'], 1) }}%</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Performance by Line (Every 3 Minutes)</h5>
            </div>
            <div class="card-body">
                <canvas id="lineChart" height="250"></canvas>
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Line</th>
                                <th>Total Answers</th>
                                <th>Correct</th>
                                <th>Accuracy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($answersByLine as $line)
                            <tr>
                                <td>Garis {{ $line->line_marker }}</td>
                                <td>{{ $line->total }}</td>
                                <td>{{ $line->correct }}</td>
                                <td>
                                    @if($line->total > 0)
                                    {{ number_format(($line->correct / $line->total) * 100, 1) }}%
                                    @else
                                    0%
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Performance by Column</h5>
            </div>
            <div class="card-body">
                <canvas id="columnChart" height="250"></canvas>
            </div>
        </div>

        <div class="mt-3 text-center">
            <a href="{{ route('pauli-test.result', $session->id) }}" class="btn btn-primary">
                <i class="fas fa-chart-line"></i> View Full Result
            </a>
            <a href="{{ route('tester.sessions') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: @json($answersByLine - > pluck('line_marker') - > map(function($item) {
                return "Garis $item";
            })),
            datasets: [{
                label: 'Total Answers',
                data: @json($answersByLine - > pluck('total')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Correct Answers',
                data: @json($answersByLine - > pluck('correct')),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Column Chart
    const columnCtx = document.getElementById('columnChart').getContext('2d');
    new Chart(columnCtx, {
        type: 'bar',
        data: {
            labels: @json($answersByColumn - > pluck('column_number')),
            datasets: [{
                label: 'Total Answers',
                data: @json($answersByColumn - > pluck('total')),
                backgroundColor: 'rgba(153, 102, 255, 0.5)',
                borderColor: 'rgb(153, 102, 255)',
                borderWidth: 1
            }, {
                label: 'Correct Answers',
                data: @json($answersByColumn - > pluck('correct')),
                backgroundColor: 'rgba(255, 159, 64, 0.5)',
                borderColor: 'rgb(255, 159, 64)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush