@extends('layouts.app')

@section('title', 'Grafik Kerja - ' . $session->applicant->full_name)

@section('content')
    @php
        $grafikI = $chartData['grafikI'] ?? [];

        $grafikII = $chartData['grafikII'] ?? [];

        $meanValue = $chartData['mean'] ?? 0;

        $height = $chartData['height'] ?? 0;

        $peakPositions = $chartData['peakPositions'] ?? [];

        $totalAnswered = $chartData['totalPerformance'] ?? 0;

        $wrongCount = $chartData['totalWrong'] ?? 0;

        $revisedCount = $chartData['revisedCount'] ?? 0;

        $wrongPercentage = $totalAnswered > 0 ? ($wrongCount / $totalAnswered) * 100 : 0;

        $revisedPercentage = $totalAnswered > 0 ? ($revisedCount / $totalAnswered) * 100 : 0;

        $deviationPercentage = $chartData['deviationPercentage'] ?? 0;
        $deviationSum = round(($deviationPercentage * $totalAnswered) / 100);

        $totalIntervals = count($grafikI);

        $baseRow = [];
        $fluktuasiRow = [];

        foreach ($grafikI as $val) {
            $base = floor($val / 50) * 50;
            $baseRow[] = $base;
            $fluktuasiRow[] = $val - $base;
        }
    @endphp

    <div class="container-fluid px-3 py-2 bg-white text-dark" style="font-family: Arial, sans-serif;">
        <!-- Top Header -->
        <div class="row border-bottom pb-2 mb-3 align-items-center">
            <div class="col-8">
                <h3 class="fw-bold mb-0 text-uppercase tracking-wide">GRAFIK KERJA</h3>
            </div>
            <div class="col-4 text-end">
                <small class="text-muted fw-bold">PAULI TEST REPORT</small>
            </div>
        </div>

        <!-- Informasi Peserta & Hasil Utama -->
        <div class="row mb-3">
            <!-- Kolom Data Diri -->
            <div class="col-md-6 border-end">
                <table class="table table-borderless table-sm mb-0 compact-info-table">
                    <tr>
                        <th width="25%">Nama</th>
                        <td width="2%">:</td>
                        <td><strong>{{ strtoupper($session->applicant->full_name) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nomor Peserta</th>
                        <td>:</td>
                        <td>{{ $session->applicant->participant_numb }}</td>
                    </tr>
                    <tr>
                        <th>Tgl. Lahir</th>
                        <td>:</td>
                        <td>
                            {{ $session->applicant->date_of_birth ? \Carbon\Carbon::parse($session->applicant->date_of_birth)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Pendidikan</th>
                        <td>:</td>
                        <td>{{ $session->applicant->education_background ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tgl. Pemeriksaan</th>
                        <td>:</td>
                        <td>{{ $session->created_at ? $session->created_at->format('d F Y') : date('d F Y') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Kolom Summary Psikometri -->
            <div class="col-md-6 ps-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold">
                        Ketelitian: {{ $analysis['accuracy'] ?? '-' }}
                    </span>
                    <span class="badge bg-light text-dark border">
                        Daya Tahan: {{ $statistics['endurance'] ?? 0 }}%
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center align-middle mb-0 summary-table">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 14%;">$\overline{x}$</th>
                                <th style="width: 14%;">JUMLAH</th>
                                <th style="width: 14%;">SALAH</th>
                                <th style="width: 16%;">DIBETULKAN</th>
                                <th style="width: 16%;">PENYIMPANGAN</th>
                                <th style="width: 13%;">TINGGI</th>
                                <th style="width: 13%;">PUNCAK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">{{ number_format($meanValue, 2) }}</td>
                                <td class="fw-bold">{{ number_format($totalAnswered) }}</td>
                                <td>
                                    {{ $wrongCount }}<br>
                                    <small class="text-muted">({{ number_format($wrongPercentage, 2) }}%)</small>
                                </td>
                                <td>
                                    {{ $revisedCount }}<br>
                                    <small class="text-muted">({{ number_format($revisedPercentage, 2) }}%)</small>
                                </td>
                                <td>
                                    {{ round($deviationSum) }}<br>
                                    <small class="text-muted">{{ number_format($deviationPercentage, 2) }}%</small>
                                </td>
                                <td class="fw-bold text-primary">{{ $height }}</td>
                                <td class="fw-bold text-success">
                                    {{ !empty($peakPositions) ? implode(', ', $peakPositions) : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Canvas Grafik Kerja -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border rounded-0 shadow-none">
                    <div class="card-body p-2">
                        <div class="chart-container" style="position: relative; height: 380px; width: 100%;">
                            <canvas id="pauliChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabulasi Data Korektor (Tabel Interval) -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center align-middle korektor-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="90px">Interval</th>
                                @for ($i = 1; $i <= $totalIntervals; $i++)
                                    <th>{{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris 1: Grafik II (Garis Perata Antar Titik) -->
                            <tr>
                                <td class="fw-bold bg-light">Grafik II</td>
                                @foreach ($grafikII as $val)
                                    <td class="text-secondary small">{{ $val }}</td>
                                @endforeach
                                <td class="text-muted">-</td>
                            </tr>
                            <!-- Baris 2: Base / Sumbu Dasar -->
                            <tr>
                                <td class="fw-bold bg-light">Base</td>
                                @foreach ($baseRow as $val)
                                    <td class="small text-muted">{{ $val }}</td>
                                @endforeach
                            </tr>
                            <!-- Baris 3: Fluktuasi / Selisih Point -->
                            <tr>
                                <td class="fw-bold bg-light">Fluktuasi</td>
                                @foreach ($fluktuasiRow as $val)
                                    <td>{{ $val }}</td>
                                @endforeach
                            </tr>
                            <!-- Baris 4: JUMLAH (Grafik I) -->
                            <tr class="fw-bold table-primary">
                                <td class="bg-light text-dark">JUMLAH</td>
                                @foreach ($grafikI as $val)
                                    <td class="text-dark">{{ $val }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer Action -->
        <div class="row pt-2 align-items-center">
            <div class="col-md-6">
                <span class="fw-bold me-2"><i class="fas fa-chart-line me-1"></i> Konsistensi:</span>
                <span class="badge bg-secondary">{{ $analysis['consistency'] ?? 'N/A' }}</span>
            </div>
            <div class="col-md-6 text-end no-print">
                <button onclick="window.print()" class="btn btn-sm btn-dark me-1">
                    <i class="fas fa-print me-1"></i> Cetak PDF
                </button>
                <a href="{{ route('pauli-test.result', $session->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Hasil
                </a>
            </div>
        </div>
    </div>

    <style>
        .compact-info-table th,
        .compact-info-table td {
            padding: 3px 4px;
            font-size: 12px;
        }

        .summary-table th,
        .summary-table td {
            padding: 4px 2px;
            font-size: 10px;
        }

        .korektor-table th,
        .korektor-table td {
            padding: 4px 1px;
            font-size: 11px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: #fff !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .card {
                border: none !important;
            }
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const grafikI = @json($grafikI);
                const grafikIIValues = @json($grafikII);
                const meanValue = {{ $meanValue }};
                const totalIntervals = {{ $totalIntervals }};

                const labels = Array.from({
                    length: totalIntervals
                }, (_, i) => i + 1);

                // Map Grafik II data onto middle points for display
                const grafikIIData = labels.map((_, index) => {
                    return index < grafikIIValues.length ? grafikIIValues[index] : null;
                });

                // Dynamic scale min and max calculation
                const minData = Math.min(...grafikI);
                const maxData = Math.max(...grafikI);
                const yMin = Math.max(0, Math.floor((minData - 10) / 10) * 10);
                const yMax = Math.ceil((maxData + 10) / 10) * 10;

                console.log('Grafik I:', grafikI);
                console.log('Grafik II:', grafikIIValues);

                const ctx = document.getElementById('pauliChart').getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Grafik I (Hasil Kerja Asli)',
                                data: grafikI,
                                borderColor: '#000000',
                                backgroundColor: '#000000',
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                tension: 0,
                                order: 1
                            },
                            {
                                label: 'Grafik II (Garis Perata)',
                                data: grafikIIData,
                                borderColor: '#dc3545',
                                backgroundColor: '#dc3545',
                                borderWidth: 1.5,
                                borderDash: [4, 4],
                                pointRadius: 3,
                                pointStyle: 'rectRot',
                                spanGaps: true,
                                order: 2
                            },
                            {
                                label: 'Garis Rata-Rata (x̄)',
                                data: Array(totalIntervals).fill(meanValue),
                                borderColor: '#0d6efd',
                                borderWidth: 1.5,
                                borderDash: [2, 2],
                                pointRadius: 0,
                                fill: false,
                                order: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                min: yMin,
                                max: yMax,
                                ticks: {
                                    stepSize: 10,
                                    font: {
                                        size: 10
                                    }
                                },
                                grid: {
                                    color: '#e5e5e5'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10,
                                        weight: 'bold'
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
