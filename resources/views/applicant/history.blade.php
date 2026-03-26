{{-- resources/views/applicant/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Test History')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-history me-2"></i> Test History</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6>Total Tests</h6>
                            <h3>{{ number_format($summary['total']) }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6>Completed</h6>
                            <h3 class="text-success">{{ number_format($summary['completed']) }}</h3>
                        </div>
                        <div class="col-md-4">
                            <h6>Average Score</h6>
                            <h3 class="text-primary">{{ number_format($summary['average_score'], 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Test Name</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Score</th>
                        <th>Accuracy</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testSessions as $session)
                    <tr>
                        <td>
                            <strong>{{ $session->test->test_name }}</strong><br>
                            <small class="text-muted">{{ $session->test->test_code }}</small>
                        </td>
                        <td>
                            {{ $session->created_at->format('d F Y H:i') }}<br>
                            <small class="text-muted">{{ $session->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($session->start_time && $session->end_time)
                            {{ $session->start_time->diffInMinutes($session->end_time) }} minutes
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($session->score)
                            <span class="badge bg-success fs-6">{{ number_format($session->score) }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                            $total = $session->answers()->count();
                            $correct = $session->answers()->where('is_correct', true)->count();
                            $accuracy = $total > 0 ? ($correct / $total) * 100 : 0;
                            @endphp
                            @if($total > 0)
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $accuracy >= 80 ? 'success' : ($accuracy >= 60 ? 'warning' : 'danger') }}"
                                    style="width: {{ $accuracy }}%">
                                    {{ number_format($accuracy, 1) }}%
                                </div>
                            </div>
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
                            'evaluated' => 'info'
                            ];
                            $color = $statusColors[$session->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($session->status === 'completed')
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('applicant.history.show', $session) }}" class="btn btn-info">
                                    <i class="fas fa-chart-line"></i> Result
                                </a>
                                <a href="{{ route('applicant.certificate', $session) }}" class="btn btn-success">
                                    <i class="fas fa-certificate"></i> Certificate
                                </a>
                            </div>
                            @elseif($session->status === 'in_progress')
                            <a href="{{ route('pauli-test.start', [$applicant, $session->test]) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-play"></i> Continue
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No test history yet. Start your first test!</p>
                            <a href="{{ route('applicant.tests') }}" class="btn btn-primary">
                                <i class="fas fa-play"></i> Take a Test
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $testSessions->links() }}
        </div>
    </div>
</div>
@endsection