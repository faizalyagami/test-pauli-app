{{-- resources/views/tester/tests/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Test - ' . $test->test_name)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-edit me-2 text-primary"></i> Edit Test: {{ $test->test_name }}</h6>
        <button onclick="generateQuestions()" class="btn btn-warning btn-sm">
            <i class="fas fa-sync-alt me-1"></i> Generate Questions
        </button>
    </div>
    <div class="card-body">
        <form action="{{ route('tester.tests.update', $test) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Test Code</label>
                    <input type="text" name="test_code" class="form-control" value="{{ $test->test_code }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Test Name</label>
                    <input type="text" name="test_name" class="form-control" value="{{ $test->test_name }}" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $test->description }}</textarea>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" class="form-control" value="{{ $test->duration_minutes }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Total Questions</label>
                    <input type="number" name="total_questions" class="form-control" value="{{ $test->total_questions }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Total Columns</label>
                    <input type="number" name="total_columns" class="form-control" value="{{ $test->total_columns }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Rows per Column</label>
                    <input type="number" name="rows_per_column" class="form-control" value="{{ $test->rows_per_column }}" required>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ $test->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Test</button>
            <a href="{{ route('tester.tests') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-table me-2 text-primary"></i> Questions Grid</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                @for($row = 1; $row <= $test->rows_per_column; $row++)
                    <tr>
                        @for($col = 1; $col <= $test->total_columns; $col++)
                            @php
                            $question = $questions[$col]->firstWhere('row_number', $row);
                            @endphp
                            <td class="text-center" style="min-width: 60px;">
                                <input type="number" class="form-control form-control-sm question-value"
                                    data-col="{{ $col }}" data-row="{{ $row }}"
                                    value="{{ $question ? $question->value : '' }}"
                                    style="width: 60px; text-align: center;">
                            </td>
                            @endfor
                    </tr>
                    @endfor
            </table>
        </div>
    </div>
</div>

<script>
    function generateQuestions() {
        if (confirm('Generate random questions? This will overwrite existing values.')) {
            fetch('{{ route("tester.tests.generate", $test) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(() => location.reload());
        }
    }

    document.querySelectorAll('.question-value').forEach(input => {
        input.addEventListener('change', function() {
            fetch(`/tester/questions/{{ $test->id }}/${this.dataset.col}/${this.dataset.row}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    value: this.value
                })
            });
        });
    });
</script>
@endsection