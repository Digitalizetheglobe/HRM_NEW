@extends('layouts.app')

@section('content')
<!-- PAGE TITLE -->
<div class="module-header">
    <div class="module-title-wrap">
        <div class="module-breadcrumbs">
            <a href="{{ route('branches.index') }}">
                <i class="fa-solid fa-arrow-left"></i> Branches
            </a>
            <span>/</span>
            <span>Edit</span>
        </div>
        <div class="module-title">Edit Branch</div>
    </div>
</div>

<!-- CONTENT -->
<div class="content module-content-wrap">
    <div class="col-main">

        <!-- EDIT FORM CARD -->
        <div class="card module-form-card">
            <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        Branch Name <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $branch->name) }}"
                        class="form-control" placeholder="e.g. Headquarters, London Office">
                    @error('name')
                        <p class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Address Field -->
                <div class="form-group-last">
                    <label for="address" class="form-label">
                        Address Description
                    </label>
                    <textarea name="address" id="address" rows="4"
                        class="form-control" placeholder="Enter the full physical location address...">{{ old('address', $branch->address) }}</textarea>
                    @error('address')
                        <p class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="form-actions-footer">
                    <a href="{{ route('branches.index') }}" class="btn-module-cancel">
                        Cancel
                    </a>
                    <button type="submit" class="btn-module-submit">
                        Update Branch
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
