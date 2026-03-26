{{-- resources/views/tester/applicants/partials/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th width="5%">#</th>
                <th width="20%">Participant Number</th>
                <th width="25%">Full Name</th>
                <th width="15%">Phone</th>
                <th width="10%">Gender</th>
                <th width="10%">Status</th>
                <th width="15%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applicants as $applicant)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                    <code>{{ $applicant->participant_numb }}</code>
                </td>
                <td>
                    <strong>{{ $applicant->full_name }}</strong><br>
                    <small class="text-muted">{{ $applicant->user->email ?? '-' }}</small>
                </td>
                <td>{{ $applicant->phone ?? '-' }}</td>
                <td class="text-center">
                    @if($applicant->gender == 'male')
                    <i class="fas fa-mars text-primary"></i> Male
                    @elseif($applicant->gender == 'female')
                    <i class="fas fa-venus text-danger"></i> Female
                    @else
                    -
                    @endif
                </td>
                <td>
                    @php
                    $statusColors = [
                    'registered' => 'secondary',
                    'test_taken' => 'info',
                    'processed' => 'warning',
                    'accepted' => 'success',
                    'rejected' => 'danger'
                    ];
                    $color = $statusColors[$applicant->status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }}">
                        {{ ucfirst(str_replace('_', ' ', $applicant->status)) }}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('tester.applicants.show', $applicant) }}" class="btn btn-info" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('tester.applicants.edit', $applicant) }}" class="btn btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('tester.applicants.destroy', $applicant) }}" method="POST" class="d-inline" id="delete-form-{{ $applicant->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger" onclick="deleteApplicant({{ $applicant->id }}, '{{ $applicant->full_name }}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No applicants found</p>
                    <small class="text-muted">Click "Add New" to create your first applicant</small>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function deleteApplicant(id, name) {
        Swal.fire({
            title: 'Delete Applicant?',
            html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>