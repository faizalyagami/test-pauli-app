{{-- resources/views/tester/settings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Settings')

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
                                <i class="fas fa-cog me-2"></i> Settings
                            </h4>
                            <p class="mb-0 opacity-75">Manage your account preferences and system configurations</p>
                        </div>
                        <div>
                            <i class="fas fa-sliders-h fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="list-group list-group-flush">
                    <a href="#account" class="list-group-item list-group-item-action active settings-tab" data-tab="account">
                        <i class="fas fa-user-circle me-2 text-primary"></i> Account Settings
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action settings-tab" data-tab="notifications">
                        <i class="fas fa-bell me-2 text-warning"></i> Notifications
                    </a>
                    <a href="#preferences" class="list-group-item list-group-item-action settings-tab" data-tab="preferences">
                        <i class="fas fa-sliders-h me-2 text-info"></i> Preferences
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action settings-tab" data-tab="security">
                        <i class="fas fa-shield-alt me-2 text-danger"></i> Security
                    </a>
                    <a href="#data" class="list-group-item list-group-item-action settings-tab" data-tab="data">
                        <i class="fas fa-database me-2 text-success"></i> Data Management
                    </a>
                    <a href="{{ route('tester.settings.users') }}" class="btn btn-info">
                        <i class="fas fa-users me-1"></i> Manage Users
                    </a>
                    <a href="{{ route('tester.settings.logs') }}" class="btn btn-secondary">
                        <i class="fas fa-history me-1"></i> View Logs
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-md-9">
            <!-- Account Settings Tab -->
            <div id="account-tab" class="settings-tab-content active">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-user-circle me-2 text-primary"></i> Account Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tester.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    @if(Auth::user()->avatar)
                                    <img src="{{ Storage::url(Auth::user()->avatar) }}"
                                        class="rounded-circle" width="120" height="120" style="object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 120px; height: 120px;">
                                        <i class="fas fa-user-tie fa-4x text-secondary"></i>
                                    </div>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                                        style="width: 32px; height: 32px;" onclick="document.getElementById('avatarInput').click()">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                    <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="{{ ucfirst(Auth::user()->role) }}" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Member Since</label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->created_at->format('d F Y') }}" disabled>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications-tab" class="settings-tab-content">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-bell me-2 text-warning"></i> Notification Preferences
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tester.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotif"
                                        {{ ($settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="emailNotif">
                                        Email Notifications
                                    </label>
                                    <div class="form-text">Receive email notifications about test completions and reports</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="test_reminders" id="testReminders"
                                        {{ ($settings['test_reminders'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="testReminders">
                                        Test Reminders
                                    </label>
                                    <div class="form-text">Receive reminders for upcoming scheduled tests</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="report_notifications" id="reportNotif"
                                        {{ ($settings['report_notifications'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="reportNotif">
                                        Report Notifications
                                    </label>
                                    <div class="form-text">Get notified when new reports are generated</div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div id="preferences-tab" class="settings-tab-content">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-sliders-h me-2 text-info"></i> Display Preferences
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tester.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Language</label>
                                    <select name="language" class="form-select">
                                        <option value="id" {{ ($settings['language'] ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                        <option value="en" {{ ($settings['language'] ?? 'id') == 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Timezone</label>
                                    <select name="timezone" class="form-select">
                                        <option value="Asia/Jakarta" {{ ($settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                        <option value="Asia/Makassar" {{ ($settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                        <option value="Asia/Jayapura" {{ ($settings['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date Format</label>
                                    <select name="date_format" class="form-select">
                                        <option value="d/m/Y" {{ ($settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="m/d/Y" {{ ($settings['date_format'] ?? 'd/m/Y') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="Y-m-d" {{ ($settings['date_format'] ?? 'd/m/Y') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Items Per Page</label>
                                    <select name="items_per_page" class="form-select">
                                        <option value="10" {{ ($settings['items_per_page'] ?? 20) == 10 ? 'selected' : '' }}>10 items</option>
                                        <option value="20" {{ ($settings['items_per_page'] ?? 20) == 20 ? 'selected' : '' }}>20 items</option>
                                        <option value="50" {{ ($settings['items_per_page'] ?? 20) == 50 ? 'selected' : '' }}>50 items</option>
                                        <option value="100" {{ ($settings['items_per_page'] ?? 20) == 100 ? 'selected' : '' }}>100 items</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div id="security-tab" class="settings-tab-content">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-shield-alt me-2 text-danger"></i> Security Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6>Change Password</h6>
                            <p class="text-muted">Update your password to keep your account secure</p>
                            <a href="{{ route('profile.change-password') }}" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i> Change Password
                            </a>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6>Two-Factor Authentication</h6>
                            <p class="text-muted">Add an extra layer of security to your account</p>
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-mobile-alt me-1"></i> Coming Soon
                            </button>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6>Session Management</h6>
                            <p class="text-muted">Manage your active sessions and devices</p>
                            <button class="btn btn-info" onclick="viewSessions()">
                                <i class="fas fa-laptop me-1"></i> View Active Sessions
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Management Tab -->
            <div id="data-tab" class="settings-tab-content">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-database me-2 text-success"></i> Data Management
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h6>Export Data</h6>
                                <p class="text-muted">Export all test results and participant data</p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('tester.reports.export-excel') }}" class="btn btn-success">
                                        <i class="fas fa-file-excel me-1"></i> Export Excel
                                    </a>
                                    <a href="{{ route('tester.reports.export-pdf') }}" class="btn btn-danger">
                                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6>Backup Data</h6>
                                <p class="text-muted">Create a backup of your data</p>
                                <button onclick="createBackup()" class="btn btn-info">
                                    <i class="fas fa-database me-1"></i> Create Backup
                                </button>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6>Clear Cache</h6>
                                <p class="text-muted">Clear application cache to refresh data</p>
                                <button onclick="clearCache()" class="btn btn-warning">
                                    <i class="fas fa-trash-alt me-1"></i> Clear Cache
                                </button>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h6>System Logs</h6>
                                <p class="text-muted">View application logs and activity</p>
                                <a href="{{ route('tester.settings.logs') }}" class="btn btn-secondary">
                                    <i class="fas fa-history me-1"></i> View Logs
                                </a>
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

    .settings-tab-content {
        display: none;
    }

    .settings-tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .list-group-item {
        border: none;
        padding: 12px 20px;
        transition: all 0.2s ease;
    }

    .list-group-item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        color: white;
    }

    .list-group-item:not(.active):hover {
        background: #f8f9fa;
        border-radius: 10px;
    }

    .form-switch .form-check-input {
        width: 2.5rem;
        height: 1.25rem;
    }

    .form-switch .form-check-input:checked {
        background-color: #27ae60;
        border-color: #27ae60;
    }
</style>

<script>
    // Tab switching
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all tabs and contents
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.settings-tab-content').forEach(c => c.classList.remove('active'));

            // Add active class to clicked tab
            this.classList.add('active');

            // Show corresponding content
            const tabName = this.dataset.tab;
            document.getElementById(`${tabName}-tab`).classList.add('active');

            // Update URL hash without scrolling
            history.pushState(null, null, `#${tabName}`);
        });
    });

    // Check URL hash on load
    const hash = window.location.hash.substring(1);
    if (hash) {
        const tab = document.querySelector(`.settings-tab[data-tab="${hash}"]`);
        if (tab) {
            tab.click();
        }
    }

    // Avatar upload preview
    document.getElementById('avatarInput')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const img = document.querySelector('.rounded-circle');
                if (img) {
                    img.src = ev.target.result;
                }
            };
            reader.readAsDataURL(e.target.files[0]);

            // Auto submit form
            this.closest('form').submit();
        }
    });

    function createBackup() {
        if (confirm('Create a backup of your data?')) {
            fetch('{{ route("tester.settings.backup") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => alert('Backup created successfully!'));
        }
    }

    function clearCache() {
        if (confirm('Clear application cache?')) {
            fetch('{{ route("tester.settings.clear-cache") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => alert('Cache cleared successfully!'));
        }
    }

    function viewSessions() {
        alert('Feature coming soon!');
    }
</script>
@endsection