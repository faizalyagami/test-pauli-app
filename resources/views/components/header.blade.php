{{-- resources/views/components/header.blade.php --}}
<nav class="navbar-top">
    <div class="d-flex align-items-center">
        <button class="btn btn-link d-md-none text-dark" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <div class="ms-2">
            <h5 class="mb-0 fw-semibold text-dark">@yield('page-title', 'Dashboard')</h5>
            <small class="text-muted d-none d-md-block" id="currentDateTime"></small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Notification Bell -->
        <div class="dropdown">
            <button class="btn btn-link position-relative text-dark" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-bell fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="font-size: 10px;">
                    0
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="width: 300px;">
                <li class="dropdown-header bg-light py-2">
                    <strong>Notifications</strong>
                </li>
                <li>
                    <div class="dropdown-item text-muted text-center py-3" id="notificationList">
                        <i class="fas fa-inbox me-2"></i> No new notifications
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li class="text-center py-2">
                    <a href="#" class="text-primary text-decoration-none small" id="markAllRead">Mark all as read</a>
                </li>
            </ul>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link dropdown-toggle d-flex align-items-center gap-2 text-dark text-decoration-none" type="button" data-bs-toggle="dropdown">
                @if(Auth::user()->avatar)
                <img src="{{ Storage::url(Auth::user()->avatar) }}"
                    class="rounded-circle"
                    width="32"
                    height="32"
                    style="object-fit: cover;">
                @else
                <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-user text-secondary"></i>
                </div>
                @endif
                <span class="d-none d-md-inline fw-medium">{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down fa-xs text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                <li class="dropdown-header bg-light py-2">
                    <div class="d-flex align-items-center gap-2">
                        @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}"
                            class="rounded-circle"
                            width="36"
                            height="36"
                            style="object-fit: cover;">
                        @else
                        <div class="avatar-sm bg-secondary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        @endif
                        <div>
                            <strong>{{ Auth::user()->name }}</strong><br>
                            <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                        </div>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider m-0">
                </li>

                @if(auth()->user()->role === 'applicant')
                <li>
                    <a class="dropdown-item py-2" href="{{ route('applicant.profile') }}">
                        <i class="fas fa-user-circle me-2 text-primary"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('applicant.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2 text-info"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('applicant.history') }}">
                        <i class="fas fa-history me-2 text-warning"></i> Test History
                    </a>
                </li>
                @elseif(auth()->user()->role === 'tester')
                <li>
                    <a class="dropdown-item py-2" href="{{ route('tester.profile') }}">
                        <i class="fas fa-user-circle me-2 text-primary"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('tester.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2 text-info"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('tester.tests') }}">
                        <i class="fas fa-file-alt me-2 text-success"></i> Manage Tests
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('tester.reports') }}">
                        <i class="fas fa-chart-bar me-2 text-warning"></i> Reports
                    </a>
                </li>
                @elseif(auth()->user()->role === 'admin')
                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                        <i class="fas fa-user-circle me-2 text-primary"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2 text-info"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('admin.users') }}">
                        <i class="fas fa-users me-2 text-success"></i> Manage Users
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('admin.settings') }}">
                        <i class="fas fa-cog me-2 text-warning"></i> System Settings
                    </a>
                </li>
                @endif

                <li>
                    <hr class="dropdown-divider m-0">
                </li>
                <li>
                    <a class="dropdown-item py-2 text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    .navbar-top {
        background: white;
        padding: 12px 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .navbar-top .btn-link {
        text-decoration: none;
        padding: 0;
        border: none;
        background: transparent;
    }

    .navbar-top .btn-link:hover {
        opacity: 0.8;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        background: #e9ecef;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .dropdown-menu {
        border-radius: 12px;
        animation: fadeInDown 0.2s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-item {
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        padding-left: 20px;
    }

    .dropdown-header {
        border-radius: 12px 12px 0 0;
    }

    .badge {
        font-size: 9px;
        padding: 3px 6px;
        margin-top: -5px;
        margin-right: -5px;
    }

    @media (max-width: 768px) {
        .navbar-top {
            padding: 10px 16px;
        }

        .navbar-top h5 {
            font-size: 16px;
        }

        .dropdown-menu {
            min-width: 280px;
        }
    }
</style>

@push('scripts')
<script>
    // Update current date and time
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        const dateTimeElement = document.getElementById('currentDateTime');
        if (dateTimeElement) {
            dateTimeElement.textContent = now.toLocaleDateString('id-ID', options);
        }
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Simulate notification count (you can replace with actual data from server)
    function updateNotifications() {
        // You can fetch notifications from server via AJAX
        const badge = document.getElementById('notificationBadge');
        const notificationList = document.getElementById('notificationList');

        // Example: fetch('/api/notifications/unread-count')
        // .then(response => response.json())
        // .then(data => {
        //     if (data.count > 0) {
        //         badge.textContent = data.count;
        //         badge.style.display = 'block';
        //     } else {
        //         badge.style.display = 'none';
        //     }
        // });

        // Temporary: hide badge if no notifications
        const unreadCount = 0;
        if (unreadCount === 0) {
            badge.style.display = 'none';
        } else {
            badge.textContent = unreadCount;
            badge.style.display = 'block';
        }
    }
    updateNotifications();

    // Mark all notifications as read
    document.getElementById('markAllRead')?.addEventListener('click', function(e) {
        e.preventDefault();
        // Example: fetch('/api/notifications/mark-all-read', { method: 'POST' })
        // .then(() => {
        //     document.getElementById('notificationBadge').style.display = 'none';
        //     document.getElementById('notificationList').innerHTML = '<div class="text-center py-3"><i class="fas fa-check-circle me-2"></i> All notifications read</div>';
        // });

        document.getElementById('notificationBadge').style.display = 'none';
        document.getElementById('notificationList').innerHTML = '<div class="text-center py-3"><i class="fas fa-check-circle me-2 text-success"></i> All notifications marked as read</div>';
    });
</script>
@endpush