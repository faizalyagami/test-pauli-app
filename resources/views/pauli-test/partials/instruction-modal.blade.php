{{-- resources/views/pauli-test/partials/instruction-modal.blade.php --}}
<div id="instructionModal" class="instruction-modal">
    <div class="instruction-content">
        <div class="text-center mb-3">
            <i class="fas fa-info-circle fa-4x text-primary"></i>
        </div>
        <h3 class="text-center mb-3">Petunjuk Pengerjaan Tes Pauli</h3>

        <ul class="mt-3">
            <li>Jumlahkan angka dari <strong>atas ke bawah</strong> (angka dengan angka di bawahnya)</li>
            <li>Hasil penjumlahan ditulis di <strong>sebelah kanan</strong> antara kedua angka</li>
            <li>Jika hasil penjumlahan <strong>puluhan</strong>, tulis <strong>satuannya saja</strong>
                <div class="example-box mt-2">
                    <span class="example-numbers">6 + 4 = 10 → tulis <strong class="text-danger">0</strong></span>
                </div>
            </li>
            <li>Jika salah, <strong>tumpuk/timpa</strong> dengan angka yang benar (jangan dihapus)</li>
            <li>Setiap aba-aba <strong>"GARIS"</strong> (setiap 3 menit), beri garis di bawah hasil penjumlahan</li>
            <li>Jika <strong>terlewat satu kolom</strong>, biarkan kosong dan lanjut ke kolom berikutnya</li>
            <li>Jika sampai akhir lembar, <strong>balik</strong> dan lanjutkan ke halaman belakang</li>
            <li>Kerjakan <strong>secepat-cepatnya</strong> dan <strong>seteliti mungkin</strong></li>
        </ul>

        <div class="example-box bg-light p-3 rounded mb-3">
            <strong>Contoh:</strong><br>
            <div class="row mt-2">
                <div class="col-6">
                    <div class="border p-2 rounded">
                        <span class="display-6">3 + 5 = 8</span><br>
                        <span class="text-success">Tulis angka 8</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border p-2 rounded">
                        <span class="display-6">6 + 4 = 10</span><br>
                        <span class="text-warning">Tulis angka 0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-primary btn-lg px-4" id="startTestBtn">
                <i class="fas fa-play me-2"></i> Saya Mengerti & Mulai Tes
            </button>
        </div>
    </div>
</div>

<style>
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
        border-radius: 15px;
        max-width: 600px;
        max-height: 85vh;
        overflow-y: auto;
    }

    .instruction-content ul {
        padding-left: 20px;
    }

    .instruction-content li {
        margin: 12px 0;
        line-height: 1.5;
    }

    .example-box {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
    }

    .example-numbers {
        font-size: 18px;
        font-family: monospace;
        font-weight: bold;
    }

    @media (max-width: 576px) {
        .instruction-content {
            padding: 20px;
            margin: 15px;
        }

        .instruction-content li {
            font-size: 13px;
        }
    }
</style>