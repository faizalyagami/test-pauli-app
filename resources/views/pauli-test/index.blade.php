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
        max-width: 100%;
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
        font-size: 48px !important;
        font-weight: bold;
        font-family: monospace;
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

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        max-height: 70vh;
        position: relative;
    }

    .pauli-grid {
        font-family: 'Courier New', monospace;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 100%;
    }

    .pauli-grid th {
        background: #f8f9fa;
        padding: 10px 5px;
        border: 1px solid #ddd;
        position: sticky;
        top: 0;
        z-index: 10;
        font-weight: bold;
        text-align: center;
        min-width: 70px;
    }

    .pauli-grid td {
        border: 1px solid #ddd;
        padding: 8px 4px;
        text-align: center;
        vertical-align: middle;
    }

    .top-number {
        font-size: 14px;
        font-weight: bold;
        color: #3498db;
    }

    .bottom-number {
        font-size: 14px;
        font-weight: bold;
        color: #27ae60;
        margin-top: 5px;
    }

    .answer-area {
        margin: 5px 0;
    }

    .answer-input {
        width: 50px;
        text-align: center;
        border: 2px solid #ddd;
        border-radius: 5px;
        padding: 5px;
        font-size: 14px;
        font-family: monospace;
        display: block;
        margin: 0 auto;
    }

    .answer-input:focus {
        border-color: #3498db;
        outline: none;
    }

    .answer-input:disabled {
        background: #f8f9fa;
        cursor: not-allowed;
    }

    /* Garis penanda pada input yang sedang dikerjakan */
    .answer-input.line-marker {
        border-bottom: 3px solid red !important;
        animation: blink 1s ease-in-out 3;
    }

    @keyframes blink {

        0%,
        100% {
            border-bottom-color: red;
        }

        50% {
            border-bottom-color: #ff6666;
        }
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
        z-index: 5;
    }

    @media (max-width: 768px) {
        .pauli-grid th {
            font-size: 10px;
            padding: 5px;
            min-width: 50px;
        }

        .answer-input {
            width: 40px;
            font-size: 12px;
            padding: 3px;
        }

        .top-number,
        .bottom-number {
            font-size: 11px;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded');

        // Data dari server
        var testData = @json($testData);
        var sessionId = @json($session->id);
        var totalColumns = @json($test->total_columns);
        var rowsPerColumn = @json($test->rows_per_column);
        var totalDuration = @json($test->duration_minutes * 60);

        var totalAnswerRows = rowsPerColumn - 1;

        var startTime = null;
        var currentLine = 0;
        var skippedColumns = [];
        var answers = {};
        var timerInterval = null;
        var lineInterval = null;
        var remainingSeconds = totalDuration;
        var isTestActive = false;
        var gridRendered = false;
        var currentRow = 1;

        // 🔥 TRACK INPUT TERAKHIR
        var lastAnswered = null;

        // ================= RENDER GRID =================
        function renderGrid() {
            if (gridRendered) return;

            var html = '<table class="pauli-grid"><thead><tr>';
            for (var col = 1; col <= totalColumns; col++) {
                html += '<th>Kolom ' + col + '</th>';
            }
            html += '</tr></thead><tbody>';

            html += '<tr>';
            for (var col = 1; col <= totalColumns; col++) {
                var value = testData[col - 1]?.[0]?.value ?? '?';
                html += '<td><div class="top-number">' + value + '</div></td>';
            }
            html += '</tr>';

            for (var row = 1; row <= totalAnswerRows; row++) {
                html += '<tr>';
                for (var col = 1; col <= totalColumns; col++) {
                    var isSkipped = skippedColumns.includes(col);
                    var answer = answers[col + '_' + row] || '';
                    var bottomValue = testData[col - 1]?.[row]?.value ?? '?';

                    html += '<td data-col="' + col + '" data-row="' + row + '">';
                    html += '<input type="text" class="answer-input" data-col="' + col + '" data-row="' + row + '" value="' + answer + '" maxlength="1" ' + (isSkipped ? 'disabled' : '') + '>';
                    html += '<div class="bottom-number">' + bottomValue + '</div>';
                    html += '</td>';
                }
                html += '</tr>';
            }

            html += '</tbody></table>';

            document.getElementById('pauliGridContainer').innerHTML = html;
            gridRendered = true;
            document.getElementById('skipColumnBtn').style.display = 'inline-block';
        }

        // ================= SAVE ANSWER =================
        function saveAnswer(col, row, answer) {
            answers[col + '_' + row] = answer;

            // 🔥 SIMPAN POSISI TERAKHIR
            lastAnswered = { col: col, row: row };

            var elapsedSeconds = Math.floor((new Date() - startTime) / 1000);

            fetch('/pauli-test/save-answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    column: col,
                    row: row,
                    answer: answer,
                    time_taken: elapsedSeconds,
                    line_marker: currentLine
                })
            });
        }

        // ================= INPUT =================
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('answer-input')) {
                var input = e.target;
                var value = input.value;

                if (value && !isNaN(value)) {
                    var lastDigit = value.toString().slice(-1);
                    input.value = lastDigit;

                    var col = parseInt(input.dataset.col);
                    var row = parseInt(input.dataset.row);

                    saveAnswer(col, row, lastDigit);
                }
            }
        });

        // ================= TIMER =================
        function updateTimer() {
            if (!isTestActive) return;

            var minutes = Math.floor(remainingSeconds / 60);
            var seconds = remainingSeconds % 60;

            document.getElementById('timer').textContent =
                minutes.toString().padStart(2, '0') + ':' +
                seconds.toString().padStart(2, '0');

            if (remainingSeconds <= 0) endTest();
            remainingSeconds--;
        }

        function startTimer() {
            timerInterval = setInterval(updateTimer, 1000);
            updateTimer();
        }

        // ================= GARIS TIAP 3 MENIT =================
        function markLine() {
            if (!isTestActive || !lastAnswered) return;

            currentLine++;
            document.getElementById('lineCount').textContent = currentLine;

            var selector = '.answer-input[data-col="' + lastAnswered.col + '"][data-row="' + lastAnswered.row + '"]';
            var input = document.querySelector(selector);

            if (input) {
                input.classList.add('line-marker'); // 🔴 garis merah (permanen)
            }

            var elapsedSeconds = Math.floor((new Date() - startTime) / 1000);

            fetch('/pauli-test/mark-line', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    line_number: currentLine,
                    time_mark: elapsedSeconds
                })
            });

            if (currentLine >= 20) clearInterval(lineInterval);
        }

        function startLineMarker() {
            lineInterval = setInterval(markLine, 180000); // 3 menit
        }

        // ================= END =================
        function endTest() {
            isTestActive = false;
            clearInterval(timerInterval);
            clearInterval(lineInterval);

            fetch('/pauli-test/end', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: sessionId
                })
            }).then(() => {
                window.location.href = '/pauli-test/result/' + sessionId;
            });
        }

        // ================= START =================
        function startTest() {
            document.getElementById('testBody').style.display = 'block';
            startTime = new Date();
            renderGrid();
            startTimer();
            startLineMarker();
            isTestActive = true;
        }

        document.getElementById('startTestBtn').addEventListener('click', function() {
            document.getElementById('instructionModal').style.display = 'none';
            startTest();
        });
    });
</script>
@endsection