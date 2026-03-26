{{-- resources/views/tester/applicants/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Applicants')

@section('content')
@include('tester.applicants.partials.stats-cards')

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-users me-2"></i> Applicants List</h5>
        <div class="card-tools">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i> Import
            </button>
            <a href="{{ route('tester.applicants.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-1"></i> Add New
            </a>
        </div>
    </div>

    @include('tester.applicants.partials.filters')

    <div class="card-body">
        @include('tester.applicants.partials.table')

        <div class="mt-3">
            {{ $applicants->links() }}
        </div>
    </div>
</div>

@include('tester.applicants.partials.import-modal')
@endsection

@push('scripts')
<script>
    function deleteApplicant(id, name) {
        Swal.fire({
            title: 'Delete Applicant?',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    function filterByStatus(status) {
        window.location.href = `{{ route('tester.applicants') }}?status=${status}`;
    }
</script>
@endpush