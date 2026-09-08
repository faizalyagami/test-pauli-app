{{-- resources/views/pauli-test/result.blade.php --}}
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
                                        <th>Nomor Peserta</th>
                                        <td>{{ $session->applicant->participant_numb }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tgl. Lahir</th>
                                        <td>{{ $session->applicant->date_of_birth ? $session->applicant->date_of_birth->format('d F Y') : '-' }}
                                        </td>
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
                                        <th>Jenis Kelamin</th>
                                        <td>{{ $session->applicant->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
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
                                        <th width="35%">Durasi Pengerjaan</th>
                                        <td>
                                            @if ($session->start_time && $session->end_time)
                                                {{ $session->start_time->diffInMinutes($session->end_time) }} menit
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span
                                                class="badge bg-{{ $session->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Utama (Jumlah, Benar, Salah, Akurasi) -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white border-0">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Total Jawaban</h6>
                        <h2 class="mb-0">{{ number_format($statistics['total_answered'] ?? 0) }}</h2>
                        <small>soal dikerjakan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white border-0">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Benar</h6>
                        <h2 class="mb-0">{{ number_format($statistics['correct_count'] ?? 0) }}</h2>
                        <small>jawaban benar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-danger text-white border-0">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Salah</h6>
                        <h2 class="mb-0">{{ number_format($statistics['wrong_count'] ?? 0) }}</h2>
                        <small>jawaban salah</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-info text-white border-0">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Akurasi</h6>
                        <h2 class="mb-0">{{ number_format($statistics['accuracy'] ?? 0, 1) }}%</h2>
                        <small>tingkat ketepatan</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Pauli (Mean, Tinggi, Puncak, Penyimpangan, LQ) -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-calculator me-2 text-primary"></i> Statistik Pauli
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            // Hitung statistik dari data yang ada
                            $performanceData = $answersByLine->pluck('total')->toArray();
                            $totalPerformance = array_sum($performanceData);
                            $mean = count($performanceData) > 0 ? $totalPerformance / count($performanceData) : 0;
                            $highest = max($performanceData);
                            $lowest = min($performanceData);
                            $height = $highest - $lowest;

                            // Tempat Puncak (3 interval tertinggi)
                            $sortedData = $performanceData;
                            arsort($sortedData);
                            $topThree = array_slice($sortedData, 0, 3, true);
                            $peakPositions = array_keys($topThree);
                            $peakPositions = array_map(function ($pos) {
                                return $pos + 1;
                            }, $peakPositions);

                            // Penyimpangan (kolom 3-18)
                            $deviations = [];
                            for ($i = 2; $i <= 17 && $i < count($performanceData); $i++) {
                                if (isset($performanceData[$i]) && isset($performanceData[$i - 1])) {
                                    $deviations[] = abs($performanceData[$i] - $performanceData[$i - 1]);
                                }
                            }
                            $avgDeviation = count($deviations) > 0 ? array_sum($deviations) / count($deviations) : 0;
                            $deviationPercentage = $mean > 0 ? ($avgDeviation / $mean) * 100 : 0;

                            // LQ (Kualitas Kerja)
                            $totalCorrect = $answersByLine->sum('correct');
                            $lq = $totalPerformance > 0 ? ($totalCorrect / $totalPerformance) * 100 : 0;

                            // Keterangan LQ
                            if ($lq >= 85) {
                                $keterangan = 'Sangat Baik';
                            } elseif ($lq >= 70) {
                                $keterangan = 'Baik';
                            } elseif ($lq >= 55) {
                                $keterangan = 'Cukup';
                            } elseif ($lq >= 40) {
                                $keterangan = 'Kurang';
                            } else {
                                $keterangan = 'Sangat Kurang';
                            }
                        @endphp

                        <div class="row text-center">
                            <div class="col-md-2">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted">Mean</h6>
                                    <h3 class="text-primary">{{ number_format($mean, 2) }}</h3>
                                    <small>rata-rata</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted">Tinggi</h6>
                                    <h3 class="text-success">{{ number_format($height) }}</h3>
                                    <small>max - min</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted">Temp. Puncak</h6>
                                    <h3 class="text-warning">{{ implode(', ', $peakPositions) }}</h3>
                                    <small>interval ke-</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted">Penyimpangan</h6>
                                    <h3 class="text-info">{{ number_format($deviationPercentage, 2) }}%</h3>
                                    <small>fluktuasi</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted">LQ</h6>
                                    <h3 class="text-primary">{{ number_format($lq, 2) }}%</h3>
                                    <small>kualitas kerja</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="border rounded p-3 bg-light">
                                    <h6 class="text-muted">Keterangan</h6>
                                    <h3 class="text-{{ $lq >= 70 ? 'success' : ($lq >= 55 ? 'warning' : 'danger') }}">
                                        {{ $keterangan }}
                                    </h3>
                                    <small>LQ kategori</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Tambahan -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-warning text-white border-0">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Dikoreksi</h6>
                        <h2 class="mb-0">{{ number_format($statistics['revised_count'] ?? 0) }}</h2>
                        <small>jawaban diubah</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-secondary text-white border-0">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Kolom Terpenuhi</h6>
                        <h2 class="mb-0">{{ number_format($statistics['columns_fulfilled'] ?? 0) }} /
                            {{ $statistics['total_columns'] ?? $session->test->total_columns }}</h2>
                        <small>kolom yang terisi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-dark text-white border-0">
                    <div class="card-body text-center">
                        <h6 class="mb-2">Rata-rata Waktu</h6>
                        <h2 class="mb-0">{{ number_format($statistics['avg_time_per_answer'] ?? 0, 1) }} detik</h2>
                        <small>per jawaban</small>
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
                                        <th>Benar</th>
                                        <th>%</th>
                                        <th>Dikoreksi</th>
                                        <th>Akurasi</th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalAll = $statistics['total_answered'] ?? 0;
                                    @endphp
                                    @foreach ($answersByLine as $index => $line)
                                        @php
                                            $interval = $line->line_marker;
                                            $jumlah = $line->total;
                                            $salah = $line->total - $line->correct;
                                            $persenJumlah = ($jumlah / max($totalAll, 1)) * 100;
                                            $persenSalah = ($salah / max($totalAll, 1)) * 100;
                                            $persenBenar = ($line->correct / max($totalAll, 1)) * 100;
                                            $akurasiInterval = $jumlah > 0 ? ($line->correct / $jumlah) * 100 : 0;
                                            $trend = $line->correct > $line->total / 2 ? '↑ Baik' : '↓ Kurang';
                                            $trendColor =
                                                $line->correct > $line->total / 2 ? 'text-success' : 'text-danger';
                                        @endphp
                                        <tr class="text-center">
                                            <td><strong>{{ $interval }}</strong></td>
                                            <td>{{ number_format($jumlah) }}</td>
                                            <td class="text-danger">{{ number_format($salah) }}</td>
                                            <td>{{ number_format($persenSalah, 2) }}%</td>
                                            <td class="text-success">{{ number_format($line->correct) }}</td>
                                            <td>{{ number_format($persenBenar, 2) }}%</td>
                                            <td>{{ $line->revised ?? 0 }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px; width: 80px; margin: 0 auto;">
                                                    <div class="progress-bar bg-{{ $akurasiInterval >= 80 ? 'success' : ($akurasiInterval >= 60 ? 'warning' : 'danger') }}"
                                                        style="width: {{ $akurasiInterval }}%">
                                                        {{ number_format($akurasiInterval, 0) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="{{ $trendColor }}">{{ $trend }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr class="text-center fw-bold">
                                        <td>Total</td>
                                        <td>{{ number_format($statistics['total_answered'] ?? 0) }}</td>
                                        <td class="text-danger">{{ number_format($statistics['wrong_count'] ?? 0) }}</td>
                                        <td>{{ number_format(($statistics['wrong_count'] / max($statistics['total_answered'], 1)) * 100, 2) }}%
                                        </td>
                                        <td class="text-success">{{ number_format($statistics['correct_count'] ?? 0) }}
                                        </td>
                                        <td>{{ number_format($statistics['accuracy'] ?? 0, 2) }}%</td>
                                        <td>{{ number_format($statistics['revised_count'] ?? 0) }}</td>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analisis Psikologis -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-brain me-2 text-primary"></i> Analisis Psikologis
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6>Konsistensi Kerja</h6>
                                    <div class="progress mb-2" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $statistics['consistency'] < 20 ? 'success' : ($statistics['consistency'] < 35 ? 'warning' : 'danger') }}"
                                            style="width: {{ min(100, $statistics['consistency']) }}%">
                                            {{ number_format($statistics['consistency'], 1) }}
                                        </div>
                                    </div>
                                    <small>{{ $analysis['consistency'] ?? 'Data tidak tersedia' }}</small>
                                </div>
                                <div class="mb-3">
                                    <h6>Daya Tahan</h6>
                                    <div class="progress mb-2" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $statistics['endurance'] >= 80 ? 'success' : ($statistics['endurance'] >= 60 ? 'warning' : 'danger') }}"
                                            style="width: {{ min(100, $statistics['endurance']) }}%">
                                            {{ number_format($statistics['endurance'], 1) }}%
                                        </div>
                                    </div>
                                    <small>{{ $analysis['endurance'] ?? 'Data tidak tersedia' }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6>Kecepatan Kerja</h6>
                                    <div class="progress mb-2" style="height: 25px;">
                                        @php
                                            $speedScore = max(
                                                0,
                                                min(100, 100 - ($statistics['avg_time_per_answer'] / 10) * 100),
                                            );
                                        @endphp
                                        <div class="progress-bar bg-{{ $speedScore >= 70 ? 'success' : ($speedScore >= 50 ? 'warning' : 'danger') }}"
                                            style="width: {{ $speedScore }}%">
                                            {{ number_format($statistics['avg_time_per_answer'], 1) }} detik
                                        </div>
                                    </div>
                                    <small>{{ $analysis['speed'] ?? 'Data tidak tersedia' }}</small>
                                </div>
                                <div class="mb-3">
                                    <h6>Akurasi</h6>
                                    <div class="progress mb-2" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $statistics['accuracy'] >= 80 ? 'success' : ($statistics['accuracy'] >= 60 ? 'warning' : 'danger') }}"
                                            style="width: {{ $statistics['accuracy'] }}%">
                                            {{ number_format($statistics['accuracy'], 1) }}%
                                        </div>
                                    </div>
                                    <small>{{ $analysis['accuracy'] ?? 'Data tidak tersedia' }}</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="alert alert-info">
                            <strong><i class="fas fa-lightbulb me-2"></i> Rekomendasi:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($analysis['recommendations'] ?? [] as $recommendation)
                                    <li>{{ $recommendation }}</li>
                                @endforeach
                                @if (empty($analysis['recommendations']))
                                    <li>Pertahankan performa yang sudah baik dan terus tingkatkan.</li>
                                @endif
                            </ul>
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
                            <i class="fas fa-clipboard-list me-2 text-primary"></i> KOREKTOR (Distribusi Jawaban per
                            Interval)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($answersByLine as $index => $line)
                                @php
                                    $persentase = ($line->total / max($statistics['total_answered'], 1)) * 100;
                                    $color = $persentase >= 15 ? 'danger' : ($persentase >= 10 ? 'warning' : 'primary');
                                @endphp
                                <div class="col-md-2 col-sm-3 col-4 mb-2">
                                    <div class="border rounded p-2 text-center">
                                        <strong>Interval {{ $line->line_marker }}</strong>
                                        <div class="progress mt-1" style="height: 30px;">
                                            <div class="progress-bar bg-{{ $color }} d-flex align-items-center justify-content-center"
                                                style="width: {{ $persentase }}%">
                                                {{ number_format($persentase, 1) }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format($line->total) }} jawaban</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i> Cetak Hasil
                </button>
                <a href="{{ route('pauli-test.statistics', $session->id) }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-2"></i> Lihat Statistik Lengkap
                </a>
                <a href="{{ route('applicant.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-home me-2"></i> Kembali ke Dashboard
                </a>
                @if (auth()->user()->role === 'tester' || auth()->user()->role === 'admin')
                    <a href="{{ route('tester.sessions') }}" class="btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Sesi
                    </a>
                @endif
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            border-radius: 1rem;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .table th,
        .table td {
            vertical-align: middle;
            font-size: 13px;
        }

        .progress {
            border-radius: 20px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        @media print {

            .no-print,
            .btn,
            .navbar-top,
            .sidebar {
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
            var lineLabels = @json($answersByLine->pluck('line_marker'));
            var jumlahData = @json($answersByLine->pluck('total'));
            var benarData = @json($answersByLine->pluck('correct'));

            // Hitung nilai mean dan puncak
            var meanValue = jumlahData.reduce(function(a, b) {
                return a + b;
            }, 0) / jumlahData.length;
            var maxValue = Math.max.apply(null, jumlahData);
            var maxIndex = jumlahData.indexOf(maxValue);

            // Buat data untuk garis mean
            var meanLineData = jumlahData.map(function() {
                return meanValue;
            });

            // Buat data untuk titik puncak
            var peakData = jumlahData.map(function(val, index) {
                return index === maxIndex ? val : null;
            });

            new Chart(document.getElementById('workCurveChart'), {
                type: 'line',
                data: {
                    labels: lineLabels,
                    datasets: [{
                            label: 'Jumlah Jawaban',
                            data: jumlahData,
                            borderColor: '#2c3e50',
                            backgroundColor: 'rgba(44, 62, 80, 0.05)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.2,
                            pointBackgroundColor: '#2c3e50',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            order: 1
                        },
                        {
                            label: 'Jawaban Benar',
                            data: benarData,
                            borderColor: '#27ae60',
                            backgroundColor: 'rgba(39, 174, 96, 0.05)',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.2,
                            pointBackgroundColor: '#27ae60',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            order: 1
                        },
                        {
                            label: 'Garis Mean (Rata-rata)',
                            data: meanLineData,
                            borderColor: '#e74c3c',
                            borderWidth: 2,
                            borderDash: [6, 4],
                            fill: false,
                            pointRadius: 0,
                            order: 0
                        },
                        {
                            label: 'Puncak Tertinggi',
                            data: peakData,
                            borderColor: '#f39c12',
                            backgroundColor: 'rgba(243, 156, 18, 0.3)',
                            borderWidth: 0,
                            pointBackgroundColor: '#f39c12',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 10,
                            pointHoverRadius: 12,
                            order: 2
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
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                usePointStyle: true,
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.dataset.label || '';
                                    var value = context.raw;
                                    if (context.dataset.label === 'Garis Mean') {
                                        return label + ': ' + value.toFixed(1);
                                    }
                                    if (context.dataset.label === 'Puncak Tertinggi' && value !== null) {
                                        return label + ': ' + value + ' (Interval ' + (context.dataIndex + 1) + ')';
                                    }
                                    return label + ': ' + value;
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
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Interval Waktu (setiap 3 menit)',
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
