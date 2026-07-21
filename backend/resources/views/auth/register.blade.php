@extends('layouts.auth')

@section('page_title', 'Register')

@section('content')
<div class="auth-heading">
    <h1>Company Registration</h1>
    <p>Join DTGHRM and manage your team efficiently</p>
</div>

<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
    @csrf

    <!-- Company Name -->
    <div class="form-group">
        <label class="form-label" for="name">Company Name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
            class="form-control" placeholder="Enter company name">
        @error('name')
            <p class="form-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Email Address -->
    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required
            class="form-control" placeholder="name@company.com" style="text-color:white;">
        @error('email')
            <p class="form-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Password Grid -->
    <div class="form-grid-2 form-group">
        <div>
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" required
                class="form-control" placeholder="••••••••">
        </div>
        <div>
            <label class="form-label" for="password_confirmation">Confirm</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="form-control" placeholder="••••••••">
        </div>
    </div>
    @error('password')
        <div class="form-group" style="margin-top:-10px;">
            <p class="form-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
            </p>
        </div>
    @enderror

    <!-- Company Logo / Avatar -->
    <div class="form-group">
        <label class="form-label">Company Logo / Avatar</label>
        <div class="file-upload-row">
            <div class="file-preview" id="file-preview-box">
                <i class="fa-regular fa-image" id="file-preview-icon"></i>
            </div>
            <input type="file" name="avatar" id="avatar-input" accept="image/*">
        </div>
        @error('avatar')
            <p class="form-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Create Company Button -->
    <button type="submit" class="btn-primary">
        Create Company Account
    </button>

    <!-- Navigation Footer -->
    <div class="auth-divider">
        Already have an account? <a href="{{ route('login') }}">Login here</a>
    </div>
</form>

@push('scripts')
<script>
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const previewBox = document.getElementById('file-preview-box');
        const previewIcon = document.getElementById('file-preview-icon');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewBox.innerHTML = `<img src="${event.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:10px;">`;
            }
            reader.readAsDataURL(file);
        } else {
            previewBox.innerHTML = `<i class="fa-regular fa-image" id="file-preview-icon"></i>`;
        }
    });
</script>
@endpush
@endsection
