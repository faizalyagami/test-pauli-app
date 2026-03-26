{{-- resources/views/tester/tests/partials/questions-grid.blade.php --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
            <i class="fas fa-table me-2 text-primary"></i> Questions Grid
        </h6>
        <button onclick="generateQuestions()" class="btn btn-sm btn-warning">
            <i class="fas fa-sync-alt me-1"></i> Generate All Questions
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="text-center" style="min-width: 60px;">Row/Col</th>
                        @for($col = 1; $col <= $test->total_columns; $col++)
                            <th class="text-center">Col {{ $col }}</th>
                            @endfor
                </thead>
                <tbody>
                    @for($row = 1; $row <= $test->rows_per_column; $row++)
                        <tr>
                            <td class="fw-bold text-center bg-light">Row {{ $row }}</td>
                            @for($col = 1; $col <= $test->total_columns; $col++)
                                @php
                                $question = $questions[$col]->firstWhere('row_number', $row);
                                $value = $question ? $question->value : '';
                                @endphp
                                <td class="text-center p-1">
                                    <input type="number"
                                        class="form-control form-control-sm question-value text-center"
                                        data-col="{{ $col }}"
                                        data-row="{{ $row }}"
                                        value="{{ $value }}"
                                        min="0"
                                        max="9"
                                        style="width: 55px; margin: 0 auto;">
                                </td>
                                @endfor
                        </tr>
                        @endfor
                </tbody>

        </div>
    </div>
</div>
</div>

<style>
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .question-value {
        font-family: monospace;
        font-size: 14px;
        font-weight: bold;
        transition: all 0.2s ease;
    }

    .question-value:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>