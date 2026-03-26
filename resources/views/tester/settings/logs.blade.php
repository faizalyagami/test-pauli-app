{{-- resources/views/tester/settings/logs.blade.php --}}
@extends('layouts.app')

@section('title', 'System Logs')

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
                                <i class="fas fa-history me-2"></i> System Logs
                            </h4>
                            <p class="mb-0 opacity-75">View application logs and system activity</p>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-list me-2 text-primary"></i> Laravel Logs
                    </h6>
                    <div>
                        <button onclick="clearLogs()" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash-alt me-1"></i> Clear Logs
                        </button>
                        <button onclick="refreshLogs()" class="btn btn-secondary btn-sm ms-2">
                            <i class="fas fa-sync me-1"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase text-muted">
                                    <th class="py-2 px-3" style="width: 80px;">Level</th>
                                    <th class="py-2 px-3">Message</th>
                                    <th class="py-2 px-3" style="width: 200px;">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody id="logTableBody">
                                @forelse($logs as $log)
                                @php
                                $logLevel = 'info';
                                $logClass = 'text-info';
                                if (str_contains($log, 'ERROR')) {
                                $logLevel = 'error';
                                $logClass = 'text-danger';
                                } elseif (str_contains($log, 'WARNING')) {
                                $logLevel = 'warning';
                                $logClass = 'text-warning';
                                } elseif (str_contains($log, 'DEBUG')) {
                                $logLevel = 'debug';
                                $logClass = 'text-secondary';
                                }
                                @endphp
                                <tr>
                                    <td class="px-3">
                                        <span class="badge bg-{{ $logLevel == 'error' ? 'danger' : ($logLevel == 'warning' ? 'warning' : 'info') }}">
                                            {{ strtoupper($logLevel) }}
                                        </span>
                                    </td>
                                    <td class="px-3">
                                        <code class="{{ $logClass }}" style="font-size: 12px;">
                                            {{ Str::limit($log, 200) }}
                                        </code>
                                    </td>
                                    <td class="px-3 text-muted small">
                                        @if(preg_match('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $log, $matches))
                                        {{ $matches[0] }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                        <p class="text-muted mb-0">No logs found</p>
                                        <small class="text-muted">System is running smoothly</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($logs) > 0)
                <div class="card-footer bg-white border-top">
                    <div class="text-center">
                        <small class="text-muted">Showing last {{ count($logs) }} log entries</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Log Info Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Log Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-primary">{{ count($logs) }}</h3>
                                <small class="text-muted">Total Entries</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-danger">
                                    {{ collect($logs)->filter(function($log) { return str_contains($log, 'ERROR'); })->count() }}
                                </h3>
                                <small class="text-muted">Errors</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-warning">
                                    {{ collect($logs)->filter(function($log) { return str_contains($log, 'WARNING'); })->count() }}
                                </h3>
                                <small class="text-muted">Warnings</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h3 class="text-info">
                                    {{ collect($logs)->filter(function($log) { return str_contains($log, 'INFO'); })->count() }}
                                </h3>
                                <small class="text-muted">Info</small>
                            </div>
                        </div>
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

    code {
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        word-break: break-all;
    }

    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }

    .table thead th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }
</style>

<script>
    function clearLogs() {
        if (confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
            fetch('{{ route("tester.settings.clear-logs") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Logs cleared successfully');
                        location.reload();
                    } else {
                        alert('Failed to clear logs');
                    }
                });
        }
    }

    function refreshLogs() {
        location.reload();
    }
</script>
@endsection