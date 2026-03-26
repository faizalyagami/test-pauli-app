{{-- resources/views/tester/tests/partials/stats.blade.php --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Questions</h6>
                <h3 class="mb-0">{{ number_format($test->total_questions) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">Columns × Rows</h6>
                <h3 class="mb-0">{{ $test->total_columns }} × {{ $test->rows_per_column }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">Duration</h6>
                <h3 class="mb-0">{{ $test->duration_minutes }} minutes</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card bg-light border-0">
            <div class="card-body text-center">
                <h6 class="text-muted">Status</h6>
                <h3 class="mb-0">
                    <span class="badge bg-{{ $test->is_active ? 'success' : 'danger' }} px-3 py-2">
                        {{ $test->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </h3>
            </div>
        </div>
    </div>
</div>