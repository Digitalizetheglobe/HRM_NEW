@extends('layouts.admin')
@php
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
@endphp

@push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
@section('page-title')
    {{ __('Profile') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"> {{ __('Profile') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Profile') }}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
<div class="row">

<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xl-3">
                <div class="card sticky-top" style="top:30px">
                    <div class="list-group list-group-flush" id="useradd-sidenav">
                        <a href="#useradd-1"
                            class="list-group-item list-group-item-action border-0">{{ __('Personal Info') }} <div
                                class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                        <a href="#useradd-2"
                            class="list-group-item list-group-item-action border-0">{{ __('Change Password') }} <div
                                class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div id="useradd-1">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Personal Information') }}</h5>
                            <small> {{ __('Details about your personal information') }}</small>
                        </div>
                        <div class="card-body">
                            {{ Form::model($userDetail, ['route' => ['update.account'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label text-dark">{{ __('Name') }}</label>
                                        <input class="form-control @error('name') is-invalid @enderror" name="name"
                                            type="text" id="name" placeholder="{{ __('Enter Your Name') }}"
                                            value="{{ $userDetail->name }}" required autocomplete="name">
                                        @error('name')
                                            <span class="invalid-feedback text-danger text-xs"
                                                role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="email" class="col-form-label text-dark">{{ __('Email') }}</label>
                                        @if(\Auth::user()->type === 'employee')
                                            <small class="text-muted d-block mb-1">
                                                <i class="ti ti-info-circle"></i> {{ __('Email cannot be changed. Please contact administrator.') }}
                                            </small>
                                        @endif
                                        <input class="form-control @error('email') is-invalid @enderror {{ \Auth::user()->type === 'employee' ? 'bg-light' : '' }}" name="email"
                                            type="email" id="email" placeholder="{{ __('Enter Your Email Address') }}"
                                            value="{{ $userDetail->email }}" {{ \Auth::user()->type === 'employee' ? 'readonly' : 'required' }} autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback text-danger text-xs"
                                                role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        {{ Form::label('profile', __('Avatar'), ['class' => 'col-form-label']) }}
                                        <div class="choose-files ">
                                            <label for="profile">
                                                <div class=" bg-primary profile "> <i
                                                        class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                </div>
                                                <input type="file" class="form-control file" name="profile" id="profile" accept="image/*">
                                                <img id="blah" width="100" height="100" style="object-fit: cover; border-radius: 50%;" src="{{ !empty($userDetail->avatar) ? $profile . $userDetail->avatar : $profile . 'avatar.png' }}" />
                                            </label>
                                        </div>
                                        <span
                                        class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.') }}</span>
                                    @error('profile')
                                        <span class="invalid-feedback text-danger text-xs"
                                            role="alert">{{ $message }}</span>
                                    @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12 text-end">
                                    <input type="submit" value="{{ __('Save Changes') }}"
                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                </div>
                            </div>
                            </form>

                        </div>

                    </div>
                </div>

                <div id="useradd-2">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Change Password') }}</h5>
                            <small> {{ __('Details about your account password change') }}</small>
                        </div>
                        <div class="card-body">
                            {{ Form::model($userDetail, ['route' => ['update.password', $userDetail->id], 'method' => 'post']) }}

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('current_password', __('Current Password'), ['class' => 'col-form-label text-dark']) }}
                                        {{ Form::password('current_password', ['class' => 'form-control', 'placeholder' => __('Enter Current Password')]) }}
                                        @error('current_password')
                                            <span class="invalid-current_password" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('new_password', __('New Password'), ['class' => 'col-form-label text-dark']) }}
                                        {{ Form::password('new_password', ['class' => 'form-control', 'placeholder' => __('Enter New Password')]) }}
                                        @error('new_password')
                                            <span class="invalid-new_password" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{ Form::label('confirm_password', __('Re-type New Password'), ['class' => 'col-form-label text-dark']) }}
                                        {{ Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __('Enter Re-type New Password')]) }}
                                        @error('confirm_password')
                                            <span class="invalid-confirm_password" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer pr-0">
                                {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Avatar Crop Modal --}}
<div class="modal fade" id="avatarCropModal" tabindex="-1" aria-labelledby="avatarCropModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none;">
                <h5 class="modal-title text-white" id="avatarCropModalLabel">
                    <i class="ti ti-crop me-2" style="color: #38bdf8;"></i>{{ __('Crop Profile Photo') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div style="width: 100%; max-height: 360px; overflow: hidden; background: #f8fafc; border-radius: 10px;">
                    <img id="avatarCropImage" src="" alt="Crop Preview" style="display: block; max-width: 100%;">
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cropZoomIn" title="{{ __('Zoom In') }}" style="width:auto; height:auto; padding:6px 12px;">
                        <i class="ti ti-zoom-in"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cropZoomOut" title="{{ __('Zoom Out') }}" style="width:auto; height:auto; padding:6px 12px;">
                        <i class="ti ti-zoom-out"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cropRotateLeft" title="{{ __('Rotate Left') }}" style="width:auto; height:auto; padding:6px 12px;">
                        <i class="ti ti-rotate-2"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cropRotateRight" title="{{ __('Rotate Right') }}" style="width:auto; height:auto; padding:6px 12px;">
                        <i class="ti ti-rotate-clockwise-2"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cropReset" title="{{ __('Reset') }}" style="width:auto; height:auto; padding:6px 12px;">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="cropApplyBtn">
                    <i class="ti ti-check me-1"></i>{{ __('Crop & Apply') }}
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profile');
    const previewImg = document.getElementById('blah');
    const modalEl = document.getElementById('avatarCropModal');
    const cropImg = document.getElementById('avatarCropImage');
    let cropper = null;
    let croppedApplied = false;

    if (!fileInput || !modalEl || !cropImg) return;

    const cropModal = new bootstrap.Modal(modalEl);

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('{{ __("Please select a valid image file.") }}');
            fileInput.value = '';
            return;
        }

        croppedApplied = false;
        const reader = new FileReader();
        reader.onload = function (evt) {
            cropImg.src = evt.target.result;
            cropModal.show();
        };
        reader.readAsDataURL(file);
    });

    modalEl.addEventListener('shown.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
        }
        cropper = new Cropper(cropImg, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.85,
            responsive: true,
            background: false,
        });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        if (!croppedApplied) {
            fileInput.value = '';
        }
    });

    document.getElementById('cropZoomIn').addEventListener('click', () => cropper && cropper.zoom(0.1));
    document.getElementById('cropZoomOut').addEventListener('click', () => cropper && cropper.zoom(-0.1));
    document.getElementById('cropRotateLeft').addEventListener('click', () => cropper && cropper.rotate(-90));
    document.getElementById('cropRotateRight').addEventListener('click', () => cropper && cropper.rotate(90));
    document.getElementById('cropReset').addEventListener('click', () => cropper && cropper.reset());

    document.getElementById('cropApplyBtn').addEventListener('click', function () {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            fillColor: '#ffffff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        canvas.toBlob(function (blob) {
            if (!blob) return;
            const croppedFile = new File([blob], 'avatar.jpg', { type: 'image/jpeg', lastModified: Date.now() });
            const dt = new DataTransfer();
            dt.items.add(croppedFile);
            fileInput.files = dt.files;

            previewImg.src = URL.createObjectURL(blob);
            croppedApplied = true;
            cropModal.hide();
        }, 'image/jpeg', 0.82);
    });
});
</script>
@endsection
