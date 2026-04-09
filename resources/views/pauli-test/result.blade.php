@extends('layouts.app')

@section('title', 'Hasil Tes Pauli - ' . $session->applicant->full_name)

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="fas fa-chart-line me-2"></i> Grafik Kerja Pauli Test
                            </h4>
                            <p class="mb-0 opacity-75">Hasil analisis tes Pauli - Kurva kerja dan performa</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-bar fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Peserta -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-user me-2 text-primary"></i> Data Peserta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="35%">Nama</th>
                                    <td><strong>{{ $session->applicant->full_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Nomor</th>
                                    <td>{{ $session->applicant->participant_numb }}</td>
                                </tr>
                                <tr>
                                    <th>Tgl. Lahir</th>
                                    <td>{{ $session->applicant->date_of_birth ? $session->applicant->date_of_birth->format('d F Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="35%">Pendidikan</th>
                                    <td>{{ $session->applicant->education_background ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Sk. Bangsa</th>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <th>Tgl. Pemeriksaan</th>
                                    <td>{{ $session->created_at->format('d F Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="35%">LQ</th>
                                    <td><strong class="text-primary">{{ number_format($scoreData['accuracy'], 2) }}%</strong></td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $this->getKeterangan($scoreData['accuracy']) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white border-0">
                <div class="card-body text-center">
                    <h6 class="mb-2">Jumlah</h6>
                    <h2 class="mb-0">{{ number_format($scoreData['total_attempted']) }}</h2>
                    <small>total jawaban</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-danger text-white border-0">
                <div class="card-body text-center">
                    <h6 class="mb-2">Salah</h6>
                    <h2 class="mb-0">{{ number_format($scoreData['total_attempted'] - $scoreData['correct']) }}</h2>
                    <small>jawaban salah</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white border-0">
                <div class="card-body text-center">
                    <h6 class="mb-2">Dibetulkan</h6>
                    <h2 class="mb-0">{{ number_format($scoreData['correct']) }}</h2>
                    <small>jawaban benar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white border-0">
                <div class="card-body text-center">
                    <h6 class="mb-2">Tinggi</h6>
                    <h2 class="mb-0">{{ number_format($maxLineScore ?? 0) }}</h2>
                    <small>puncak tertinggi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Kerja -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line me-2 text-primary"></i> Grafik Kerja Pauli Test
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="workCurveChart" style="height: 400px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Per Interval -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-table me-2 text-primary"></i> Statistik Per Interval (Setiap 3 Menit)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>Interval</th>
                                    <th>Jumlah</th>
                                    <th>Salah</th>
                                    <th>%</th>
                                    <th>Dibetulkan</th>
                                    <th>%</th>
                                    <th>Penyimpangan</th>
                                    <th>%</th>
                                    <th>Tinggi</th>
                                    <th>Temp. Punca</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAll = $scoreData['total_attempted'];
                                    $totalWrong = $scoreData['total_attempted'] - $scoreData['correct'];
                                @endphp
                                @foreach($answersByLine as $index => $line)
                                @php
                                    $interval = $line->line_marker;
                                    $jumlah = $line->total;
                                    $salah = $line->total - $line->correct;
                                    $persenJumlah = ($jumlah / max($totalAll, 1)) * 100;
                                    $persenSalah = ($salah / max($totalAll, 1)) * 100;
                                    $persenBenar = ($line->correct / max($totalAll, 1)) * 100;
                                    $penyimpangan = abs($persenJumlah - ($persenJumlah / 2));
                                @endphp
                                <tr class="text-center">
                                    <td><strong>{{ $interval }}</strong></td>
                                    <td>{{ number_format($jumlah) }}</td>
                                    <td class="text-danger">{{ number_format($salah) }}</td>
                                    <td>{{ number_format($persenSalah, 2) }}%</td>
                                    <td class="text-success">{{ number_format($line->correct) }}</td>
                                    <td>{{ number_format($persenBenar, 2) }}%</td>
                                    <td>{{ number_format($penyimpangan, 2) }}</td>
                                    <td>{{ number_format(($penyimpangan / max($persenJumlah, 1)) * 100, 2) }}%</td>
                                    <td>{{ $line->correct > ($line->total / 2) ? '↑' : '↓' }}</td>
                                    <td>{{ $line->line_marker }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr class="text-center fw-bold">
                                    <td>Total</td>
                                    <td>{{ number_format($totalAll) }}</td>
                                    <td class="text-danger">{{ number_format($totalWrong) }}</td>
                                    <td>{{ number_format(($totalWrong / max($totalAll, 1)) * 100, 2) }}%</td>
                                    <td class="text-success">{{ number_format($scoreData['correct']) }}</td>
                                    <td>{{ number_format($scoreData['accuracy'], 2) }}%</td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KOREKTOR Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i> KOREKTOR
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($answersByLine as $index => $line)
                        @php
                            $persentase = ($line->total / max($totalAll, 1)) * 100;
                        @endphp
                        <div class="col-md-2 col-sm-3 col-4 mb-2">
                            <div class="border rounded p-2 text-center">
                                <strong>{{ $line->line_marker }}</strong>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $persentase }}%"></div>
                                </div>
                                <small>{{ number_format($persentase, 2) }}%</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card {
        transition: transform 0.2s ease;
        cursor: pointer;
        border-radius: 1rem;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .table th, .table td {
        vertical-align: middle;
        font-size: 13px;
    }
    
    .progress {
        border-radius: 10px;
    }

    @media print {
    .no-print, .btn, .navbar-top, .sidebar {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    
    .card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    
    .table {
        font-size: 10px;
    }
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk grafik kerja
    const lineLabels = @json($answersByLine->pluck('line_marker'));
    const jumlahData = @json($answersByLine->pluck('total'));
    const benarData = @json($answersByLine->pluck('correct'));
    
    // Hitung data untuk grafik
    const totalMax = Math.max(...jumlahData);
    
    new Chart(document.getElementById('workCurveChart'), {
        type: 'line',
        data: {
            labels: lineLabels,
            datasets: [
                {
                    label: 'Jumlah Jawaban',
                    data: jumlahData,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Jawaban Benar',
                    data: benarData,
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#27ae60',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { size: 12, weight: '500' },
                        usePointStyle: true,
                        boxWidth: 10
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw;
                            let total = context.dataset.label === 'Jumlah Jawaban' ? 
                                {{ $scoreData['total_attempted'] }} : 
                                {{ $scoreData['correct'] }};
                            let percentage = (value / total * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Jawaban',
                        font: { size: 12, weight: 'bold' }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    max: {{ max($answersByLine->pluck('total')->toArray() + [100]) + 20 }}
                },
                x: {
                    title: {
                        display: true,
                        text: 'Interval Waktu (setiap 3 menit)',
                        font: { size: 12, weight: 'bold' }
                    },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush