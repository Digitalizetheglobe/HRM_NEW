<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'DTGHRM') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard-body">

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<x-menu />

<!-- MAIN -->
<div class="main">
    <!-- TOPBAR -->
    <x-header />

    @yield('content')
</div>

<script>
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('overlay');
    sb.classList.toggle('open');
    ov.classList.toggle('show');
}

// Date button
const dateOptions = ['Today','Yesterday','This Week','This Month'];
let dateIdx = 0;
function toggleDateMenu() {
    dateIdx = (dateIdx + 1) % dateOptions.length;
    document.getElementById('dateBtnLabel').textContent = dateOptions[dateIdx];
}
</script>
@stack('scripts')
</body>
</html>
