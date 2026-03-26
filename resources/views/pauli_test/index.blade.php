{{-- resources/views/pauli-test/index.blade.php --}}
@extends('layouts.guest')

@section('title', 'Tes Pauli - ' . $applicant->full_name)

@section('content')
<div class="test-header p-4 bg-primary text-white">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-calculator"></i> Tes Pauli</h2>
            <p class="mb-0">Nama: {{ $applicant->full_name }} | No. Peserta: {{ $applicant->participant_numb }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="timer-box bg-white text-dark rounded p-3 d-inline-block">
                <div class="timer" id="timer" style="font-size: 48px; font-family: monospace;">00:00</div>
                <div class="small">Sisa Waktu</div>
            </div>
        </div>
    </div>
</div>

<div class="test-body p-4">
    <form id="pauliTestForm">
        @csrf
        <input type="hidden" name="session_id" value="{{ $session->id }}">
        <input type="hidden" id="currentLine" value="0">

        <div class="table-responsive" id="pauliGrid">
            {{-- Grid akan di-generate dengan JavaScript --}}
        </div>

        <div class="row mt-4">
            <div class="col text-center">
                <button type="button" class="btn btn-warning btn-lg" id="skipColumnBtn">
                    <i class="fas fa-forward"></i> Lewati Kolom (Parit)
                </button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col text-center text-muted">
                <small>Garis ke-<span id="lineCount">0</span> / 20</small>
                <span class="mx-3">|</span>
                <small>Kolom Dilewati: <span id="skippedCount">0</span></small>
            </div>
        </div>
    </form>
</div>

@include('pauli-test.partials.instruction-modal')
@endsection

@push('styles')
<style>
    .pauli-grid {
        font-family: 'Courier New', monospace;
        border-collapse: collapse;
        width: 100%;
    }

    .pauli-grid th {
        background: #f8f9fa;
        padding: 10px;
        border: 1px solid #ddd;
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .pauli-grid td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        vertical-align: middle;
        min-width: 90px;
    }

    .top-number {
        font-size: 18px;
        font-weight: bold;
        color: #3498db;
    }

    .bottom-number {
        font-size: 18px;
        font-weight: bold;
        color: #27ae60;
        margin-top: 5px;
    }

    .answer-input {
        width: 60px;
        text-align: center;
        border: 2px solid #ddd;
        border-radius: 5px;
        padding: 8px;
        font-size: 18px;
        font-family: monospace;
    }

    .answer-input:focus {
        border-color: #3498db;
        outline: none;
    }

    .answer-input:disabled {
        background: #f8f9fa;
        cursor: not-allowed;
    }

    .line-marker {
        border-bottom: 3px solid red !important;
        animation: blink 1s ease-in-out 3;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .skipped-column {
        background-color: #ffe0e0 !important;
        position: relative;
    }

    .skipped-column::before {
        content: 'SKIP';
        position: absolute;
        top: 5px;
        right: 5px;
        background: red;
        color: white;
        font-size: 10px;
        padding: 2px 5px;
        border-radius: 3px;
    }

    .timer-box {
        min-width: 150px;
    }

    .instruction-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .instruction-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .example-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
        font-size: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
    let testData = @json($testData);
    let sessionId = {
        {
            $session - > id
        }
    };
    let totalColumns = {
        {
            $test - > total_columns
        }
    };
    let rowsPerColumn = {
        {
            $test - > rows_per_column
        }
    };
    let totalDuration = {
        {
            $test - > duration_minutes * 60
        }
    };

    let startTime = new Date();
    let currentLine = 0;
    let skippedColumns = [];
    let answers = {};
    let timerInterval;
    let lineInterval;
    let totalSeconds = totalDuration;
    let isTestActive = true;

    function renderGrid() {
        let html = '<table class="pauli-grid"><thead><tr>';
        for (let col = 1; col <= totalColumns; col++) {
            html += `<th>Kolom ${col}</th>`;
        }
        html += '</tr></thead><tbody>';

        for (let row = 1; row <= rowsPerColumn; row++) {
            html += '<tr>';
            for (let col = 1; col <= totalColumns; col++) {
                let value = testData[col - 1]?.[row - 1]?.value || '';
                let isSkipped = skippedColumns.includes(col);
                let answer = answers[`${col}_${row}`] || '';
                let isLineMarker = (row === Math.ceil(currentLine / (totalColumns / 5)) && currentLine > 0);

                let cellClass = '';
                if (isSkipped) cellClass += ' skipped-column';
                if (isLineMarker) cellClass += ' line-marker';

                html += `<td class="${cellClass}" data-col="${col}" data-row="${row}">
                    <div class="top-number">${value}</div>`;

                if (row < rowsPerColumn) {
                    let nextValue = testData[col - 1]?.[row]?.value || '';
                    html += `<div class="my-2">
                        <input type="text" class="answer-input" data-col="${col}" data-row="${row}" 
                               value="${answer}" maxlength="1" ${isSkipped ? 'disabled' : ''}
                               style="width: 60px; text-align: center;">
                        <div class="bottom-number">${nextValue}</div>
                    </div>`;
                }
                html += `</td>`;
            }
            html += '</tr>';
        }
        html += '</tbody></table>';
        $('#pauliGrid').html(html);

        // Bind events
        $('.answer-input').off('input').on('input', function() {
            let value = $(this).val();
            if (value && !isNaN(value)) {
                let lastDigit = value.toString().slice(-1);
                $(this).val(lastDigit);
                let col = parseInt($(this).data('col'));
                let row = parseInt($(this).data('row'));
                saveAnswer(col, row, lastDigit);
            }
        });
    }

    function saveAnswer(col, row, answer) {
        let key = `${col}_${row}`;
        answers[key] = answer;

        $.ajax({
            url: '/pauli-test/save-answer',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId,
                column: col,
                row: row,
                answer: answer,
                time_taken: Math.floor((new Date() - startTime) / 1000),
                line_marker: currentLine
            },
            error: function(xhr) {
                console.error('Error saving answer:', xhr);
            }
        });
    }

    function startTimer() {
        timerInterval = setInterval(() => {
            if (!isTestActive) return;

            if (totalSeconds <= 0) {
                endTest();
            } else {
                totalSeconds--;
                let minutes = Math.floor(totalSeconds / 60);
                let seconds = totalSeconds % 60;
                $('#timer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);

                if (totalSeconds === 60) {
                    showToast('Sisa waktu 1 menit!', 'warning');
                }
            }
        }, 1000);
    }

    function startLineMarker() {
        lineInterval = setInterval(() => {
            if (!isTestActive) return;
            markLine();
        }, 180000);
        setTimeout(() => markLine(), 100);
    }

    function markLine() {
        currentLine++;
        $('#lineCount').text(currentLine);

        // Visual marker
        let rowToMark = Math.ceil(currentLine / (totalColumns / 5));
        if (rowToMark <= rowsPerColumn) {
            $(`.pauli-grid tbody tr:nth-child(${rowToMark}) td`).addClass('line-marker');
            setTimeout(() => {
                $(`.pauli-grid tbody tr:nth-child(${rowToMark}) td`).removeClass('line-marker');
            }, 3000);
        }

        $.ajax({
            url: '/pauli-test/mark-line',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId,
                line_number: currentLine,
                time_mark: Math.floor((new Date() - startTime) / 1000)
            }
        });

        if (currentLine >= 20) {
            clearInterval(lineInterval);
        }
    }

    function skipColumn() {
        let colToSkip = prompt(`Masukkan nomor kolom yang akan dilewati (1-${totalColumns}):`);
        if (colToSkip && !isNaN(colToSkip)) {
            let colNumber = parseInt(colToSkip);
            if (colNumber >= 1 && colNumber <= totalColumns && !skippedColumns.includes(colNumber)) {
                skippedColumns.push(colNumber);
                $(`.answer-input[data-col="${colNumber}"]`).prop('disabled', true);
                $(`td[data-col="${colNumber}"]`).addClass('skipped-column');
                $('#skippedCount').text(skippedColumns.length);

                $.ajax({
                    url: '/pauli-test/skip-column',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        session_id: sessionId,
                        column: colNumber
                    }
                });

                showToast(`Kolom ${colNumber} telah dilewati`, 'warning');
            } else {
                alert('Kolom tidak valid atau sudah dilewati!');
            }
        }
    }

    function endTest() {
        if (!isTestActive) return;
        isTestActive = false;
        clearInterval(timerInterval);
        clearInterval(lineInterval);

        let loading = showLoading();

        $.ajax({
            url: '/pauli-test/end',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId,
                end_time: new Date().toISOString()
            },
            success: function() {
                hideLoading(loading);
                window.location.href = '/pauli-test/result/' + sessionId;
            },
            error: function() {
                hideLoading(loading);
                alert('Terjadi kesalahan saat menyelesaikan tes');
            }
        });
    }

    function preventCopyPaste() {
        $(document).on('copy paste cut drag drop', function(e) {
            e.preventDefault();
            return false;
        });
        $(document).on('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    }

    $(document).ready(function() {
        renderGrid();
        startTimer();
        startLineMarker();
        preventCopyPaste();

        $('#skipColumnBtn').click(skipColumn);
    });
</script>
@endpush