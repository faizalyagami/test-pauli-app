{{-- resources/views/applicant/certificate.blade.php --}}
@extends('layouts.print')

@section('title', 'Certificate of Completion')

@section('print-content')
<div class="certificate-container" style="text-align: center; padding: 50px;">
    <div class="certificate-border" style="border: 10px solid #3498db; padding: 30px;">
        <div class="certificate-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 80px; margin-bottom: 20px;">
            <h1 style="color: #2c3e50; font-size: 48px; margin-bottom: 20px;">CERTIFICATE OF COMPLETION</h1>
            <p style="font-size: 18px; color: #7f8c8d;">This certificate is proudly presented to</p>
        </div>

        <div class="certificate-body">
            <h2 style="font-size: 36px; color: #3498db; margin: 30px 0;">
                {{ $applicant->full_name }}
            </h2>
            <p style="font-size: 16px;">Participant Number: <strong>{{ $applicant->participant_numb }}</strong></p>

            <div style="margin: 30px 0;">
                <p>For successfully completing the</p>
                <h3 style="color: #2c3e50;">{{ $session->test->test_name }}</h3>
                <p>with a score of <strong>{{ number_format($scoreData['correct']) }}</strong> points</p>
                <p>and accuracy of <strong>{{ number_format($scoreData['accuracy'], 1) }}%</strong></p>
            </div>

            <div style="margin: 40px 0;">
                <p>Date of Completion: <strong>{{ $session->end_time->format('d F Y') }}</strong></p>
                <p>Test Duration: <strong>{{ $session->start_time->diffInMinutes($session->end_time) }} minutes</strong></p>
            </div>
        </div>

        <div class="certificate-footer" style="margin-top: 50px;">
            <div style="display: flex; justify-content: space-between;">
                <div style="text-align: left;">
                    <hr style="width: 200px; margin-bottom: 5px;">
                    <p>Test Administrator</p>
                </div>
                <div style="text-align: right;">
                    <hr style="width: 200px; margin-bottom: 5px;">
                    <p>Date Issued</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <p class="text-muted" style="font-size: 12px;">
                Certificate ID: {{ $session->id }}-{{ $applicant->participant_numb }}
            </p>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .certificate-container {
            width: 100%;
            margin: 0;
            padding: 20px;
        }

        .certificate-border {
            border: 10px solid #3498db !important;
            page-break-inside: avoid;
        }
    }
</style>
@endsection