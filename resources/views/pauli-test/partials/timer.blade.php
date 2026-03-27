{{-- resources/views/pauli-test/partials/timer.blade.php --}}
<div class="timer-container">
    <div class="timer-box bg-white text-dark rounded p-3 shadow-sm">
        <div class="timer" id="timer" style="font-size: 48px; font-family: monospace; font-weight: bold;">00:00</div>
        <div class="timer-label small text-muted">Sisa Waktu</div>
    </div>
</div>

@push('styles')
<style>
    .timer-container {
        display: inline-block;
    }

    .timer-box {
        min-width: 150px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .timer {
        line-height: 1.2;
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

    @media (max-width: 768px) {
        .timer {
            font-size: 32px !important;
        }

        .timer-box {
            min-width: 100px;
        }
    }
</style>
</style>