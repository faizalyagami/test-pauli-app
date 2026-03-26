{{-- resources/views/pauli-test/partials/instruction-modal.blade.php --}}
<div class="instruction-modal" id="instructionModal">
    <div class="instruction-content">
        <h3><i class="fas fa-info-circle text-primary"></i> Petunjuk Pengerjaan Tes Pauli</h3>

        <ul class="mt-3">
            <li>Jumlahkan angka dari <strong>atas ke bawah</strong> (angka dengan angka di bawahnya)</li>
            <li>Hasil penjumlahan ditulis di <strong>sebelah kanan</strong> antara kedua angka</li>
            <li>Jika hasil penjumlahan <strong>puluhan</strong>, tulis <strong>satuannya saja</strong></li>
            <li>Jika salah, <strong>tumpuk/timpa</strong> dengan angka yang benar (jangan dihapus)</li>
            <li>Setiap aba-aba <strong>"GARIS"</strong> (setiap 3 menit), beri garis di bawah hasil penjumlahan</li>
            <li>Jika <strong>terlewat satu kolom</strong>, biarkan kosong dan lanjut ke kolom berikutnya</li>
            <li>Kerjakan <strong>secepat-cepatnya</strong> dan <strong>seteliti mungkin</strong></li>
        </ul>

        <div class="example-box bg-light p-3 rounded mb-3">
            <strong>Contoh:</strong><br>
            <span class="display-6">3 + 5 = 8</span><br>
            <span class="text-success">Tulis angka 8 di sebelah kanan</span><br>
            <span class="display-6 mt-2">6 + 4 = 10</span><br>
            <span class="text-warning">Tulis angka 0 (satuannya saja)</span>
        </div>

        <div class="text-center">
            <button class="btn btn-primary btn-lg" onclick="$('#instructionModal').remove();">
                <i class="fas fa-play"></i> Saya Mengerti & Mulai Tes
            </button>
        </div>
    </div>
</div>