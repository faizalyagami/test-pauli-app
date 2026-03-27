{{-- resources/views/pauli-test/partials/line-marker.blade.php --}}
<div class="line-marker-container">
    <div class="line-info">
        <i class="fas fa-arrow-down text-danger me-1"></i>
        <span>Garis ke-<span id="lineCount" class="fw-bold">0</span> / 20</span>
    </div>
    <div class="skip-info ms-3">
        <i class="fas fa-forward text-warning me-1"></i>
        <span>Kolom Dilewati: <span id="skippedCount" class="fw-bold">0</span></span>
    </div>
</div>

@push('styles')
<style>
    .line-marker-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        font-size: 13px;
    }

    .line-info,
    .skip-info {
        background: #f8f9fa;
        padding: 5px 12px;
        border-radius: 20px;
    }

    @media (max-width: 768px) {
        .line-marker-container {
            font-size: 11px;
            gap: 10px;
        }

        .line-info,
        .skip-info {
            padding: 3px 8px;
        }
    }
</style>