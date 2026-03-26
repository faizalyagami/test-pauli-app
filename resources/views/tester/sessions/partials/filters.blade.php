{{-- resources/views/tester/sessions/partials/filters.blade.php --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i> Filter Sessions
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('tester.sessions') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="all">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>Evaluated</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Test</label>
                <select name="test_id" class="form-select">
                    <option value="">All Tests</option>
                    @foreach($tests ?? [] as $test)
                    <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                        {{ $test->test_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Apply Filter
                </button>
                <a href="{{ route('tester.sessions') }}" class="btn btn-secondary">
                    <i class="fas fa-sync me-1"></i> Reset
                </a>
                <button type="button" class="btn btn-success float-end" onclick="exportData()">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function exportData() {
        const status = document.querySelector('select[name="status"]').value;
        const testId = document.querySelector('select[name="test_id"]').value;
        const dateFrom = document.querySelector('input[name="date_from"]').value;
        const dateTo = document.querySelector('input[name="date_to"]').value;

        window.location.href = `{{ route('tester.reports.export-excel') }}?status=${status}&test_id=${testId}&date_from=${dateFrom}&date_to=${dateTo}`;
    }
</script>