<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Full Name *</label>
            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                value="{{ old('full_name', $applicant->full_name ?? '') }}" required>
            @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $applicant->user->email ?? '') }}" required>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Date of Birth *</label>
            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                value="{{ old('date_of_birth', isset($applicant) && $applicant->date_of_birth ? $applicant->date_of_birth->format('Y-m-d') : '') }}" required>
            @error('date_of_birth')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Gender *</label>
            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender', $applicant->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $applicant->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Phone Number *</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $applicant->phone ?? '') }}" required>
            @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Education Background</label>
            <input type="text" name="education_background" class="form-control @error('education_background') is-invalid @enderror"
                value="{{ old('education_background', $applicant->education_background ?? '') }}">
            @error('education_background')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Institution</label>
            <input type="text" name="institution" class="form-control @error('institution') is-invalid @enderror"
                value="{{ old('institution', $applicant->institution ?? '') }}">
            @error('institution')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="registered" {{ old('status', $applicant->status ?? '') == 'registered' ? 'selected' : '' }}>Registered</option>
                <option value="test_taken" {{ old('status', $applicant->status ?? '') == 'test_taken' ? 'selected' : '' }}>Test Taken</option>
                <option value="processed" {{ old('status', $applicant->status ?? '') == 'processed' ? 'selected' : '' }}>Processed</option>
                <option value="accepted" {{ old('status', $applicant->status ?? '') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="rejected" {{ old('status', $applicant->status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $applicant->address ?? '') }}</textarea>
    @error('address')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if(!isset($applicant) || !$applicant->id)
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Confirm Password *</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
    </div>
</div>
@endif