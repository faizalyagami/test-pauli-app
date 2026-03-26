/**
 * Pauli Test Core Functionality
 */

class PauliTest {
    constructor(config) {
        this.config = {
            sessionId: config.sessionId,
            totalColumns: config.totalColumns,
            rowsPerColumn: config.rowsPerColumn,
            totalDuration: config.totalDuration,
            testData: config.testData,
            onAnswerSaved: config.onAnswerSaved || function() {},
            onTestEnd: config.onTestEnd || function() {}
        };
        
        this.startTime = null;
        this.currentLine = 0;
        this.skippedColumns = [];
        this.answers = {};
        this.timerInterval = null;
        this.lineInterval = null;
        this.totalSeconds = this.config.totalDuration;
        this.isTestActive = true;
        
        this.init();
    }
    
    init() {
        this.startTime = new Date();
        this.renderGrid();
        this.bindEvents();
        this.startTimer();
        this.startLineMarker();
        this.showInstructions();
        
        // Prevent copy paste and right click
        this.preventCopyPaste();
    }
    
    renderGrid() {
        let html = '<div class="table-responsive"><table class="pauli-grid"><thead><tr>';
        
        for (let col = 1; col <= this.config.totalColumns; col++) {
            html += `<th>Kolom ${col}</th>`;
        }
        html += '</tr></thead><tbody>';
        
        for (let row = 1; row <= this.config.rowsPerColumn; row++) {
            html += '<tr>';
            for (let col = 1; col <= this.config.totalColumns; col++) {
                const value = this.config.testData[col-1]?.[row-1]?.value || '';
                const isSkipped = this.skippedColumns.includes(col);
                const answer = this.answers[`${col}_${row}`] || '';
                const isLineMarker = this.shouldShowLineMarker(row);
                
                let cellClass = 'grid-cell';
                if (isSkipped) cellClass += ' skipped-column';
                if (isLineMarker) cellClass += ' line-marker';
                
                html += `<td class="${cellClass}" data-col="${col}" data-row="${row}">
                    <div class="top-number">${value}</div>`;
                
                if (row < this.config.rowsPerColumn) {
                    const nextValue = this.config.testData[col-1]?.[row]?.value || '';
                    html += `<div class="answer-area">
                        <input type="text" class="answer-input" data-col="${col}" data-row="${row}" 
                               value="${answer}" maxlength="1" ${isSkipped ? 'disabled' : ''}
                               placeholder="?">
                        <div class="bottom-number">${nextValue}</div>
                    </div>`;
                }
                html += `</td>`;
            }
            html += '</tr>';
        }
        html += '</tbody></table></div>';
        
        $('#pauliGrid').html(html);
        this.attachAnswerEvents();
    }
    
    attachAnswerEvents() {
        $('.answer-input').off('input').on('input', (e) => {
            const $input = $(e.target);
            let value = $input.val();
            
            if (value && !isNaN(value)) {
                // Only take the last digit
                const lastDigit = value.toString().slice(-1);
                $input.val(lastDigit);
                
                const col = parseInt($input.data('col'));
                const row = parseInt($input.data('row'));
                this.saveAnswer(col, row, lastDigit);
            }
        });
    }
    
    saveAnswer(col, row, answer) {
        const key = `${col}_${row}`;
        this.answers[key] = answer;
        
        const elapsedSeconds = Math.floor((new Date() - this.startTime) / 1000);
        
        $.ajax({
            url: '/pauli-test/save-answer',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: this.config.sessionId,
                column: col,
                row: row,
                answer: answer,
                time_taken: elapsedSeconds,
                line_marker: this.currentLine
            },
            success: (response) => {
                this.config.onAnswerSaved(col, row, answer);
            },
            error: (xhr) => {
                console.error('Error saving answer:', xhr);
                PauliTest.toast('Gagal menyimpan jawaban', 'danger');
            }
        });
    }
    
    startTimer() {
        this.timerInterval = setInterval(() => {
            if (!this.isTestActive) return;
            
            if (this.totalSeconds <= 0) {
                this.endTest();
            } else {
                this.totalSeconds--;
                this.updateTimerDisplay();
            }
        }, 1000);
        
        this.updateTimerDisplay();
    }
    
    updateTimerDisplay() {
        const minutes = Math.floor(this.totalSeconds / 60);
        const seconds = this.totalSeconds % 60;
        const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        $('#timer').text(timeString);
        
        // Warning when time is low
        if (this.totalSeconds <= 60) {
            $('#timer').css('color', 'red');
            if (this.totalSeconds === 60) {
                PauliTest.toast('Sisa waktu 1 menit!', 'warning');
            }
        }
    }
    
    startLineMarker() {
        // Mark line every 3 minutes (180 seconds)
        this.lineInterval = setInterval(() => {
            if (!this.isTestActive) return;
            this.markLine();
        }, 180000);
        
        // Mark first line after 1 second
        setTimeout(() => this.markLine(), 100);
    }
    
    markLine() {
        this.currentLine++;
        $('#lineCount').text(this.currentLine);
        
        // Add visual marker to current row
        const rowToMark = Math.ceil(this.currentLine / (this.config.totalColumns / 5));
        if (rowToMark <= this.config.rowsPerColumn) {
            $(`.pauli-grid tbody tr:nth-child(${rowToMark}) td`).addClass('line-marker');
            
            // Remove marker from previous row after 3 seconds
            setTimeout(() => {
                $(`.pauli-grid tbody tr:nth-child(${rowToMark}) td`).removeClass('line-marker');
            }, 3000);
        }
        
        // Save line position to server
        const elapsedSeconds = Math.floor((new Date() - this.startTime) / 1000);
        
        $.ajax({
            url: '/pauli-test/mark-line',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: this.config.sessionId,
                line_number: this.currentLine,
                time_mark: elapsedSeconds
            }
        });
        
        // Play beep sound (optional)
        this.playBeep();
        
        if (this.currentLine >= 20) {
            clearInterval(this.lineInterval);
        }
    }
    
    skipColumn() {
        const colToSkip = prompt('Masukkan nomor kolom yang akan dilewati (1-' + this.config.totalColumns + '):');
        
        if (colToSkip && !isNaN(colToSkip)) {
            const colNumber = parseInt(colToSkip);
            
            if (colNumber >= 1 && colNumber <= this.config.totalColumns) {
                if (!this.skippedColumns.includes(colNumber)) {
                    this.skippedColumns.push(colNumber);
                    
                    // Disable inputs in skipped column
                    $(`.answer-input[data-col="${colNumber}"]`).prop('disabled', true);
                    $(`td[data-col="${colNumber}"]`).addClass('skipped-column');
                    
                    $('#skippedCount').text(this.skippedColumns.length);
                    
                    // Save to server
                    $.ajax({
                        url: '/pauli-test/skip-column',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            session_id: this.config.sessionId,
                            column: colNumber
                        }
                    });
                    
                    PauliTest.toast(`Kolom ${colNumber} telah dilewati`, 'warning');
                } else {
                    alert('Kolom sudah dilewati sebelumnya!');
                }
            } else {
                alert(`Nomor kolom harus antara 1 dan ${this.config.totalColumns}`);
            }
        }
    }
    
    endTest() {
        if (!this.isTestActive) return;
        
        this.isTestActive = false;
        clearInterval(this.timerInterval);
        clearInterval(this.lineInterval);
        
        const loading = PauliTest.showLoading();
        
        $.ajax({
            url: '/pauli-test/end',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: this.config.sessionId,
                end_time: new Date().toISOString()
            },
            success: (response) => {
                PauliTest.hideLoading(loading);
                this.config.onTestEnd(response);
                window.location.href = '/pauli-test/result/' + this.config.sessionId;
            },
            error: (xhr) => {
                PauliTest.hideLoading(loading);
                PauliTest.toast('Gagal menyelesaikan tes', 'danger');
                console.error(xhr);
            }
        });
    }
    
    shouldShowLineMarker(row) {
        // Determine if current row should have line marker
        const currentRowMarker = Math.ceil(this.currentLine / (this.config.totalColumns / 5));
        return row === currentRowMarker && this.currentLine > 0;
    }
    
    playBeep() {
        try {
            const audio = new Audio('/sounds/beep.mp3');
            audio.play().catch(e => console.log('Audio play failed:', e));
        } catch (e) {
            console.log('Audio not supported');
        }
    }
    
    preventCopyPaste() {
        $(document).on('copy paste cut drag drop', (e) => {
            e.preventDefault();
            return false;
        });
        
        $(document).on('contextmenu', (e) => {
            e.preventDefault();
            return false;
        });
    }
    
    showInstructions() {
        const instructionHtml = `
            <div class="instruction-modal" id="instructionModal">
                <div class="instruction-content">
                    <h3><i class="fas fa-info-circle"></i> Petunjuk Pengerjaan Tes Pauli</h3>
                    <ul>
                        <li>Jumlahkan angka dari <strong>atas ke bawah</strong> (angka dengan angka di bawahnya)</li>
                        <li>Hasil penjumlahan ditulis di <strong>sebelah kanan</strong> antara kedua angka</li>
                        <li>Jika hasil penjumlahan <strong>puluhan</strong>, tulis <strong>satuannya saja</strong> 
                            <div class="example-box">
                                <span class="example-numbers">6 + 4 = 10 → tulis <strong>0</strong></span>
                            </div>
                        </li>
                        <li>Jika salah, <strong>tumpuk/timpa</strong> dengan angka yang benar (jangan dihapus)</li>
                        <li>Setiap aba-aba <strong>"GARIS"</strong> (setiap 3 menit), beri garis di bawah hasil penjumlahan</li>
                        <li>Jika <strong>terlewat satu kolom</strong>, biarkan kosong dan lanjut ke kolom berikutnya</li>
                        <li>Jika sampai akhir lembar, <strong>balik</strong> dan lanjutkan ke halaman belakang</li>
                        <li>Kerjakan <strong>secepat-cepatnya</strong> dan <strong>seteliti mungkin</strong></li>
                    </ul>
                    <div class="text-center mt-4">
                        <button class="btn btn-primary btn-lg" onclick="$('#instructionModal').remove();">
                            <i class="fas fa-play"></i> Saya Mengerti & Mulai Tes
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(instructionHtml);
    }
}

// Export for use
window.PauliTestCore = PauliTest;