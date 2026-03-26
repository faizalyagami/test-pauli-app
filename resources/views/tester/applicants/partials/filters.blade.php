{{-- resources/views/tester/applicants/partials/filters.blade.php --}}
<div class="card-body bg-light">
    <form method="GET" action="{{ route('tester.applicants') }}" class="row">
        <div class="col-md-3 mb-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="all">All Status</option>
                <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Registered</option>
                <option value="test_taken" {{ request('status') == 'test_taken' ? 'selected' : '' }}>Test Taken</option>
                <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div class="col-md-3 mb-2">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Name or participant number..." value="{{ request('search') }}">
        </div>

        <div class="col-md-2 mb-2">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="">All</option>
                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <label class="form-label">Date From</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>

        <div class="col-md-2 mb-2">
            <label class="form-label">Date To</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>

        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Search
            </button>
            <a href="{{ route('tester.applicants') }}" class="btn btn-secondary">
                <i class="fas fa-sync me-1"></i> Reset
            </a>
            <a href="{{ route('tester.applicants.export') }}" class="btn btn-success float-end">
                <i class="fas fa-file-excel me-1"></i> Export
            </a>
        </div>
    </form>
</div>