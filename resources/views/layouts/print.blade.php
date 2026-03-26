<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Pauli Test Result')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .result-table th,
        .result-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .result-table th {
            background-color: #f2f2f2;
        }

        .score-box {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            border: 2px solid #333;
            border-radius: 5px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
    @stack('print-styles')
</head>

<body>
    @yield('print-content')

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Cetak</button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>
</body>

</html>