{{-- resources/views/tester/sessions/partials/stats-cards.blade.php --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Sessions</h6>
                        <h2 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h2>
                    </div>
                    <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="fas fa-chart-line me-1"></i> All time
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">In Progress</h6>
                        <h2 class="mb-0">{{ number_format($stats['in_progress'] ?? 0) }}</h2>
                    </div>
                    <i class="fas fa-spinner fa-3x opacity-50"></i>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="fas fa-clock me-1"></i> Active now
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Completed</h6>
                        <h2 class="mb-0">{{ number_format($stats['completed'] ?? 0) }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="fas fa-percent me-1"></i> Avg Score: {{ number_format($stats['avg_score'] ?? 0, 0) }}
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Evaluated</h6>
                        <h2 class="mb-0">{{ number_format($stats['evaluated'] ?? 0) }}</h2>
                    </div>
                    <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="fas fa-users me-1"></i> Reviewed
                </small>
            </div>
        </div>
    </div>
</div>