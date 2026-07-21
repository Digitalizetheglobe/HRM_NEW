@extends('layouts.app')

@section('content')
<!-- PAGE TITLE -->
<div style="padding:20px 28px 0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
    <div>
        <div style="font-size:20px; font-weight:800; color:var(--text-primary);">Dashboard</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">{{ now()->format('l, j F Y') }} · Welcome back!</div>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <button style="padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:7px; font-family:inherit;">
            <i class="fa-solid fa-download"></i> Export Report
        </button>
    </div>
</div>

<!-- CONTENT -->
<div class="content">
    <div class="col-main">

        <!-- STAT CARDS -->
        <div class="stats-grid">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-label">Total Employees</div>
                <div class="stat-value">{{ \App\Models\User::where('company_id', Auth::id())->count() }}</div>
                <div class="stat-delta up"><i class="fa-solid fa-arrow-trend-up"></i> +{{ \App\Models\User::where('company_id', Auth::id())->whereMonth('created_at', now()->month)->count() }} this month</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fa-solid fa-sitemap"></i></div>
                <div class="stat-label">Departments</div>
                <div class="stat-value">0</div>
                <div class="stat-delta neutral"><i class="fa-solid fa-minus"></i> No change</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-regular fa-calendar-check"></i></div>
                <div class="stat-label">Total Leaves</div>
                <div class="stat-value">0</div>
                <div class="stat-delta down"><i class="fa-solid fa-arrow-trend-down"></i> All on duty</div>
            </div>
            <div class="stat-card teal">
                <div class="stat-icon"><i class="fa-solid fa-umbrella-beach"></i></div>
                <div class="stat-label">Holidays</div>
                <div class="stat-value">0</div>
                <div class="stat-delta neutral"><i class="fa-solid fa-minus"></i> None scheduled</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-icon"><i class="fa-solid fa-diagram-project"></i></div>
                <div class="stat-label">Total Projects</div>
                <div class="stat-value">0</div>
                <div class="stat-delta up"><i class="fa-solid fa-plus"></i> Start a project</div>
            </div>
            <div class="stat-card pink">
                <div class="stat-icon"><i class="fa-solid fa-ticket"></i></div>
                <div class="stat-label">Total Tickets</div>
                <div class="stat-value">0</div>
                <div class="stat-delta neutral"><i class="fa-solid fa-circle-check"></i> All resolved</div>
            </div>
        </div>

        <!-- CLOCK IN / NOT CLOCK IN -->
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-fingerprint" style="color:var(--accent-teal); font-size:16px;"></i>
                <span class="card-title">Today's Attendance</span>
                <div style="display:flex; gap:16px; align-items:center; margin-right:8px;">
                    <span style="font-size:12px; font-weight:600; color:var(--accent-green); display:flex; align-items:center; gap:5px;"><span style="width:8px;height:8px;background:var(--accent-green);border-radius:50%;display:inline-block;"></span>Clocked In: 0</span>
                    <span style="font-size:12px; font-weight:600; color:#ef4444; display:flex; align-items:center; gap:5px;"><span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block;"></span>Absent: 0</span>
                </div>
                <button class="card-action">View All</button>
            </div>
            <div class="attendance-wrap">
                <div class="half">
                    <div style="padding:10px 16px 6px; font-size:11.5px; font-weight:700; color:var(--accent-teal); text-transform:uppercase; letter-spacing:.06em; display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle-check"></i> Clock-In Employees</div>
                    <div class="table-scroll">
                        <table>
                            <thead><tr><th>Employee</th><th>Clock-In Time</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-slate-400">No clock-ins recorded today</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="half">
                    <div style="padding:10px 16px 6px; font-size:11.5px; font-weight:700; color:#ef4444; text-transform:uppercase; letter-spacing:.06em; display:flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle-xmark"></i> Not Clocked In</div>
                    <div class="table-scroll">
                        <table>
                            <thead><tr><th>Employee</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-slate-400">All employees accounted for</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ATTENDANCE DONUT + NOTICES -->
        <div class="mid-row">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-chart-pie" style="color:var(--primary); font-size:15px;"></i>
                    <span class="card-title">Attendance Rate</span>
                </div>
                <div class="donut-wrap">
                    <svg width="120" height="120" viewBox="0 0 120 120" class="donut-svg">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#f1f5f9" stroke-width="12"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="url(#donutGrad)" stroke-width="12"
                            stroke-dasharray="0 314" stroke-linecap="round"
                            transform="rotate(-90 60 60)" style="transition:stroke-dasharray 1s ease;"/>
                        <defs>
                            <linearGradient id="donutGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#2563eb"/>
                                <stop offset="100%" stop-color="#7c3aed"/>
                            </linearGradient>
                        </defs>
                        <text x="60" y="56" text-anchor="middle" font-family="DM Mono,monospace" font-size="18" font-weight="800" fill="#0f172a">0%</text>
                        <text x="60" y="72" text-anchor="middle" font-family="Plus Jakarta Sans,sans-serif" font-size="9" fill="#94a3b8">Present Today</text>
                    </svg>
                    <div class="donut-legend">
                        <div class="legend-row"><div class="legend-dot" style="background:#2563eb;"></div><div class="legend-key">Present</div><div class="legend-val">0</div></div>
                        <div class="legend-row"><div class="legend-dot" style="background:#ef4444;"></div><div class="legend-key">Absent</div><div class="legend-val">0</div></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-bullhorn" style="color:var(--accent-orange); font-size:15px;"></i>
                    <span class="card-title">Company Notices</span>
                    <button class="card-action">+ New</button>
                </div>
                <div class="notice-list">
                    <div class="notice-item text-center py-8 text-slate-400">
                        No active notices at the moment.
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /col-main -->

    <!-- RIGHT SIDEBAR -->
    <div class="col-side">

        <!-- QUICK STATS -->
        <div class="side-card">
            <div class="side-card-header"><i class="fa-solid fa-chart-line"></i> Quick Stats</div>
            <div class="quick-stats">
                <div class="qs-row">
                    <div class="qs-icon" style="background:#eff6ff;"><i class="fa-solid fa-clock" style="color:#2563eb;"></i></div>
                    <div class="qs-info"><div class="qs-label">Avg Clock-In</div><div class="qs-val">00:00 AM</div></div>
                </div>
                <div style="margin: 4px 0 2px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;"><span style="font-size:11.5px; color:var(--text-muted); font-weight:500;">Attendance Rate</span><span style="font-size:11.5px; font-weight:700; color:var(--accent-green);">0%</span></div>
                    <div class="qs-bar"><div class="qs-fill" style="width:0%; background:linear-gradient(90deg,#16a34a,#4ade80);"></div></div>
                </div>
            </div>
        </div>

        <!-- CALENDAR -->
        <div class="side-card">
            <div class="cal-nav">
                <button class="cal-nav-btn" onclick="prevMonth()"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="cal-month" id="calMonthLabel">MAY 2026</div>
                <button class="cal-nav-btn" onclick="nextMonth()"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            <div class="cal-grid">
                <div class="cal-days">
                    <div class="cal-day-label">Su</div>
                    <div class="cal-day-label">Mo</div>
                    <div class="cal-day-label">Tu</div>
                    <div class="cal-day-label">We</div>
                    <div class="cal-day-label">Th</div>
                    <div class="cal-day-label">Fr</div>
                    <div class="cal-day-label">Sa</div>
                </div>
                <div class="cal-dates" id="calDates"></div>
            </div>
        </div>

    </div><!-- /col-side -->
</div><!-- /content -->
@endsection

@push('scripts')
<script>
// Calendar
let calYear = {{ now()->year }}, calMonth = {{ now()->month - 1 }}; 
const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function renderCalendar() {
    document.getElementById('calMonthLabel').textContent = months[calMonth].toUpperCase() + ' ' + calYear;
    const firstDay = new Date(calYear, calMonth, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
    const prevDays = new Date(calYear, calMonth, 0).getDate();
    const today = new Date();
    const isCurrentMonth = today.getFullYear() === calYear && today.getMonth() === calMonth;
    let html = '';
    for (let i = 0; i < firstDay; i++) {
        html += `<div class="cal-date other">${prevDays - firstDay + 1 + i}</div>`;
    }
    for (let d = 1; d <= daysInMonth; d++) {
        const isToday = isCurrentMonth && d === today.getDate();
        html += `<div class="cal-date${isToday ? ' today' : ''}">${d}</div>`;
    }
    const total = firstDay + daysInMonth;
    const remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let i = 1; i <= remaining; i++) {
        html += `<div class="cal-date other">${i}</div>`;
    }
    document.getElementById('calDates').innerHTML = html;
}

function prevMonth() {
    calMonth--;
    if (calMonth < 0) { calMonth = 11; calYear--; }
    renderCalendar();
}
function nextMonth() {
    calMonth++;
    if (calMonth > 11) { calMonth = 0; calYear++; }
    renderCalendar();
}

renderCalendar();

window.addEventListener('load', () => {
    setTimeout(() => {
        const circle = document.querySelector('.donut-svg circle:last-child');
        if (circle) circle.style.strokeDasharray = '0 314';
    }, 300);
});
</script>
@endpush
