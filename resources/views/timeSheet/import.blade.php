<!-- resources/views/units/import.blade.php -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Import Units</h5>
    </div>
    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('units.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="project_id" class="form-label">Project *</label>
                        <select name="project_id" id="project_id" class="form-control select2" required>
                            <option value="">Select Project</option>
                            @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="import_file" class="form-label">Import File *</label>
                        <div class="input-group">
                            <input type="file" name="import_file" id="import_file" class="form-control" required
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            <button class="btn btn-outline-secondary" type="button" id="clearFile">Clear</button>
                        </div>
                        <small class="form-text text-muted">
                            CSV or Excel file (Max: 2MB). <a href="{{ asset('sample/sample_units_import.csv') }}" download class="text-primary">Download Sample</a>
                        </small>
                        @error('import_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <div>
                    <button type="submit" class="btn btn-primary" id="import-button">
                        <i class="fas fa-upload me-2"></i> Import Units
                    </button>
                    <a href="{{ route('units.index') }}" class="btn btn-light">
                        <i class="fas fa-list me-2"></i> View All Units
                    </a>
                </div>
                <div>
                    <a href="{{ asset('sample/sample_units_import.csv') }}" download class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i> Download Sample
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding-top: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .file-info {
        margin-top: 5px;
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2({
        placeholder: "Select a project",
        width: '100%',
        allowClear: true
    });
    
    // Clear file input
    $('#clearFile').click(function() {
        $('#import_file').val('');
        $('#file-name').remove();
    });
    
    // Show selected file name
    $('#import_file').change(function() {
        if (this.files.length > 0) {
            // Remove previous file info if exists
            $('#file-name').remove();
            
            // Add new file info
            $(this).after('<div id="file-name" class="file-info">Selected file: ' + this.files[0].name + '</div>');
        }
    });
    
    // Form submission handler
    $('#importForm').submit(function(e) {
        e.preventDefault();
        const importBtn = $('#import-button');
        importBtn.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i> Importing...');
        
        // Submit form via AJAX if you want to handle response without page reload
        // Otherwise just submit normally
        this.submit();
        
        // If using AJAX:
        /*
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                // Handle success
            },
            error: function(xhr) {
                // Handle error
            },
            complete: function() {
                importBtn.prop('disabled', false).html('<i class="fas fa-upload me-2"></i> Import Units');
            }
        });
        */
    });
    
    // Enable tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush