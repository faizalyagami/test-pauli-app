<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-import me-2"></i> Import Applicants
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tester.applicants.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Format File:</strong> Excel (.xlsx, .xls) or CSV<br>
                        <strong>Required Columns:</strong> full_name, email, phone, date_of_birth, gender<br>
                        <strong>Optional:</strong> address, education_background, institution
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select File</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Max size: 5MB</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Default Password</label>
                        <input type="text" name="default_password" class="form-control" value="password" required>
                        <small class="text-muted">Password for imported applicants</small>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="send_email" id="sendEmail" value="1">
                        <label class="form-check-label" for="sendEmail">
                            Send login credentials via email
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ asset('templates/applicant_import_template.xlsx') }}" class="btn btn-secondary">
                        <i class="fas fa-download me-1"></i> Download Template
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>