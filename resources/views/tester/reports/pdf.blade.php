{{-- resources/views/tester/reports/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Test Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #333;
        }

        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Pauli Test System - Test Report</h1>
        <p>Generated on: {{ now()->format('d F Y H:i:s') }}</p>
        @if(request('start_date') || request('end_date'))
        <p>Period: {{ request('start_date') ?: 'All time' }} - {{ request('end_date') ?: 'Present' }}</p>
        @endif
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <table style="width: auto;">
            <tr>
                <th>Total Tests</th>
                <td>{{ number_format($summary['total_sessions']) }}</td>
            </tr>
            <tr>
                <th>Total Participants</th>
                <td>{{ number_format($summary['total_participants']) }}</td>
            </tr>
            <tr>
                <th>Average Score</th>
                <td>{{ number_format($summary['average_score'], 0) }}</td>
            </tr>
        </table>
    </div>

    <h3>Detailed Results</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Participant</th>
                <th>Test</th>
                <th>Score</th>
                <th>Time (min)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr>
                <td>{{ $session->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $session->applicant->full_name }} ({{ $session->applicant->participant_numb }})</td>
                <td>{{ $session->test->test_name }}</td>
                <td>{{ number_format($session->score) }}</td>
                <td>
                    @if($session->start_time && $session->end_time)
                    {{ $session->start_time->diffInMinutes($session->end_time) }}
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Pauli Test System - All rights reserved</p>
    </div>
</body>

</html>