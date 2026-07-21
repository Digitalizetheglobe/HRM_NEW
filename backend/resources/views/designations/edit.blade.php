@extends('layouts.app')

@section('content')
<!-- PAGE TITLE -->
<div class="module-header">
    <div class="module-title-wrap">
        <div class="module-breadcrumbs">
            <a href="{{ route('designations.index') }}">
                <i class="fa-solid fa-arrow-left"></i> Designations
            </a>
            <span>/</span>
            <span>Edit</span>
        </div>
        <div class="module-title">Edit Designation</div>
    </div>
</div>

<!-- CONTENT -->
<div class="content module-content-wrap">
    <div class="col-main">

        <!-- EDIT FORM CARD -->
        <div class="card module-form-card">
            <form action="{{ route('designations.update', $designation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Branch Dropdown -->
                <div class="form-group">
                    <label for="branch_id" class="form-label">
                        Select Branch Location <span class="required">*</span>
                    </label>
                    <select name="branch_id" id="branch_id" required class="form-select">
                        <option value="" disabled>Choose a Branch location</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $designation->department->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Dropdown -->
                <div class="form-group">
                    <label for="department_id" class="form-label">
                        Select Department <span class="required">*</span>
                    </label>
                    <select name="department_id" id="department_id" required class="form-select">
                        <option value="" disabled>Choose a parent Department</option>
                    </select>
                    @error('department_id')
                        <p class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Designation Name -->
                <div class="form-group-last">
                    <label for="name" class="form-label">
                        Designation Title <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $designation->name) }}"
                        class="form-control" placeholder="e.g. Senior Laravel Engineer, Tech Lead">
                    @error('name')
                        <p class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="form-actions-footer">
                    <a href="{{ route('designations.index') }}" class="btn-module-cancel">
                        Cancel
                    </a>
                    <button type="submit" class="btn-module-submit">
                        Update Designation
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const departments = @json($departments);
    const branchSelect = document.getElementById('branch_id');
    const deptSelect = document.getElementById('department_id');

    function filterDepartments() {
        const branchId = branchSelect.value;
        const selectedDeptId = "{{ old('department_id', $designation->department_id) }}";
        
        // Clear previous options
        deptSelect.innerHTML = '<option value="" disabled selected>Choose a parent Department</option>';
        
        if (!branchId) {
            deptSelect.setAttribute('disabled', 'disabled');
            return;
        }

        // Filter departments by selected branch
        const filtered = departments.filter(d => d.branch_id == branchId);
        
        if (filtered.length > 0) {
            filtered.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = d.name;
                if (d.id == selectedDeptId) {
                    opt.selected = true;
                }
                deptSelect.appendChild(opt);
            });
            deptSelect.removeAttribute('disabled');
        } else {
            deptSelect.setAttribute('disabled', 'disabled');
            const opt = document.createElement('option');
            opt.value = "";
            opt.textContent = "No departments in this branch";
            opt.disabled = true;
            opt.selected = true;
            deptSelect.appendChild(opt);
        }
    }

    branchSelect.addEventListener('change', filterDepartments);

    // Initial run on load in case of dynamic validation redirects
    if (branchSelect.value) {
        filterDepartments();
    }
</script>
@endpush
