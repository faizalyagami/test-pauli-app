{{-- resources/views/tester/tests/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Test')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-plus-circle me-2 text-primary"></i> Create New Test
                </h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tester.tests.store') }}" method="POST" id="createTestForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Test Code <span class="text-danger">*</span></label>
                            <input type="text" name="test_code" class="form-control @error('test_code') is-invalid @enderror" 
                                   value="{{ old('test_code') }}" required>
                            @error('test_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unique identifier (e.g., PAULI-001)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Test Name <span class="text-danger">*</span></label>
                            <input type="text" name="test_name" class="form-control @error('test_name') is-invalid @enderror" 
                                   value="{{ old('test_name') }}" required>
                            @error('test_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Columns <span class="text-danger">*</span></label>
                            <input type="number" name="total_columns" class="form-control @error('total_columns') is-invalid @enderror" required>
                            @error('total_columns')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of columns in test grid</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rows per Column <span class="text-danger">*</span></label>
                            <input type="number" name="rows_per_column" class="form-control @error('rows_per_column') is-invalid @enderror" required>
                            @error('rows_per_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of rows in each column</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Total Questions:</strong> 
                                {{ (old('total_columns', 50) * (old('rows_per_column', 6) - 1)) }} questions 
                                ({{ old('total_columns', 50) }} columns × {{ old('rows_per_column', 6) - 1 }} additions)
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    Active
                                </label>
                                <small class="text-muted d-block">If active, this test will be available for participants</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tester.tests') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-1"></i> Create Test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-calculate total questions
    const totalColumnsInput = document.querySelector('input[name="total_columns"]');
    const rowsPerColumnInput = document.querySelector('input[name="rows_per_column"]');
    const totalQuestionsAlert = document.querySelector('.alert-info strong');

    function updateTotalQuestions() {
        const columns = parseInt(totalColumnsInput.value) || 0;
        const rows = parseInt(rowsPerColumnInput.value) || 0;
        const totalQuestions = columns * (rows - 1);
        totalQuestionsAlert.textContent = totalQuestions + ' questions (' + columns + ' columns × ' + (rows - 1) + ' additions)';
    }

    totalColumnsInput?.addEventListener('input', updateTotalQuestions);
    rowsPerColumnInput?.addEventListener('input', updateTotalQuestions);
    updateTotalQuestions();

    // Prevent double submit
    document.getElementById('createTestForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
    });
</script>
@endsection