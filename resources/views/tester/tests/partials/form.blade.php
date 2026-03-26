{{-- resources/views/tester/tests/partials/form.blade.php --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold small text-muted">Test Code *</label>
        <input type="text" name="test_code" class="form-control @error('test_code') is-invalid @enderror"
            value="{{ old('test_code', $test->test_code ?? '') }}" required>
        @error('test_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold small text-muted">Test Name *</label>
        <input type="text" name="test_name" class="form-control @error('test_name') is-invalid @enderror"
            value="{{ old('test_name', $test->test_name ?? '') }}" required>
        @error('test_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 mb-3">
        <label class="form-label fw-semibold small text-muted">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $test->description ?? '') }}</textarea>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold small text-muted">Duration (minutes) *</label>
        <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $test->duration_minutes ?? 30) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold small text-muted">Total Questions *</label>
        <input type="number" name="total_questions" class="form-control" value="{{ old('total_questions', $test->total_questions ?? 300) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold small text-muted">Total Columns *</label>
        <input type="number" name="total_columns" class="form-control" value="{{ old('total_columns', $test->total_columns ?? 50) }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label fw-semibold small text-muted">Rows per Column *</label>
        <input type="number" name="rows_per_column" class="form-control" value="{{ old('rows_per_column', $test->rows_per_column ?? 6) }}" required>
    </div>
    <div class="col-12 mb-3">
        <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ old('is_active', $test->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="isActive">Active</label>
        </div>
    </div>
</div>