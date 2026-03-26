{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0">
                <div class="card-body py-4">
                    <h4 class="mb-1 fw-bold"><i class="fas fa-cog me-2"></i> System Settings</h4>
                    <p class="mb-0 opacity-75">Configure system-wide settings and preferences</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">General Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">System Name</label>
                                <input type="text" name="app_name" class="form-control" value="{{ config('app.name') }}" required>
                                <small class="text-muted">This will be displayed as the system title</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">System Timezone</label>
                                <select name="timezone" class="form-select" required>
                                    <option value="Asia/Jakarta" {{ config('app.timezone') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" {{ config('app.timezone') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" {{ config('app.timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Changes to system name and timezone will take effect after page refresh.
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Information Card -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2 text-primary"></i> System Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Laravel Version</th>
                                    <td>{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <th>PHP Version</th>
                                    <td>{{ phpversion() }}</td>
                                </tr>
                                <tr>
                                    <th>Environment</th>
                                    <td><span class="badge bg-{{ app()->environment() == 'production' ? 'danger' : 'info' }}">{{ app()->environment() }}</span></td>
                                </tr>
                                <tr>
                                    <th>Debug Mode</th>
                                    <td><span class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Server Software</th>
                                    <td>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>Server OS</th>
                                    <td>{{ PHP_OS }}</td>
                                </tr>
                                <tr>
                                    <th>Max Execution Time</th>
                                    <td>{{ ini_get('max_execution_time') }} seconds</td>
                                </tr>
                                <tr>
                                    <th>Memory Limit</th>
                                    <td>{{ ini_get('memory_limit') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cache Management Card -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-database me-2 text-primary"></i> Cache Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <button onclick="clearCache('cache')" class="btn btn-warning w-100">
                                <i class="fas fa-trash-alt me-1"></i> Clear Cache
                            </button>
                            <small class="text-muted">Clear application cache</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <button onclick="clearCache('config')" class="btn btn-info w-100">
                                <i class="fas fa-cog me-1"></i> Clear Config
                            </button>
                            <small class="text-muted">Clear configuration cache</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <button onclick="clearCache('view')" class="btn btn-secondary w-100">
                                <i class="fas fa-eye me-1"></i> Clear View
                            </button>
                            <small class="text-muted">Clear compiled views</small>
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
</style>

@push('scripts')
<script>
    function clearCache(type) {
        fetch(`/admin/cache/${type}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            alert(`${type.charAt(0).toUpperCase() + type.slice(1)} cleared successfully!`);
        }).catch(() => {
            alert('Failed to clear cache');
        });
    }
</script>
@endpush
@endsection