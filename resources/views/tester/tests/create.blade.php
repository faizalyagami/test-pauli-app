{{-- resources/views/tester/tests/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Test')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-plus-circle me-2 text-primary"></i> Create New Test</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('tester.tests.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Test Code *</label>
                    <input type="text" name="test_code" class="form-control @error('test_code') is-invalid @enderror" required>
                    @error('test_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Test Name *</label>
                    <input type="text" name="test_name" class="form-control @error('test_name') is-invalid @enderror" required>
                    @error('test_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" class="form-control" value="30" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Total Questions *</label>
                    <input type="number" name="total_questions" class="form-control" value="300" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Total Columns *</label>
                    <input type="number" name="total_columns" class="form-control" value="50" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Rows per Column *</label>
                    <input type="number" name="rows_per_column" class="form-control" value="6" required>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Create Test</button>
                    <a href="{{ route('tester.tests') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection