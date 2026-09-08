@extends('layouts.app')

@section('title', 'Certificate of Completion')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 80px;">
                            <h1 class="mt-3" style="color: #2c3e50;">CERTIFICATE OF COMPLETION</h1>
                            <hr class="w-25 mx-auto" style="height: 3px; background: #3498db;">
                        </div>

                        <div class="text-center mb-4">
                            <p class="lead">This certificate is proudly presented to</p>
                            <h2 class="display-4 text-primary">{{ $applicant->full_name }}</h2>
                            <p class="text-muted">Participant Number: <strong>{{ $applicant->participant_numb }}</strong>
                            </p>
                        </div>

                        <div class="text-center mb-4">
                            <p>For successfully completing the</p>
                            <h3>{{ $session->test->test_name }}</h3>
                            <div class="row justify-content-center mt-4">
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h5>Score</h5>
                                        <h2 class="text-success">{{ number_format($scoreData['correct']) }}</h2>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h5>Accuracy</h5>
                                        <h2 class="text-info">{{ number_format($scoreData['accuracy'], 1) }}%</h2>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h5>Date</h5>
                                        <h2 class="text-warning">{{ $session->end_time->format('d/m/Y') }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-6 text-center">
                                <hr style="width: 200px; margin: 0 auto;">
                                <p class="mt-2">Test Administrator</p>
                            </div>
                            <div class="col-6 text-center">
                                <hr style="width: 200px; margin: 0 auto;">
                                <p class="mt-2">Date Issued</p>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted small">
                                Certificate ID: {{ $session->id }}-{{ $applicant->participant_numb }}
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('applicant.certificate.download', $session) }}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i> Download PDF
                            </a>
                            <a href="{{ route('applicant.history') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to History
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print me-2"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .btn,
            .navbar-top,
            .sidebar {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd;
            }
        }
    </style>
@endsection
