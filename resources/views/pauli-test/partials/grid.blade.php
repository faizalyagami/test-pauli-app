{{-- resources/views/pauli-test/partials/grid.blade.php --}}
<div class="table-responsive" id="pauliGridContainer">
    <table class="pauli-grid" id="pauliGrid">
        <thead>
            <tr>
                @for($col = 1; $col <= $test->total_columns; $col++)
                    <th>Kolom {{ $col }}</th>
                    @endfor
            </tr>
        </thead>
        <tbody id="pauliGridBody">
            {{-- Grid akan di-generate dengan JavaScript --}}
        </tbody>
    </table>
</div>

@push('styles')
<style>
    .pauli-grid {
        font-family: 'Courier New', monospace;
        border-collapse: collapse;
        width: 100%;
        margin: 0;
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
        transition: background-color 0.2s;
    }

    .grid-cell {
        position: relative;
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

    .answer-area {
        margin: 8px 0;
        position: relative;
    }

    .answer-input {
        width: 55px;
        text-align: center;
        border: 2px solid #ddd;
        border-radius: 5px;
        padding: 5px;
        font-size: 16px;
        font-family: monospace;
        transition: all 0.2s;
    }

    .answer-input:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
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
            font-size: 12px;
            padding: 5px;
        }

        .pauli-grid td {
            min-width: 60px;
            padding: 4px;
        }

        .answer-input {
            width: 40px;
            font-size: 12px;
            padding: 3px;
        }

        .top-number,
        .bottom-number {
            font-size: 12px;
        }
    }
</style>