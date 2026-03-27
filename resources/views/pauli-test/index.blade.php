{{-- resources/views/pauli-test/index.blade.php --}}
@extends('layouts.guest')

@section('title', 'Pauli Test - ' . $applicant->full_name)

@section('content')
<div class="test-container">
    <div class="test-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3><i class="fas fa-calculator"></i> Tes Pauli</h3>
                <p class="mb-0">Nama: {{ $applicant->full_name }}</p>
                <p class="mb-0">No. Peserta: {{ $applicant->participant_numb }}</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="timer-box bg-white text-dark rounded p-3 d-inline-block">
                    <div class="timer" id="timer" style="font-size: 48px; font-family: monospace; font-weight: bold;">00:00</div>
                    <div class="small">Sisa Waktu</div>
                </div>
            </div>
        </div>
    </div>

    <div class="test-body p-4" id="testBody" style="display: none;">
        <form id="pauliTestForm">
            @csrf
            <input type="hidden" name="session_id" value="{{ $session->id }}">
            <input type="hidden" id="totalColumns" value="{{ $test->total_columns }}">
            <input type="hidden" id="rowsPerColumn" value="{{ $test->rows_per_column }}">

            <div class="table-wrapper">
                <div id="pauliGridContainer"></div>
            </div>

            <div class="row mt-4">
                <div class="col text-center">
                    <button type="button" class="btn btn-warning btn-lg" id="skipColumnBtn" style="display: none;">
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
</div>

@include('pauli-test.partials.instruction-modal')

<style>
    .test-container {
        max-width: 95%;
        margin: 20px auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    .test-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
    }

    .timer-box {
        min-width: 150px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .timer {
        color: #2c3e50;
    }

    .timer.warning {
        color: #e74c3c;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }
    
    .pauli-grid {
        font-family: 'Courier New', monospace;
        border-collapse: collapse;
        width: max-content;
        /* width: 100%;
        margin: 0; */
    }

    .pauli-grid th {
        background: #f8f9fa;
        padding: 10px;
        border: 1px solid #ddd;
        position: sticky;
        top: 0;
        z-index: 10;
        font-weight: bold;
    }

    .pauli-grid td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
        vertical-align: middle;
        min-width: 90px;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .top-number {
        font-size: 16px;
        font-weight: bold;
        color: #3498db;
        margin-bottom: 5px;
    }

    .bottom-number {
        font-size: 16px;
        font-weight: bold;
        color: #27ae60;
        margin-top: 5px;
    }

    .answer-input {
        width: 55px;
        text-align: center;
        border: 2px solid #ddd;
        border-radius: 5px;
        padding: 5px;
        font-size: 16px;
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
    }

    .skipped-column {
        background-color: #ffe0e0 !important;
        position: relative;
    }

    .skipped-column::before {
        content: 'SKIP';
        position: absolute;
        top: 2px;
        right: 2px;
        background: red;
        color: white;
        font-size: 8px;
        padding: 2px 4px;
        border-radius: 3px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        // Data dari server
        const testData = @json($testData);
        const sessionId = {{ $session->id }};
        const totalColumns = {{ $test->total_columns }};
        const rowsPerColumn = {{ $test->rows_per_column }};
        const totalDuration = {{ $test->duration_minutes * 60 }};

        const testStatus = "{{ $session->status }}";
        const startedAt = "{{ $session->started_at }}";

        // Variables
        let startTime = null;
        let currentLine = 0;
        let skippedColumns = [];
        let answers = {};
        let timerInterval = null;
        let lineInterval = null;
        let remainingSeconds = totalDuration;
        let isTestActive = false;

        // ================= RENDER GRID =================
        function renderGrid() {
            let html = '<table class="pauli-grid"><thead><tr>';

            for (let col = 1; col <= totalColumns; col++) {
                html += `<th>Kolom ${col}</th>`;
            }

            html += '</tr></thead><tbody>';

            for (let row = 1; row <= rowsPerColumn; row++) {
                html += '<tr>';

                for (let col = 1; col <= totalColumns; col++) {

                    const cellData = testData[col - 1]?.[row - 1] || { value: '' };
                    const value = cellData.value || '';
                    const isSkipped = skippedColumns.includes(col);
                    const answer = answers[`${col}_${row}`] || '';

                    html += `<td class="${isSkipped ? 'skipped-column' : ''}" data-col="${col}" data-row="${row}">
                        <div class="top-number">${value}</div>`;

                    if (row < rowsPerColumn) {
                        const nextValue = testData[col - 1]?.[row]?.value || '';

                        html += `
                            <input type="text" class="answer-input" data-col="${col}" data-row="${row}" 
                            value="${answer}" maxlength="1" ${isSkipped ? 'disabled' : ''}>
                            <div class="bottom-number">${nextValue}</div>
                        `;
                    }

                    html += `</td>`;
                }

                html += '</tr>';
            }

            html += '</tbody></table>';

            $('#pauliGridContainer').html(html);

            $('#skipColumnBtn').show();
        }

        // ================= SAVE ANSWER =================
        function saveAnswer(col, row, answer) {

            const key = `${col}_${row}`;
            answers[key] = answer;

            const elapsedSeconds = Math.floor((new Date() - startTime) / 1000);

            $.post('/pauli-test/save-answer', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId,
                column: col,
                row: row,
                answer: answer,
                time_taken: elapsedSeconds,
                line_marker: currentLine
            });
        }

        // ================= INPUT HANDLER =================
        $(document).on('input', '.answer-input', function() {

            let value = $(this).val();

            if (value && !isNaN(value)) {

                let lastDigit = value.toString().slice(-1);
                $(this).val(lastDigit);

                let col = $(this).data('col');
                let row = $(this).data('row');

                saveAnswer(col, row, lastDigit);
            }
        });

        // ================= TIMER =================
        function updateTimer() {

            if (!isTestActive) return;

            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;

            $('#timer').text(
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
            );

            if (remainingSeconds <= 60) {
                $('#timer').addClass('warning');
            }

            if (remainingSeconds <= 0) {
                endTest();
            }

            remainingSeconds--;
        }

        function startTimer() {

            if (timerInterval) clearInterval(timerInterval);

            timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        }

        // ================= LINE MARKER =================
        function markLine() {

            if (!isTestActive) return;

            currentLine++;
            $('#lineCount').text(currentLine);

            const rowToMark = Math.ceil(currentLine / (totalColumns / 5));

            if (rowToMark <= rowsPerColumn) {

                $(`.pauli-grid tbody tr:nth-child(${rowToMark}) td`)
                    .addClass('line-marker');

                setTimeout(() => {
                    $(`.pauli-grid tbody tr:nth-child(${rowToMark}) td`)
                        .removeClass('line-marker');
                }, 3000);
            }

            const elapsedSeconds = Math.floor((new Date() - startTime) / 1000);

            $.post('/pauli-test/mark-line', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId,
                line_number: currentLine,
                time_mark: elapsedSeconds
            });
        }

        function startLineMarker() {
            if (lineInterval) clearInterval(lineInterval);
            lineInterval = setInterval(markLine, 180000);
            setTimeout(markLine, 100);
        }

        // ================= SKIP =================
        function skipColumn() {

            const col = prompt(`Kolom (1-${totalColumns})`);

            if (!col || isNaN(col)) return;

            const colNumber = parseInt(col);

            if (colNumber < 1 || colNumber > totalColumns || skippedColumns.includes(colNumber)) {
                alert('Tidak valid');
                return;
            }

            skippedColumns.push(colNumber);

            $(`.answer-input[data-col="${colNumber}"]`).prop('disabled', true);
            $(`td[data-col="${colNumber}"]`).addClass('skipped-column');

            $('#skippedCount').text(skippedColumns.length);

            $.post('/pauli-test/skip-column', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId,
                column: colNumber
            });
        }

        // ================= END =================
        function endTest() {

            if (!isTestActive) return;

            isTestActive = false;

            clearInterval(timerInterval);
            clearInterval(lineInterval);

            $.post('/pauli-test/end', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId
            }).done(() => {
                window.location.href = '/pauli-test/result/' + sessionId;
            });
        }

        // ================= START =================
        function startTest(resume = false) {

            $('#testBody').show();
            renderGrid();

            // hitung waktu jika resume
            if (resume && startedAt) {
                const start = new Date(startedAt);
                const now = new Date();
                const elapsed = Math.floor((now - start) / 1000);
                remainingSeconds = totalDuration - elapsed;

                if (remainingSeconds <= 0) {
                    endTest();
                    return;
                }

                startTime = start;
            } else {
                startTime = new Date();

                $.post('/pauli-test/start', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    session_id: sessionId
                });
            }

            startTimer();
            startLineMarker();

            isTestActive = true;

            $('#skipColumnBtn').off().on('click', skipColumn);
        }

        // ================= AUTO RESUME =================
        if (testStatus === 'started') {
            $('#instructionModal').hide();
            startTest(true);
        } 
        else if (testStatus === 'finished') {
            window.location.href = '/pauli-test/result/' + sessionId;
        } 
        else {
            $('#instructionModal').show();
        }

        // ================= START BUTTON =================
        $('#startTestBtn').on('click', function() {
            $('#instructionModal').fadeOut(300, function() {
                startTest(false);
            });
        });

        // ================= WARNING REFRESH =================
        window.addEventListener('beforeunload', function (e) {
            if (isTestActive) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

    });
</script>
@endsection