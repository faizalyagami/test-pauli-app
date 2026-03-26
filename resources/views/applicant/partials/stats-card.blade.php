{{-- resources/views/applicant/partials/stats-cards.blade.php --}}
<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Tests</h6>
                        <h2 class="mb-0">{{ number_format($stats['total_tests']) }}</h2>
                    </div>
                    <i class="fas fa-file-alt fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Completed</h6>
                        <h2 class="mb-0">{{ number_format($stats['completed_tests']) }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Average Score</h6>
                        <h2 class="mb-0">{{ number_format($stats['average_score'], 0) }}</h2>
                    </div>
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Best Score</h6>
                        <h2 class="mb-0">{{ number_format($stats['best_score'], 0) }}</h2>
                    </div>
                    <i class="fas fa-trophy fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>