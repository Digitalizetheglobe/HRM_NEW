@extends('layouts.auth')

@section('page_title', 'Login')

@section('content')
<div class="auth-heading">
    <h1>Welcome Back</h1>
    <p>Login to your DTGHRM account</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email Address -->
    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
            class="form-control" placeholder="name@company.com">
        @error('email')
            <p class="form-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Password -->
    <div class="form-group">
        <div class="form-label-row">
            <label class="form-label" for="password">Password</label>
            <a href="#" class="form-link">Forgot?</a>
        </div>
        <input type="password" id="password" name="password" required
            class="form-control" placeholder="••••••••">
        @error('password')
            <p class="form-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="checkbox-row">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Remember me</label>
    </div>

    <!-- Sign In Button -->
    <button type="submit" class="btn-primary">
        Sign In
    </button>

    <!-- Navigation Footer -->
    <div class="auth-divider">
        New to DTGHRM? <a href="{{ route('register') }}">Create an account</a>
    </div>
</form>
@endsection
