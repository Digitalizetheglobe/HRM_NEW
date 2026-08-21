<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('units.import') }}" method="POST" enctype="multipart/form-data">
            @csrf   
            <div class="row align-items-center">
                <!-- Project Name Dropdown -->
                <div class="form-group col-md-6">
                    <label for="project_id" class="form-label">{{ __('Project Name') }}</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        <option value="" disabled selected>{{ __('Select Project') }}</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Import File Input -->
                <div class="form-group col-md-6">
                    <label for="import_file" class="form-label">{{ __('Import File') }}</label>
                    <input type="file" name="import_file" id="import_file" class="form-control" accept=".csv, .xlsx" required>
                </div>
            </div>

            @if(isset($import_errors))
    <div class="alert alert-danger">
        <h5>Import Errors:</h5>
        <ul>
            @foreach($import_errors as $failure)
                <li>
                    Row {{ $failure->row() }}: {{ $failure->errors()[0] }}
                    <br>Values: {{ json_encode($failure->values()) }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

            <!-- Submit Button -->
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Import Units') }}</button>
            </div>
        </form>
    </div>
</div>

