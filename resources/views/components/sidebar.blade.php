{{-- resources/views/components/sidebar.blade.php --}}
<nav class="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-chart-line"></i> Pauli Test</h3>
    </div>

    <ul class="sidebar-menu">
        @auth
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'tester')
        <li class="{{ request()->routeIs('tester.dashboard') ? 'active' : '' }}">
            <a href="{{ route('tester.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-header">Manajemen</li>

        <li class="{{ request()->routeIs('tester.tests*') ? 'active' : '' }}">
            <a href="{{ route('tester.tests') }}">
                <i class="fas fa-file-alt"></i>
                <span>Daftar Test</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('tester.applicants*') ? 'active' : '' }}">
            <a href="{{ route('tester.applicants') }}">
                <i class="fas fa-users"></i>
                <span>Daftar Peserta</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('tester.sessions*') ? 'active' : '' }}">
            <a href="{{ route('tester.sessions') }}">
                <i class="fas fa-clock"></i>
                <span>Sesi Tes</span>
            </a>
        </li>

        <li class="menu-header">Laporan</li>

        <li class="{{ request()->routeIs('tester.reports*') ? 'active' : '' }}">
            <a href="{{ route('tester.reports') }}">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan Hasil</span>
            </a>
        </li>

        <li class="menu-header">Pengaturan</li>

        {{-- PERBAIKAN: Gunakan route tester.settings.index --}}
        <li class="{{ request()->routeIs('tester.settings*') ? 'active' : '' }}">
            <a href="{{ route('tester.settings.index') }}">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
        </li>
        @endif

        @if(auth()->user()->role === 'applicant')
        <li class="{{ request()->routeIs('applicant.dashboard') ? 'active' : '' }}">
            <a href="{{ route('applicant.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('applicant.history') ? 'active' : '' }}">
            <a href="{{ route('applicant.history') }}">
                <i class="fas fa-history"></i>
                <span>Riwayat Tes</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('applicant.tests') ? 'active' : '' }}">
            <a href="{{ route('applicant.tests') }}">
                <i class="fas fa-file-alt"></i>
                <span>Tersedia</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('applicant.statistics') ? 'active' : '' }}">
            <a href="{{ route('applicant.statistics') }}">
                <i class="fas fa-chart-line"></i>
                <span>Statistik</span>
            </a>
        </li>

        <li class="{{ request()->routeIs('applicant.profile') ? 'active' : '' }}">
            <a href="{{ route('applicant.profile') }}">
                <i class="fas fa-user"></i>
                <span>Profil</span>
            </a>
        </li>
        @endif

        <li class="menu-header">Akun</li>

        <li>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
        @endauth
    </ul>
</nav>