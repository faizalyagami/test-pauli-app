{{-- resources/views/tester/sessions/monitor.blade.php --}}
@extends('layouts.app')

@section('title', 'Monitor Test - ' . $session->applicant->full_name)

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-tv"></i> Live Monitoring - {{ $session->applicant->full_name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6>Test Information</h6>
                            <hr>
                            <p><strong>Test:</strong> {{ $session->test->test_name }}</p>
                            <p><strong>Started:</strong> {{ $session->start_time ? $session->start_time->format('d/m/Y H:i:s') : '-' }}</p>
                            <p><strong>Duration:</strong> {{ $session->test->duration_minutes }} minutes</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <h6>Live Status</h6>
                            <hr>
                            <div class="text-center">
                                <div class="display-1" id="liveTimer">00:00</div>
                                <p class="text-muted">Time Elapsed</p>
                            </div>
                            <div class="progress mb-3">
                                <div id="progressBar" class="progress-bar bg-success" style="width: 0%"></div>
                            </div>
                            <p><strong>Status:</strong> <span class="badge bg-warning">In Progress</span></p>
                            <p><strong>Skipped Columns:</strong> <span id="skippedCount">{{ $session->skipped_columns }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button onclick="refreshData()" class="btn btn-primary">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <button onclick="cancelSession()" class="btn btn-danger">
                        <i class="fas fa-stop"></i> Cancel Test
                    </button>
                    <a href="{{ route('tester.sessions') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Real-time Progress</h5>
            </div>
            <div class="card-body">
                <canvas id="progressChart" height="300"></canvas>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Recent Answers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Column</th>
                                <th>Row</th>
                                <th>Answer</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentAnswers">
                            <tr>
                                <td colspan="5" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let sessionId = {
        {
            $session - > id
        }
    };
    let updateInterval;

    // Start auto refresh every 10 seconds
    updateInterval = setInterval(refreshData, 10000);

    function refreshData() {
        fetch(`/api/sessions/${sessionId}/progress`)
            .then(response => response.json())
            .then(data => {
                updateTimer(data.elapsed_time);
                updateProgress(data.progress_percentage);
                updateRecentAnswers(data.recent_answers);
                updateChart(data.answers_by_minute);
                $('#skippedCount').text(data.skipped_columns);
            })
            .catch(error => console.error('Error:', error));
    }

    function updateTimer(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        $('#liveTimer').text(`${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`);
    }

    function updateProgress(percentage) {
        $('#progressBar').css('width', percentage + '%');
        if (percentage >= 100) {
            $('#progressBar').removeClass('bg-success').addClass('bg-danger');
        }
    }

    function updateRecentAnswers(answers) {
        if (answers && answers.length > 0) {
            let html = '';
            answers.slice(0, 10).forEach(answer => {
                const statusClass = answer.is_correct ? 'text-success' : 'text-danger';
                const statusIcon = answer.is_correct ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';
                html += `
                    <tr>
                        <td>${answer.time}</td>
                        <td>${answer.column}</td>
                        <td>${answer.row}</td>
                        <td><strong>${answer.answer}</strong></td>
                        <td class="${statusClass}">${statusIcon}</td>
                    </tr>
                `;
            });
            $('#recentAnswers').html(html);
        }
    }

    let chart;

    function updateChart(data) {
        const ctx = document.getElementById('progressChart').getContext('2d');

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Answers per Minute',
                    data: data.values || [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
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
                            text: 'Minute'
                        }
                    }
                }
            }
        });
    }

    function cancelSession() {
        if (confirm('Are you sure you want to cancel this test session?')) {
            fetch(`/tester/sessions/${sessionId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearInterval(updateInterval);
                        window.location.href = '{{ route('
                        tester.sessions ') }}';
                    } else {
                        alert('Failed to cancel session');
                    }
                });
        }
    }

    // Initial load
    refreshData();

    // Cleanup on page unload
    window.onbeforeunload = function() {
        clearInterval(updateInterval);
    };
</script>
@endsection