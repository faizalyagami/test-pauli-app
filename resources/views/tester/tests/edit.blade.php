{{-- resources/views/tester/tests/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Test - ' . $test->test_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-edit me-2 text-primary"></i> Edit Test: {{ $test->test_name }}
                </h6>
                <button onclick="generateQuestions()" class="btn btn-warning btn-sm">
                    <i class="fas fa-sync-alt me-1"></i> Generate Questions
                </button>
            </div>
            <div class="card-body">
                <form action="{{ route('tester.tests.update', $test) }}" method="POST" id="editTestForm">
                    @csrf 
                    @method('PUT')
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" class="form-control" value="{{ $test->duration_minutes }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total Columns</label>
                            <input type="number" name="total_columns" class="form-control" value="{{ $test->total_columns }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Rows per Column</label>
                            <input type="number" name="rows_per_column" class="form-control" value="{{ $test->rows_per_column }}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Total Questions:</strong> 
                                {{ $test->total_columns * ($test->rows_per_column - 1) }} questions 
                                ({{ $test->total_columns }} columns × {{ $test->rows_per_column - 1 }} additions)
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ $test->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tester.tests') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="updateBtn">
                            <i class="fas fa-save me-1"></i> Update Test
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-semibold">
                    <i class="fas fa-table me-2 text-primary"></i> Questions Grid
                </h6>
                <small class="text-muted">Edit individual question values (0-9)</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Row/Col</th>
                                @for($col = 1; $col <= $test->total_columns; $col++)
                                    <th class="text-center">Col {{ $col }}</th>
                                @endfor
                             </thead>
                        <tbody>
                            @for($row = 1; $row <= $test->rows_per_column; $row++)
                            <tr>
                                <td class="text-center bg-light fw-bold">Row {{ $row }}</td>
                                @for($col = 1; $col <= $test->total_columns; $col++)
                                    @php
                                        $question = isset($questions[$col]) ? $questions[$col]->firstWhere('row_number', $row) : null;
                                        $value = $question ? $question->value : '';
                                        $questionId = $question ? $question->id : null;
                                    @endphp
                                    <td class="text-center p-1">
                                        <input type="number" 
                                               class="form-control form-control-sm question-value text-center" 
                                               data-id="{{ $questionId }}"
                                               data-col="{{ $col }}" 
                                               data-row="{{ $row }}"
                                               value="{{ $value }}" 
                                               min="0" 
                                               max="9"
                                               step="1"
                                               style="width: 70px; margin: 0 auto;">
                                    </td>
                                @endfor
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .question-value {
        font-family: monospace;
        font-size: 14px;
        font-weight: bold;
        transition: all 0.2s ease;
    }
    
    .question-value:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        outline: none;
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
</style>

<script>
    function generateQuestions() {
        Swal.fire({
            title: 'Generate Questions?',
            html: 'This will generate random questions and overwrite existing ones.<br><strong>This action cannot be undone!</strong>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, generate!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generating...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch('{{ route("tester.tests.generate", $test) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Questions generated successfully!',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message || 'Failed to generate questions', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Network error. Please try again.', 'error');
                });
            }
        });
    }

    // Update question value when changed
    document.querySelectorAll('.question-value').forEach(input => {
        input.addEventListener('change', function() {
            const questionId = this.dataset.id;
            const value = this.value;
            
            // Validasi nilai
            if (value < 0 || value > 9) {
                Swal.fire('Error!', 'Value must be between 0 and 9', 'error');
                this.value = this.value > 9 ? 9 : (this.value < 0 ? 0 : this.value);
                return;
            }
            
            // Jika belum ada questionId (belum pernah disimpan), tidak perlu update
            if (!questionId || questionId === '') {
                console.log('No question ID yet, skipping update');
                return;
            }
            
            // Kirim update ke server
            fetch(`/tester/questions/${questionId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Beri feedback visual
                    this.style.borderColor = '#28a745';
                    setTimeout(() => {
                        this.style.borderColor = '';
                    }, 1000);
                } else {
                    Swal.fire('Error!', 'Failed to update question', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Network error. Please try again.', 'error');
            });
        });
    });

    // Prevent double submit
    document.getElementById('editTestForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('updateBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
    });
</script>
@endsection