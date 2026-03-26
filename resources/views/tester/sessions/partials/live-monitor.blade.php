{{-- resources/views/tester/sessions/partials/live-monitor.blade.php --}}
<div class="card">
    <div class="card-header bg-warning text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-tv me-2"></i> Live Monitoring
            </h5>
            <span class="badge bg-danger" id="liveStatusBadge">
                <i class="fas fa-circle fa-fw"></i> LIVE
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="border rounded p-3 mb-3 bg-light">
                    <h6 class="text-muted mb-2">Test Progress</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <small>Elapsed Time</small>
                        <strong id="elapsedTime">00:00</strong>
                    </div>
                    <div class="progress mb-2" style="height: 25px;">
                        <div id="timeProgress" class="progress-bar bg-primary" style="width: 0%">
                            <span id="timePercentage">0%</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <small class="text-muted">Answered</small>
                            <h4 id="answeredCount" class="mb-0">0</h4>
                        </div>
                        <div>
                            <small class="text-muted">Correct</small>
                            <h4 id="correctCount" class="mb-0 text-success">0</h4>
                        </div>
                        <div>
                            <small class="text-muted">Accuracy</small>
                            <h4 id="accuracyValue" class="mb-0 text-info">0%</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border rounded p-3 mb-3 bg-light">
                    <h6 class="text-muted mb-2">Session Information</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Participant</th>
                            <td><strong>{{ $session->applicant->full_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Test</th>
                            <td>{{ $session->test->test_name }}</td>
                        </tr>
                        <tr>
                            <th>Started At</th>
                            <td id="startTime">{{ $session->start_time ? $session->start_time->format('H:i:s') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Skipped Columns</th>
                            <td><span id="skippedColumns" class="badge bg-warning">{{ $session->skipped_columns }}</span></td>
                        </tr>
                        <tr>
                            <th>Current Line</th>
                            <td><span id="currentLine" class="badge bg-info">0</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <canvas id="liveProgressChart" height="250"></canvas>
        </div>

        <div class="mt-3 text-center">
            <button onclick="refreshMonitor()" class="btn btn-primary">
                <i class="fas fa-sync"></i> Refresh
            </button>
            <button onclick="toggleAutoRefresh()" class="btn btn-info" id="autoRefreshBtn">
                <i class="fas fa-play"></i> Auto Refresh (10s)
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

@push('scripts')
<script>
    let sessionId = {
        {
            $session - > id
        }
    };
    let autoRefresh = true;
    let refreshInterval;
    let chart;

    // Start auto refresh
    startAutoRefresh();

    function startAutoRefresh() {
        refreshInterval = setInterval(refreshMonitor, 10000);
    }

    function toggleAutoRefresh() {
        autoRefresh = !autoRefresh;
        const btn = document.getElementById('autoRefreshBtn');

        if (autoRefresh) {
            startAutoRefresh();
            btn.innerHTML = '<i class="fas fa-play"></i> Auto Refresh (10s)';
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-info');
        } else {
            clearInterval(refreshInterval);
            btn.innerHTML = '<i class="fas fa-pause"></i> Auto Refresh Off';
            btn.classList.remove('btn-info');
            btn.classList.add('btn-secondary');
        }
    }

    function refreshMonitor() {
        fetch(`/api/sessions/${sessionId}/progress`)
            .then(response => response.json())
            .then(data => {
                updateDisplay(data);
                updateChart(data);
            })
            .catch(error => console.error('Error:', error));
    }

    function updateDisplay(data) {
        // Update elapsed time
        const minutes = Math.floor(data.elapsed_time / 60);
        const seconds = data.elapsed_time % 60;
        document.getElementById('elapsedTime').innerHTML =
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        // Update progress bar
        const percentage = data.progress_percentage;
        const progressBar = document.getElementById('timeProgress');
        progressBar.style.width = percentage + '%';
        document.getElementById('timePercentage').innerHTML = Math.round(percentage) + '%';

        // Update counts
        document.getElementById('answeredCount').innerHTML = data.answered_count;
        document.getElementById('correctCount').innerHTML = data.correct_count;
        const accuracy = data.answered_count > 0 ? (data.correct_count / data.answered_count * 100).toFixed(1) : 0;
        document.getElementById('accuracyValue').innerHTML = accuracy + '%';

        // Update other info
        document.getElementById('skippedColumns').innerHTML = data.skipped_columns;
        document.getElementById('currentLine').innerHTML = data.current_line;

        // Update status badge color
        if (percentage >= 95) {
            document.getElementById('timeProgress').classList.remove('bg-primary');
            document.getElementById('timeProgress').classList.add('bg-danger');
        } else if (percentage >= 75) {
            document.getElementById('timeProgress').classList.remove('bg-primary');
            document.getElementById('timeProgress').classList.add('bg-warning');
        }
    }

    function updateChart(data) {
        const ctx = document.getElementById('liveProgressChart').getContext('2d');

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.time_labels || [],
                datasets: [{
                    label: 'Answers per Minute',
                    data: data.answers_per_minute || [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' answers';
                            }
                        }
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
                            text: 'Time (Minutes)'
                        }
                    }
                }
            }
        });
    }

    function cancelSession() {
        Swal.fire({
            title: 'Cancel Test Session?',
            text: 'This action cannot be undone. The participant will be kicked out.',
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
                            setTimeout(() => {
                                window.location.href = '{{ route('
                                tester.sessions ') }}';
                            }, 1500);
                        } else {
                            Swal.fire('Error!', 'Failed to cancel session.', 'error');
                        }
                    });
            }
        });
    }

    // Initial load
    refreshMonitor();

    // Cleanup
    window.onbeforeunload = function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    };
</script>
@endpush