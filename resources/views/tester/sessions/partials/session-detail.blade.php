{{-- resources/views/tester/sessions/partials/session-detail.blade.php --}}
<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i> Participant Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Full Name</th>
                        <td><strong>{{ $session->applicant->full_name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Participant Number</th>
                        <td><code class="bg-light px-2 py-1 rounded">{{ $session->applicant->participant_numb }}</code></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td>{{ $session->applicant->date_of_birth ? $session->applicant->date_of_birth->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td>
                            @if($session->applicant->gender == 'male')
                            <i class="fas fa-mars text-primary"></i> Male
                            @else
                            <i class="fas fa-venus text-danger"></i> Female
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $session->applicant->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $session->applicant->user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Education</th>
                        <td>{{ $session->applicant->education_background ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i> Test Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Test Name</th>
                        <td><strong>{{ $session->test->test_name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Test Code</th>
                        <td><code class="bg-light px-2 py-1 rounded">{{ $session->test->test_code }}</code></td>
                    </tr>
                    <tr>
                        <th>Duration</th>
                        <td><i class="fas fa-hourglass-half me-1"></i> {{ $session->test->duration_minutes }} minutes</td>
                    </tr>
                    <tr>
                        <th>Total Questions</th>
                        <td>{{ number_format($session->test->total_questions) }} questions</td>
                    </tr>
                    <tr>
                        <th>Started At</th>
                        <td>{{ $session->start_time ? $session->start_time->format('d F Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ended At</th>
                        <td>{{ $session->end_time ? $session->end_time->format('d F Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Skipped Columns</th>
                        <td><span class="badge bg-warning">{{ $session->skipped_columns }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Score Summary</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted mb-2">Total Answered</h6>
                            <h2 class="text-primary mb-0">{{ number_format($scoreData['total_attempted']) }}</h2>
                            <small>out of {{ number_format($session->test->total_questions) }} questions</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted mb-2">Correct Answers</h6>
                            <h2 class="text-success mb-0">{{ number_format($scoreData['correct']) }}</h2>
                            <small>{{ number_format(($scoreData['correct'] / max($scoreData['total_attempted'], 1)) * 100, 1) }}% accuracy</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted mb-2">Final Score</h6>
                            <h2 class="text-info mb-0">{{ number_format($scoreData['correct']) }}</h2>
                            <small>from {{ number_format($scoreData['total_attempted']) }} attempts</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>