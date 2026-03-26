{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Users')

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
                                <i class="fas fa-users me-2"></i> Manage Users
                            </h4>
                            <p class="mb-0 opacity-75">Manage all system users, roles, and permissions</p>
                        </div>
                        <div>
                            <i class="fas fa-user-plus fa-3x opacity-50"></i>
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
                        <i class="fas fa-list me-2 text-primary"></i> All Users
                    </h6>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New User
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" id="searchUser" class="form-control" placeholder="Search by name or email...">
                        </div>
                        <div class="col-md-3">
                            <select id="filterRole" class="form-select">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="tester">Tester</option>
                                <option value="applicant">Applicant</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filterStatus" class="form-select">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary w-100" onclick="resetFilters()">
                                <i class="fas fa-sync me-1"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">#</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Role</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Joined</th>
                                    <th class="py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr data-role="{{ $user->role }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                @if($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}" width="32" height="32" class="rounded-circle">
                                                @else
                                                <i class="fas fa-user text-secondary"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong><br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'tester' ? 'warning' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td><small>{{ $user->created_at->format('d/m/Y') }}</small></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                onclick="toggleStatus({{ $user->id }}, '{{ $user->is_active ? 'inactive' : 'active' }}')"
                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check-circle' }}"></i>
                                            </button>
                                            @if($user->id != Auth::id())
                                            <button class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No users found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
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

    .avatar-sm {
        width: 35px;
        height: 35px;
        background: #f8f9fa;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>

<script>
    function filterTable() {
        const search = document.getElementById('searchUser').value.toLowerCase();
        const role = document.getElementById('filterRole').value;
        const status = document.getElementById('filterStatus').value;

        document.querySelectorAll('#usersTable tbody tr').forEach(row => {
            let show = true;
            if (search && !row.innerText.toLowerCase().includes(search)) show = false;
            if (role && row.dataset.role !== role) show = false;
            if (status && row.dataset.status !== status) show = false;
            row.style.display = show ? '' : 'none';
        });
    }

    document.getElementById('searchUser').addEventListener('keyup', filterTable);
    document.getElementById('filterRole').addEventListener('change', filterTable);
    document.getElementById('filterStatus').addEventListener('change', filterTable);

    function resetFilters() {
        document.getElementById('searchUser').value = '';
        document.getElementById('filterRole').value = '';
        document.getElementById('filterStatus').value = '';
        filterTable();
    }

    function toggleStatus(userId, newStatus) {
        if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this user?`)) {
            fetch(`/admin/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            }).then(() => location.reload());
        }
    }

    function deleteUser(userId, userName) {
        if (confirm(`Are you sure you want to delete ${userName}? This action cannot be undone.`)) {
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => location.reload());
        }
    }
</script>
@endsection