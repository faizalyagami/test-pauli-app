{{-- resources/views/admin/logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'System Logs')

@section('content')
<div class="container-fluid px-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold"><i class="fas fa-history me-2"></i> System Logs</h4>
                            <p class="mb-0 opacity-75">View application logs and system activity</p>
                        </div>
                        <button onclick="clearLogs()" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i> Clear Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 px-3">Level</th>
                                    <th class="py-2 px-3">Message</th>
                                    <th class="py-2 px-3">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                @php
                                $logLevel = 'info';
                                $logClass = 'text-info';
                                if (str_contains($log, 'ERROR')) { $logLevel = 'error'; $logClass = 'text-danger'; }
                                elseif (str_contains($log, 'WARNING')) { $logLevel = 'warning'; $logClass = 'text-warning'; }
                                @endphp
                                <tr>
                                    <td class="px-3"><span class="badge bg-{{ $logLevel == 'error' ? 'danger' : ($logLevel == 'warning' ? 'warning' : 'info') }}">{{ strtoupper($logLevel) }}</span></td>
                                    <td class="px-3"><code class="{{ $logClass }}" style="font-size: 12px;">{{ Str::limit($log, 200) }}</code></td>
                                    <td class="px-3 text-muted small">
                                        @if(preg_match('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $log, $matches)){{ $matches[0] }}@else-@endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5"><i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                        <p class="text-muted">No logs found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function clearLogs() {
        if (confirm('Are you sure you want to clear all logs?')) {
            fetch('{{ route("admin.logs.clear") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(() => location.reload());
        }
    }
</script>
@endsection