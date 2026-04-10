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
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
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

    /* Garis penanda pada baris yang sedang dikerjakan */
    .line-marker .answer-input {
        border-bottom: 3px solid red !important;
        animation: blink 1s ease-in-out 3;
    }
    
    @keyframes blink {
        0%, 100% { border-bottom-color: red; }
        50% { border-bottom-color: #ff6666; }
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
        
        .top-number, .bottom-number {
            font-size: 11px;
        }
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
        
        // Jumlah baris jawaban = rowsPerColumn - 1 (karena penjumlahan antara baris 1+2, 2+3, dst)
        const totalAnswerRows = rowsPerColumn - 1;

        // Variables
        let startTime = null;
        let currentLine = 0;
        let skippedColumns = [];
        let answers = {};
        let timerInterval = null;
        let lineInterval = null;
        let remainingSeconds = totalDuration;
        let isTestActive = false;
        let gridRendered = false;
        
        // Baris yang sedang dikerjakan (dimulai dari baris 1)
        let currentRow = 1;

        console.log('Total Columns:', totalColumns);
        console.log('Rows per Column:', rowsPerColumn);
        console.log('Total Answer Rows:', totalAnswerRows);

        // ================= RENDER GRID =================
        function renderGrid() {
            if (gridRendered) return;
            
            if (!testData || testData.length === 0) {
                $('#pauliGridContainer').html('<div class="alert alert-danger">Data soal tidak tersedia. Silahkan generate questions terlebih dahulu.</div>');
                return;
            }
            
            let html = '<table class="pauli-grid"><thead><tr>';
            for (let col = 1; col <= totalColumns; col++) {
                html += `<th>Kolom ${col}</th>`;
            }
            html += '</tr></thead><tbody>';

            // BARIS PERTAMA: Menampilkan angka atas (row 1)
            html += '<tr>';
            for (let col = 1; col <= totalColumns; col++) {
                let value = testData[col-1]?.[0]?.value || '?';
                html += `<td class="top-row-cell">
                    <div class="top-number">${value}</div>
                </td>`;
            }
            html += '</tr>';

            // BARIS JAWABAN: Untuk setiap baris jawaban (1 sampai totalAnswerRows)
            for (let row = 1; row <= totalAnswerRows; row++) {
                html += '<tr>';
                for (let col = 1; col <= totalColumns; col++) {
                    const isSkipped = skippedColumns.includes(col);
                    const answer = answers[`${col}_${row}`] || '';
                    
                    // Angka bawah adalah nilai dari baris ke-(row+1)
                    let bottomValue = testData[col-1]?.[row]?.value || '?';
                    
                    // Cek apakah baris ini sedang dikerjakan
                    const isCurrentRow = (row === currentRow && !isSkipped);
                    
                    html += `<td class="${isSkipped ? 'skipped-column' : ''}" data-col="${col}" data-row="${row}">
                        <div class="answer-area ${isCurrentRow ? 'line-marker' : ''}">
                            <input type="text" class="answer-input" data-col="${col}" data-row="${row}" 
                                   value="${answer}" maxlength="1" ${isSkipped ? 'disabled' : ''}
                                   placeholder="?">
                            <div class="bottom-number">${bottomValue}</div>
                        </div>
                    </td>`;
                }
                html += '</tr>';
            }
            
            html += '</tbody></table>';
            
            $('#pauliGridContainer').html(html);
            gridRendered = true;
            $('#skipColumnBtn').show();
            
            console.log('Grid rendered successfully');
        }

        // ================= UPDATE LINE MARKER =================
        function updateLineMarker() {
            // Hapus semua marker yang ada
            $('.answer-area').removeClass('line-marker');
            
            // Tambahkan marker pada baris yang sedang dikerjakan
            $(`.answer-area[data-row="${currentRow}"]`).addClass('line-marker');
        }

        // ================= SAVE ANSWER =================
        function saveAnswer(col, row, answer) {
            const key = `${col}_${row}`;
            answers[key] = answer;
            
            const elapsedSeconds = Math.floor((new Date() - startTime) / 1000);
            
            // Cek apakah semua kolom di baris ini sudah terisi
            let allFilled = true;
            for (let c = 1; c <= totalColumns; c++) {
                if (!skippedColumns.includes(c) && !answers[`${c}_${currentRow}`]) {
                    allFilled = false;
                    break;
                }
            }
            
            // Jika semua kolom di baris ini sudah terisi, pindah ke baris berikutnya
            if (allFilled && currentRow < totalAnswerRows) {
                currentRow++;
                updateLineMarker();
            }
            
            $.ajax({
                url: '/pauli-test/save-answer',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    session_id: sessionId,
                    column: col,
                    row: row,
                    answer: answer,
                    time_taken: elapsedSeconds,
                    line_marker: currentLine
                },
                error: function(xhr) {
                    console.error('Error saving answer:', xhr);
                }
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
            const timerElement = $('#timer');
            timerElement.text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
            
            if (remainingSeconds <= 60) {
                timerElement.addClass('warning');
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

        // ================= LINE MARKER SETIAP 3 MENIT =================
        function markLine() {
            if (!isTestActive) return;
            
            currentLine++;
            $('#lineCount').text(currentLine);
            
            // Efek visual garis pada baris yang sedang dikerjakan
            const $currentRowCells = $(`.answer-area[data-row="${currentRow}"]`);
            $currentRowCells.addClass('line-marker');
            
            // Hilangkan efek setelah 2 detik
            setTimeout(() => {
                if (isTestActive) {
                    $currentRowCells.removeClass('line-marker');
                    // Kembalikan marker ke baris yang sedang dikerjakan
                    $(`.answer-area[data-row="${currentRow}"]`).addClass('line-marker');
                }
            }, 2000);
            
            const elapsedSeconds = Math.floor((new Date() - startTime) / 1000);
            
            $.ajax({
                url: '/pauli-test/mark-line',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    session_id: sessionId,
                    line_number: currentLine,
                    time_mark: elapsedSeconds
                }
            });
            
            if (currentLine >= 20) {
                clearInterval(lineInterval);
            }
        }

        function startLineMarker() {
            if (lineInterval) clearInterval(lineInterval);
            lineInterval = setInterval(markLine, 180000); // 3 menit = 180000 ms
            setTimeout(markLine, 100);
        }

        // ================= SKIP COLUMN =================
        function skipColumn() {
            const col = prompt(`Masukkan nomor kolom yang akan dilewati (1-${totalColumns}):`);
            if (!col || isNaN(col)) return;
            
            const colNumber = parseInt(col);
            if (colNumber < 1 || colNumber > totalColumns || skippedColumns.includes(colNumber)) {
                alert('Kolom tidak valid atau sudah dilewati!');
                return;
            }
            
            skippedColumns.push(colNumber);
            $(`.answer-input[data-col="${colNumber}"]`).prop('disabled', true);
            $(`td[data-col="${colNumber}"]`).addClass('skipped-column');
            $('#skippedCount').text(skippedColumns.length);
            
            // Cek apakah semua kolom di baris ini sudah diisi atau dilewati
            let allCompleted = true;
            for (let c = 1; c <= totalColumns; c++) {
                if (!skippedColumns.includes(c) && !answers[`${c}_${currentRow}`]) {
                    allCompleted = false;
                    break;
                }
            }
            
            if (allCompleted && currentRow < totalAnswerRows) {
                currentRow++;
                updateLineMarker();
            }
            
            $.ajax({
                url: '/pauli-test/skip-column',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    session_id: sessionId,
                    column: colNumber
                }
            });
        }

        // ================= END TEST =================
        function endTest() {
            if (!isTestActive) return;
            
            isTestActive = false;
            if (timerInterval) clearInterval(timerInterval);
            if (lineInterval) clearInterval(lineInterval);
            
            $.ajax({
                url: '/pauli-test/end',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    session_id: sessionId,
                    end_time: new Date().toISOString()
                },
                success: function() {
                    window.location.href = '/pauli-test/result/' + sessionId;
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyelesaikan tes');
                }
            });
        }

        // ================= PREVENT COPY PASTE =================
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

        // ================= START TEST =================
        function startTest() {
            $('#testBody').show();
            startTime = new Date();
            renderGrid();
            startTimer();
            startLineMarker();
            preventCopyPaste();
            isTestActive = true;
            
            // Set initial line marker
            updateLineMarker();
            
            $('#skipColumnBtn').off('click').on('click', skipColumn);
        }

        // ================= EVENT START BUTTON =================
        $(document).on('click', '#startTestBtn', function() {
            $('#instructionModal').fadeOut(300, function() {
                startTest();
            });
        });
        
        // Tampilkan modal instruksi saat halaman dimuat
        $('#instructionModal').show();
    });
</script>
@endsection