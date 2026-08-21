{{ Form::model($module, ['route' => ['project-modules.update', $module->id], 'method' => 'PUT', 'id' => 'editModuleForm', 'class' => 'needs-validation', 'novalidate' => 'novalidate']) }}
<div class="modal-body p-4">
    <div class="form-group mb-3">
        <label>{{ __('Module Name') }}</label>
        <input type="text" name="module_name" class="form-control" required value="{{ $module->module_name }}">
    </div>
    <div class="form-group mb-3">
        <label>{{ __('Description (Optional)') }}</label>
        <textarea name="description" class="form-control" rows="3">{{ $module->description }}</textarea>
    </div>
    <div class="form-group mb-3">
        <label>{{ __('Assign Employees (Optional)') }}</label>
        <select name="employee_ids[]" id="edit-module-employee-choices" class="form-control" multiple="multiple" data-placeholder="{{ __('Select Employees') }}">
            @php
                $assigned_ids = is_array($module->employee_ids) ? $module->employee_ids : [];
            @endphp
            @foreach($project->getEmployeeNames() as $id => $name)
                <option value="{{ $id }}" {{ in_array($id, $assigned_ids) ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        <small class="text-muted">{{ __('Only employees currently assigned to this project are available.') }}</small>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
</div>
{{ Form::close() }}

<script>
    if (document.getElementById('edit-module-employee-choices')) {
        new Choices('#edit-module-employee-choices', {
            removeItemButton: true,
        });
    }
</script>
