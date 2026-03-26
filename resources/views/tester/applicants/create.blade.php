{{-- resources/views/tester/applicants/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add New Applicant')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-plus me-2"></i> Add New Applicant</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tester.applicants.store') }}" method="POST">
            @csrf

            @include('tester.applicants.partials.form')

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Applicant
                </button>
                <a href="{{ route('tester.applicants') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection