<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to your DTGHRM account — The Globe HR Management System">
    <title>{{ config('app.name', 'DTGHRM') }} — @yield('page_title', 'Welcome')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">

<!-- Background animated scene -->
<div class="bg-scene">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<!-- Auth content -->
<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Brand -->
        <div class="auth-brand">
            <div class="brand-icon"><i class="fa-solid fa-globe"></i></div>
            <div>
                <div class="brand-name">DTGHRM</div>
                <div class="brand-sub">The Globe — HR Management</div>
            </div>
        </div>

        @yield('content')

    </div>
</div>

@stack('scripts')
</body>
</html>
