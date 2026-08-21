<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Login') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Cache Control Headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('{{ asset('assets/images/back.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            flex-direction: column;
        }

        .login-container {
            display: flex;
            flex-direction: row;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 900px;
            min-height: 500px;
        }

        .login-image {
            flex: 1;
            background: url('{{ asset('assets/images/login.jpg') }}') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .login-image .text {
            text-align: center;
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            padding: 20px;
            z-index: 1;
        }

        .login-form {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 0px;
            left: 40px;
        }

        .logo img {
            width: 100px;
            height: auto;
        }

        .login-form h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 10px 40px 10px 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .eye-icon {
            position: absolute;
            top: 70%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column-reverse;
                width: 95%;
            }

            .logo{
                left:190px;
            }

            .login-image {
                display: none;
            }

            .login-form {
                padding: 20px;
            }

            .logo img {
                width: 100px;
            }

            .login-form h2 {
                font-size: 22px;
            }

            .btn {
                font-size: 14px;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                width: 100%;
                border-radius: 0;
                box-shadow: none;
            }

            .logo{
                left:120px;
            }

            .login-form {
                padding: 15px;
            }

            .logo img {
                width: 100px;
            }

            .login-form h2 {
                font-size: 20px;
            }

            .login-image {
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            <div class="overlay"></div>
        </div>
        <div class="login-form">
            <div class="logo">
                <img src="{{ asset('storage/uploads/logo/logo_dark_dtg.png') }}" alt="Logo">
            </div>
            
            @if(session('status'))
                <div class="alert alert-success" style="margin-top: 80px;">
                    {{ session('status') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger" style="margin-top: 80px;">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" autocomplete="off" style="margin-top: 25px;">
                @csrf
                <input type="hidden" name="timestamp" value="{{ time() }}">
                
                <div class="form-group">
                    <label>{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" placeholder="{{ __('Enter your email') }}" required autofocus value="{{ old('email') }}">
                    @error('email')
                        <span class="text-danger"><small>{{ $message }}</small></span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" placeholder="{{ __('Password') }}" required autocomplete="new-password">
                    <i class="fa fa-eye eye-icon" id="togglePassword"></i>
                    @error('password')
                        <span class="text-danger"><small>{{ $message }}</small></span>
                    @enderror
                </div>
                
                <div class="form-group" style="display: flex; align-items: center; margin-bottom: 20px;">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="margin-right: 5px;">
                    <label for="remember" style="margin: 0;">{{ __('Remember Me') }}</label>
                </div>
                <button type="submit" class="btn">{{ __('Login') }}</button>
            </form>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Auto-fill from localStorage
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const rememberCheckbox = document.getElementById('remember');

            // Fill from localStorage if exists
            const savedEmail = localStorage.getItem('savedEmail');
            const savedPassword = localStorage.getItem('savedPassword');

            if (savedEmail) {
                emailInput.value = savedEmail;
                rememberCheckbox.checked = true;
            }

            if (savedPassword) {
                passwordInput.value = savedPassword;
            }

            // Optional: force reload if loaded from cache
            if (window.performance && performance.navigation.type === 2) {
                window.location.reload();
            }

            // Optional: Clear sessionStorage
            sessionStorage.clear();
        });

        // On form submit, store email (and optionally password)
        document.querySelector('form').addEventListener('submit', function () {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;

            if (remember) {
                localStorage.setItem('savedEmail', email);
                localStorage.setItem('savedPassword', password); // Optional, remove if insecure
            } else {
                localStorage.removeItem('savedEmail');
                localStorage.removeItem('savedPassword');
            }
        });

        // Prevent form resubmission on refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>


@if (Auth::check())
<script>
    document.addEventListener("deviceready", function () {
        var ss = new cordova.plugins.SecureStorage(
            function () {
                ss.remove(function () {
                    console.log("🔓 Token cleared on logout.");
                }, function (error) {
                    console.error("Error clearing token: " + error);
                }, 'login_token');
            },
            function (error) {
                console.error("SecureStorage init error: " + error);
            },
            'connect360_storage'
        );
    }, false);
</script>


@endif


</body>
</html>