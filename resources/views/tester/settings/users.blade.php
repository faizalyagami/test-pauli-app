{{-- resources/views/tester/settings/users.blade.php --}}
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
                            <p class="mb-0 opacity-75">Manage system users, roles, and permissions</p>
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
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus me-1"></i> Add New User
                    </button>
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
                                <tr class="small text-uppercase text-muted">
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
                                    <td>
                                        <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" onclick="editUser({{ $user->id }})" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
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
                                        <small class="text-muted">Click "Add New User" to create one</small>
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tester.settings.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-select" required>
                            <option value="tester">Tester</option>
                            <option value="admin">Admin</option>
                            <option value="applicant">Applicant</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="tester">Tester</option>
                            <option value="admin">Admin</option>
                            <option value="applicant">Applicant</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="edit_is_active" class="form-check-input">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
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

    .table> :not(caption)>*>* {
        vertical-align: middle;
    }
</style>

<script>
    // Search and filter functionality
    function filterTable() {
        const search = document.getElementById('searchUser').value.toLowerCase();
        const role = document.getElementById('filterRole').value;
        const status = document.getElementById('filterStatus').value;

        document.querySelectorAll('#usersTable tbody tr').forEach(row => {
            let show = true;

            if (search) {
                const text = row.innerText.toLowerCase();
                if (!text.includes(search)) show = false;
            }

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

    function editUser(userId) {
        fetch(`/tester/settings/users/${userId}`)
            .then(response => response.json())
            .then(user => {
                document.getElementById('edit_name').value = user.name;
                document.getElementById('edit_email').value = user.email;
                document.getElementById('edit_role').value = user.role;
                document.getElementById('edit_is_active').checked = user.is_active;
                document.getElementById('editUserForm').action = `/tester/settings/users/${userId}`;
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            });
    }

    function toggleStatus(userId, newStatus) {
        const action = newStatus === 'active' ? 'activate' : 'deactivate';
        if (confirm(`Are you sure you want to ${action} this user?`)) {
            fetch(`/tester/settings/users/${userId}/toggle-status`, {
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
            fetch(`/tester/settings/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => location.reload());
        }
    }
</script>
@endsection