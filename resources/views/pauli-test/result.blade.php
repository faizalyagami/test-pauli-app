{{-- resources/views/pauli-test/result.blade.php --}}
@extends('layouts.app')

@section('title', 'Hasil Tes Pauli - ' . $session->applicant->full_name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5>Informasi Peserta</h5>
                <hr>
                <p><strong>Nama:</strong> {{ $session->applicant->full_name }}</p>
                <p><strong>No. Peserta:</strong> {{ $session->applicant->participant_numb }}</p>
                <p><strong>Tanggal Tes:</strong> {{ \Carbon\Carbon::parse($session->start_time)->format('d/m/Y H:i') }}</p>
                <p><strong>Durasi:</strong> {{ $session->start_time->diffInMinutes($session->end_time) }} menit</p>
                <p><strong>Kolom Dilewati:</strong> {{ $session->skipped_columns }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5>Skor Akhir</h5>
                <div class="display-1 text-primary">{{ number_format($scoreData['correct']) }}</div>
                <p>Jawaban Benar</p>
                <hr>
                <p>Total Dikerjakan: {{ number_format($scoreData['total_attempted']) }}</p>
                <p>Akurasi: {{ number_format($scoreData['accuracy'], 1) }}%</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Akurasi</h5>
                <canvas id="accuracyChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Performa per Interval (Setiap 3 Menit)</h5>
            </div>
            <div class="card-body">
                <canvas id="lineChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Performa per Kolom</h5>
            </div>
            <div class="card-body">
                <canvas id="columnChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Analisis Psikologis</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line"></i> Kestabilan Kerja</h6>
                        @php
                        $lineTotals = $answersByLine->pluck('total')->toArray();
                        $stability = count($lineTotals) > 0 ? (max($lineTotals) - min($lineTotals)) / array_sum($lineTotals) * 100 : 0;
                        @endphp
                        <div class="progress mb-2">
                            <div class="progress-bar bg-{{ $stability < 20 ? 'success' : ($stability < 40 ? 'warning' : 'danger') }}"
                                style="width: {{ min(100, $stability) }}%">
                                {{ number_format($stability, 1) }}%
                            </div>
                        </div>
                        @if($stability < 20)
                            <div class="alert alert-success">Stabil - Konsisten dalam pengerjaan
                    </div>
                    @elseif($stability < 40)
                        <div class="alert alert-warning">Cukup stabil - Ada sedikit fluktuasi
                </div>
                @else
                <div class="alert alert-danger">Tidak stabil - Performa sangat berfluktuasi</div>
                @endif
            </div>

            <div class="col-md-6">
                <h6><i class="fas fa-heartbeat"></i> Ketahanan (Daya Tahan)</h6>
                @php
                $firstHalf = array_slice($lineTotals, 0, floor(count($lineTotals)/2));
                $secondHalf = array_slice($lineTotals, floor(count($lineTotals)/2));
                $endurance = count($secondHalf) > 0 ? (array_sum($secondHalf) / count($secondHalf)) / (array_sum($firstHalf) / count($firstHalf)) * 100 : 100;
                @endphp
                <div class="progress mb-2">
                    <div class="progress-bar bg-{{ $endurance > 90 ? 'success' : ($endurance > 70 ? 'warning' : 'danger') }}"
                        style="width: {{ min(100, $endurance) }}%">
                        {{ number_format($endurance, 1) }}%
                    </div>
                </div>
                @if($endurance > 90)
                <div class="alert alert-success">Daya tahan baik - Performa terjaga hingga akhir</div>
                @elseif($endurance > 70)
                <div class="alert alert-warning">Daya tahan cukup - Ada penurunan performa</div>
                @else
                <div class="alert alert-danger">Daya tahan rendah - Penurunan performa signifikan</div>
                @endif
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <h6><i class="fas fa-balance-scale"></i> Kecepatan vs Ketelitian</h6>
                @if($scoreData['accuracy'] > 85 && $scoreData['total_attempted'] > 200)
                <div class="alert alert-success">Kecepatan tinggi & Ketelitian tinggi - Performa optimal</div>
                @elseif($scoreData['accuracy'] > 85 && $scoreData['total_attempted'] <= 200)
                    <div class="alert alert-warning">Kecepatan rendah tapi teliti - Perlu peningkatan kecepatan
            </div>
            @elseif($scoreData['accuracy'] <= 85 && $scoreData['total_attempted']> 200)
                <div class="alert alert-warning">Kecepatan tinggi tapi kurang teliti - Perlu peningkatan akurasi</div>
                @else
                <div class="alert alert-danger">Kecepatan rendah & kurang teliti - Perlu latihan intensif</div>
                @endif
        </div>

        <div class="col-md-6">
            <h6><i class="fas fa-graduation-cap"></i> Rekomendasi</h6>
            @php
            if ($scoreData['correct'] > 250 && $scoreData['accuracy'] > 85) {
            $recommendation = 'Sangat baik. Kandidat memiliki konsentrasi tinggi, daya tahan baik, dan ketelitian yang sangat memadai.';
            } elseif ($scoreData['correct'] > 200 && $scoreData['accuracy'] > 75) {
            $recommendation = 'Baik. Kandidat memiliki kemampuan yang cukup untuk tugas yang membutuhkan konsentrasi dan ketelitian.';
            } elseif ($scoreData['correct'] > 150 && $scoreData['accuracy'] > 70) {
            $recommendation = 'Cukup. Perlu pendampingan dan latihan lebih lanjut untuk meningkatkan konsentrasi.';
            } else {
            $recommendation = 'Kurang. Kandidat perlu evaluasi lebih lanjut dan pelatihan intensif untuk meningkatkan kemampuan konsentrasi.';
            }
            @endphp
            <div class="alert alert-info">{{ $recommendation }}</div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<div class="row mt-3">
    <div class="col text-center">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Hasil
        </button>
        <a href="{{ route('tester.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Accuracy Chart
    new Chart(document.getElementById('accuracyChart'), {
        type: 'doughnut',
        data: {
            labels: ['Benar ({{ number_format($scoreData["correct"]) }})', 'Salah ({{ number_format($scoreData["total_attempted"] - $scoreData["correct"]) }})'],
            datasets: [{
                data: [{
                    {
                        $scoreData["correct"]
                    }
                }, {
                    {
                        $scoreData["total_attempted"] - $scoreData["correct"]
                    }
                }],
                backgroundColor: ['#27ae60', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Line Chart
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: @json($answersByLine - > pluck('line_marker') - > map(function($item) {
                return "Garis $item";
            })),
            datasets: [{
                label: 'Jumlah Jawaban',
                data: @json($answersByLine - > pluck('total')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Jawaban Benar',
                data: @json($answersByLine - > pluck('correct')),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Column Chart
    new Chart(document.getElementById('columnChart'), {
        type: 'bar',
        data: {
            labels: @json($answersByColumn - > pluck('column_number')),
            datasets: [{
                label: 'Jumlah Jawaban',
                data: @json($answersByColumn - > pluck('total')),
                backgroundColor: 'rgba(153, 102, 255, 0.5)',
                borderColor: 'rgb(153, 102, 255)',
                borderWidth: 1
            }, {
                label: 'Jawaban Benar',
                data: @json($answersByColumn - > pluck('correct')),
                backgroundColor: 'rgba(255, 159, 64, 0.5)',
                borderColor: 'rgb(255, 159, 64)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush