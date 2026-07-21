@extends('layouts.app')

@section('content')
<!-- PAGE TITLE -->
<div class="module-header">
    <div class="module-title-wrap">
        <div class="module-breadcrumbs">
            <a href="{{ route('departments.index') }}">
                <i class="fa-solid fa-arrow-left"></i> Departments
            </a>
            <span>/</span>
            <span>Edit</span>
        </div>
        <div class="module-title">Edit Department</div>
    </div>
</div>

<!-- CONTENT -->
<div class="content module-content-wrap">
    <div class="col-main">

        <!-- EDIT FORM CARD -->
        <div class="card module-form-card">
            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Branch Dropdown -->
                <div class="form-group">
                    <label for="branch_id" class="form-label">
                        Select Branch Location <span class="required">*</span>
                    </label>
                    <select name="branch_id" id="branch_id" required class="form-select">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $department->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Department Name -->
                <div class="form-group-last">
                    <label for="name" class="form-label">
                        Department Name <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $department->name) }}"
                        class="form-control" placeholder="e.g. Sales, Software Development">
                    @error('name')
                        <p class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="form-actions-footer">
                    <a href="{{ route('departments.index') }}" class="btn-module-cancel">
                        Cancel
                    </a>
                    <button type="submit" class="btn-module-submit">
                        Update Department
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
