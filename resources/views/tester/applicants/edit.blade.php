@extends('layouts.app')

@section('title', 'Edit Applicant - ' . $applicant->full_name)

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-edit me-2"></i> Edit Applicant</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tester.applicants.update', $applicant) }}" method="POST">
            @csrf
            @method('PUT')

            @include('tester.applicants.partials.form')

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Applicant
                </button>
                <a href="{{ route('tester.applicants.show', $applicant) }}" class="btn btn-info">
                    <i class="fas fa-eye me-1"></i> View Details
                </a>
                <a href="{{ route('tester.applicants') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </form>
    </div>
</div>
@endsection