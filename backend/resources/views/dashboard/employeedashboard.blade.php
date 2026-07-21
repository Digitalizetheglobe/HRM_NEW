@extends('layouts.app')

@section('content')

{{-- PAGE TITLE --}}
<div style="padding:20px 28px 0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
    <div>
        <div style="font-size:20px; font-weight:800; color:var(--text-primary);">My Dashboard</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">
            {{ now()->format('l, j F Y') }} · Welcome back, {{ auth()->user()->name }}!
        </div>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('employees.show', auth()->user()->employee->id ?? 0) }}"
           style="padding:8px 16px; background:var(--primary); color:#fff; border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:7px; font-family:inherit; text-decoration:none;">
            <i class="fa-solid fa-user"></i> My Profile
        </a>
    </div>
</div>

{{-- CONTENT --}}
<div class="content">
    <div class="col-main">

        {{-- STAT CARDS --}}
        @php
            $employee   = auth()->user()->employee;
            $dept       = optional($employee)->department;
            $desig      = optional($employee)->designation;
            $joiningDate = optional($employee)->joining_date;
            $yearsWorked = $joiningDate ? $joiningDate->diffInYears(now()) : 0;
        @endphp

        {{-- TODAY'S ATTENDANCE PUNCH --}}
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-fingerprint" style="color:var(--accent-teal); font-size:16px;"></i>
                <span class="card-title">My Attendance Today</span>
                <div style="display:flex; gap:16px; align-items:center; margin-right:8px;">
                    <span id="punchStatus" style="font-size:12px; font-weight:600; color:var(--text-muted); display:flex; align-items:center; gap:5px;">
                        <span style="width:8px;height:8px;background:var(--text-muted);border-radius:50%;display:inline-block;"></span>
                        Loading…
                    </span>
                </div>
                <button class="card-action" onclick="window.location='#'">Full Log</button>
            </div>
            <div style="padding:24px; display:flex; align-items:center; justify-content:center; gap:32px; flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;">Clock-In</div>
                    <div id="punchInTime" style="font-family:'DM Mono',monospace; font-size:24px; font-weight:800; color:var(--accent-teal);">--:-- --</div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                    <button id="punchBtn" onclick="handlePunch()"
                        style="padding:12px 32px; background:linear-gradient(135deg,var(--accent-teal),#2dd4bf); color:#fff; border:none; border-radius:12px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; display:flex; align-items:center; gap:8px; box-shadow:0 4px 14px rgba(13,148,136,.3); transition:all .2s;">
                        <i class="fa-solid fa-play" id="punchIcon"></i>
                        <span id="punchLabel">Punch In</span>
                    </button>
                    <div id="punchLiveTime" style="font-family:'DM Mono',monospace; font-size:13px; color:var(--text-muted);"></div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px;">Clock-Out</div>
                    <div id="punchOutTime" style="font-family:'DM Mono',monospace; font-size:24px; font-weight:800; color:#ef4444;">--:-- --</div>
                </div>
            </div>
        </div>

        {{-- ATTENDANCE DONUT + NOTICES --}}
        <div class="mid-row">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-chart-pie" style="color:var(--primary); font-size:15px;"></i>
                    <span class="card-title">My Attendance Rate</span>
                </div>
                <div class="donut-wrap">
                    <svg width="120" height="120" viewBox="0 0 120 120" class="donut-svg">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#f1f5f9" stroke-width="12"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="url(#donutGradEmp)" stroke-width="12"
                            stroke-dasharray="0 314" stroke-linecap="round"
                            transform="rotate(-90 60 60)" style="transition:stroke-dasharray 1s ease;" id="donutCircle"/>
                        <defs>
                            <linearGradient id="donutGradEmp" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#0d9488"/>
                                <stop offset="100%" stop-color="#2dd4bf"/>
                            </linearGradient>
                        </defs>
                        <text x="60" y="56" text-anchor="middle" font-family="DM Mono,monospace" font-size="18" font-weight="800" fill="#0f172a">—</text>
                        <text x="60" y="72" text-anchor="middle" font-family="Plus Jakarta Sans,sans-serif" font-size="9" fill="#94a3b8">This Month</text>
                    </svg>
                    <div class="donut-legend">
                        <div class="legend-row">
                            <div class="legend-dot" style="background:#0d9488;"></div>
                            <div class="legend-key">Present</div>
                            <div class="legend-val">—</div>
                        </div>
                        <div class="legend-row">
                            <div class="legend-dot" style="background:#ef4444;"></div>
                            <div class="legend-key">Absent</div>
                            <div class="legend-val">—</div>
                        </div>
                        <div class="legend-row">
                            <div class="legend-dot" style="background:#7c3aed;"></div>
                            <div class="legend-key">On Leave</div>
                            <div class="legend-val">—</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-bullhorn" style="color:var(--accent-orange); font-size:15px;"></i>
                    <span class="card-title">Company Notices</span>
                </div>
                <div class="notice-list">
                    <div class="notice-item">
                        <div class="notice-dot" style="background:#2563eb;"></div>
                        <div>
                            <div class="notice-title">Q2 Performance Reviews Starting</div>
                            <div class="notice-dates">May 10, 2026 – May 20, 2026</div>
                        </div>
                        <span class="notice-type" style="background:#eff6ff; color:#2563eb;">HR</span>
                    </div>
                    <div class="notice-item">
                        <div class="notice-dot" style="background:#ea580c;"></div>
                        <div>
                            <div class="notice-title">Office Shift to New Block – 3rd Floor</div>
                            <div class="notice-dates">May 8, 2026 – May 9, 2026</div>
                        </div>
                        <span class="notice-type" style="background:#fff7ed; color:#ea580c;">Admin</span>
                    </div>
                    <div class="notice-item">
                        <div class="notice-dot" style="background:#16a34a;"></div>
                        <div>
                            <div class="notice-title">Health Insurance Renewal Deadline</div>
                            <div class="notice-dates">May 15, 2026</div>
                        </div>
                        <span class="notice-type" style="background:#f0fdf4; color:#16a34a;">Finance</span>
                    </div>
                    <div class="notice-item">
                        <div class="notice-dot" style="background:#7c3aed;"></div>
                        <div>
                            <div class="notice-title">Training: AWS Cloud Fundamentals</div>
                            <div class="notice-dates">May 20–22, 2026</div>
                        </div>
                        <span class="notice-type" style="background:#fdf4ff; color:#7c3aed;">Tech</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- UPCOMING MEETINGS --}}
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-video" style="color:var(--accent-blue); font-size:15px;"></i>
                <span class="card-title">Upcoming Meetings</span>
                <span class="card-badge">Today</span>
            </div>
            <div class="meeting-list">
                <div class="meeting-item">
                    <div class="meeting-time-col">
                        <div class="meeting-time">10:00</div>
                        <div class="meeting-ampm">AM</div>
                    </div>
                    <div style="width:3px; height:36px; background:linear-gradient(180deg,#2563eb,#7c3aed); border-radius:2px; flex-shrink:0;"></div>
                    <div class="meeting-info">
                        <div class="meeting-name">Weekly Team Standup</div>
                        <div class="meeting-room"><i class="fa-solid fa-location-dot" style="margin-right:3px; font-size:10px;"></i>Conference Room A</div>
                    </div>
                    <div class="meeting-avatars">
                        <div class="m-avatar" style="background:#7c3aed;">AS</div>
                        <div class="m-avatar" style="background:#0284c7;">PK</div>
                        <div class="m-avatar" style="background:#64748b; font-size:8px;">+4</div>
                    </div>
                </div>
                <div class="meeting-item">
                    <div class="meeting-time-col">
                        <div class="meeting-time">02:30</div>
                        <div class="meeting-ampm">PM</div>
                    </div>
                    <div style="width:3px; height:36px; background:linear-gradient(180deg,#0d9488,#2dd4bf); border-radius:2px; flex-shrink:0;"></div>
                    <div class="meeting-info">
                        <div class="meeting-name">Q2 Project Review</div>
                        <div class="meeting-room"><i class="fa-brands fa-google" style="margin-right:3px; font-size:10px;"></i>Google Meet</div>
                    </div>
                    <div class="meeting-avatars">
                        <div class="m-avatar" style="background:#0d9488;">VP</div>
                        <div class="m-avatar" style="background:#ea580c;">SJ</div>
                        <div class="m-avatar" style="background:#64748b; font-size:8px;">+2</div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /col-main --}}

    {{-- RIGHT SIDEBAR --}}
    <div class="col-side">

        {{-- MY INFO CARD --}}
        <div class="side-card">
            <div class="side-card-header"><i class="fa-solid fa-circle-user"></i> My Info</div>
            <div style="padding:16px 18px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg,var(--primary),var(--accent-purple)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px; font-weight:800; flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:700; color:var(--text-primary);">{{ auth()->user()->name }}</div>
                        <div style="font-size:12px; color:var(--text-muted);">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div style="border-top:1px solid var(--border); padding-top:10px; display:flex; flex-direction:column; gap:7px;">
                    <div style="display:flex; justify-content:space-between; font-size:12.5px;">
                        <span style="color:var(--text-muted);">Designation</span>
                        <span style="font-weight:600; color:var(--text-primary);">{{ optional($desig)->name ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:12.5px;">
                        <span style="color:var(--text-muted);">Department</span>
                        <span style="font-weight:600; color:var(--text-primary);">{{ optional($dept)->name ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:12.5px;">
                        <span style="color:var(--text-muted);">Employee ID</span>
                        <span style="font-weight:600; color:var(--primary);">{{ optional($employee)->employee_uid ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:12.5px;">
                        <span style="color:var(--text-muted);">Joined</span>
                        <span style="font-weight:600; color:var(--text-primary);">{{ $joiningDate ? $joiningDate->format('d M Y') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- UPCOMING EVENTS --}}
        <div class="side-card">
            <div class="side-card-header"><i class="fa-solid fa-star"></i> Upcoming Events This Month</div>
            <div class="events-list">
                <div class="event-item">
                    <div class="event-date-col">
                        <div class="event-day">11</div>
                        <div class="event-mon">May</div>
                    </div>
                    <div class="event-info">
                        <div class="event-name">Bhavana Ravindra Ekkaldevi</div>
                        <div class="event-sub">Birthday</div>
                    </div>
                    <span class="event-chip birthday"><i class="fa-solid fa-cake-candles" style="margin-right:3px;"></i>Birthday</span>
                </div>
                <div class="event-item">
                    <div class="event-date-col">
                        <div class="event-day">22</div>
                        <div class="event-mon">May</div>
                    </div>
                    <div class="event-info">
                        <div class="event-name">Buddha Purnima</div>
                        <div class="event-sub">Public Holiday</div>
                    </div>
                    <span class="event-chip holiday"><i class="fa-solid fa-om" style="margin-right:3px;"></i>Holiday</span>
                </div>
                <div class="event-item">
                    <div class="event-date-col">
                        <div class="event-day">28</div>
                        <div class="event-mon">May</div>
                    </div>
                    <div class="event-info">
                        <div class="event-name">Rahul Mehta</div>
                        <div class="event-sub">5-Year Work Anniversary</div>
                    </div>
                    <span class="event-chip work-anni"><i class="fa-solid fa-trophy" style="margin-right:3px;"></i>Work Anni.</span>
                </div>
            </div>
        </div>

        {{-- CALENDAR --}}
        <div class="side-card">
            <div class="cal-nav">
                <button class="cal-nav-btn" onclick="prevMonth()"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="cal-month" id="calMonthLabel"></div>
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

    </div>{{-- /col-side --}}
</div>{{-- /content --}}

@endsection

@push('scripts')
<script>
// ── Calendar ──────────────────────────────────────────
let calYear = {{ now()->year }}, calMonth = {{ now()->month - 1 }};
const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

function renderCalendar() {
    document.getElementById('calMonthLabel').textContent = months[calMonth].toUpperCase() + ' ' + calYear;
    const firstDay    = new Date(calYear, calMonth, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
    const prevDays    = new Date(calYear, calMonth, 0).getDate();
    const today       = new Date();
    const isCurrent   = today.getFullYear() === calYear && today.getMonth() === calMonth;
    let html = '';
    for (let i = 0; i < firstDay; i++) {
        html += `<div class="cal-date other">${prevDays - firstDay + 1 + i}</div>`;
    }
    for (let d = 1; d <= daysInMonth; d++) {
        const isToday = isCurrent && d === today.getDate();
        html += `<div class="cal-date${isToday ? ' today' : ''}">${d}</div>`;
    }
    const total = firstDay + daysInMonth;
    const rem   = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let i = 1; i <= rem; i++) {
        html += `<div class="cal-date other">${i}</div>`;
    }
    document.getElementById('calDates').innerHTML = html;
}
function prevMonth() { calMonth--; if (calMonth < 0) { calMonth = 11; calYear--; } renderCalendar(); }
function nextMonth() { calMonth++; if (calMonth > 11) { calMonth = 0; calYear++; } renderCalendar(); }
renderCalendar();

// ── Live clock ────────────────────────────────────────
function updateClock() {
    const now = new Date();
    const hh  = String(now.getHours()).padStart(2,'0');
    const mm  = String(now.getMinutes()).padStart(2,'0');
    const ss  = String(now.getSeconds()).padStart(2,'0');
    const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
    const h12  = now.getHours() % 12 || 12;
    const el   = document.getElementById('punchLiveTime');
    if (el) el.textContent = `${String(h12).padStart(2,'0')}:${mm}:${ss} ${ampm}`;
}
setInterval(updateClock, 1000);
updateClock();

// ── Punch In / Out (localStorage stub – wire to backend later) ─────
const PUNCH_KEY = 'emp_punch_{{ auth()->id() }}_{{ now()->format("Y-m-d") }}';
let punchData   = JSON.parse(localStorage.getItem(PUNCH_KEY) || '{}');

function fmtTime(ts) {
    const d = new Date(ts);
    const h = d.getHours() % 12 || 12;
    const m = String(d.getMinutes()).padStart(2,'0');
    const ampm = d.getHours() >= 12 ? 'PM' : 'AM';
    return `${String(h).padStart(2,'0')}:${m} ${ampm}`;
}

function renderPunchState() {
    const btn   = document.getElementById('punchBtn');
    const lbl   = document.getElementById('punchLabel');
    const icon  = document.getElementById('punchIcon');
    const status = document.getElementById('punchStatus');
    const inEl  = document.getElementById('punchInTime');
    const outEl = document.getElementById('punchOutTime');

    if (punchData.in) {
        inEl.textContent = fmtTime(punchData.in);
    }
    if (punchData.out) {
        outEl.textContent = fmtTime(punchData.out);
        btn.style.background = 'linear-gradient(135deg,#64748b,#94a3b8)';
        btn.disabled = true;
        lbl.textContent = 'Completed';
        icon.className = 'fa-solid fa-check';
        status.innerHTML = '<span style="width:8px;height:8px;background:#16a34a;border-radius:50%;display:inline-block;"></span>&nbsp;Clocked Out';
        status.style.color = 'var(--accent-green)';
    } else if (punchData.in) {
        btn.style.background = 'linear-gradient(135deg,#ef4444,#f87171)';
        btn.style.boxShadow  = '0 4px 14px rgba(239,68,68,.3)';
        lbl.textContent = 'Punch Out';
        icon.className  = 'fa-solid fa-stop';
        status.innerHTML = '<span style="width:8px;height:8px;background:var(--accent-green);border-radius:50%;display:inline-block;"></span>&nbsp;Clocked In';
        status.style.color = 'var(--accent-green)';
    } else {
        status.innerHTML = '<span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;"></span>&nbsp;Not Clocked In';
        status.style.color = '#d97706';
    }
}

function handlePunch() {
    const now = Date.now();
    if (!punchData.in) {
        punchData.in = now;
    } else if (!punchData.out) {
        punchData.out = now;
    }
    localStorage.setItem(PUNCH_KEY, JSON.stringify(punchData));
    renderPunchState();
}

renderPunchState();
</script>
@endpush
