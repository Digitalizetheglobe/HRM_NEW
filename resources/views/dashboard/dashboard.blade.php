    @extends('layouts.admin')

    @section('content')
    <style>
        /* =========================================================
           MODERN SAAS DASHBOARD STYLES (MATCHING COMPANY VIEW)
           ========================================================= */
        :root {
            --dash-primary: #018ac8;
            --dash-primary-dark: #006b9d;
            --dash-primary-light: #e6f4fa;
            --dash-dark: #0f172a;
            --dash-slate: #334155;
            --dash-muted: #64748b;
            --dash-border: #edf2f7;
            --dash-bg: #f8fafc;
            --dash-card-bg: #ffffff;
            --dash-radius: 18px;
            --dash-radius-sm: 12px;
            --dash-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -2px rgba(15, 23, 42, 0.02);
            --dash-shadow-hover: 0 16px 32px -4px rgba(15, 23, 42, 0.1), 0 4px 12px -2px rgba(15, 23, 42, 0.05);
        }

        .dashboard-container {
            padding-bottom: 30px;
        }

        /* Modern Hero Greeting Header */
        .modern-welcome-hero {
            background: #ffffff;
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius);
            padding: 22px 26px;
            margin-bottom: 24px;
            box-shadow: var(--dash-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: relative;
            overflow: hidden;
        }
        .modern-welcome-hero::after {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 180px;
            background: radial-gradient(circle at top right, rgba(1, 138, 200, 0.08), transparent 70%);
            pointer-events: none;
        }
        .welcome-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(1, 138, 200, 0.08);
            color: var(--dash-primary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 6px;
        }
        .welcome-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--dash-dark);
            letter-spacing: -0.4px;
            margin: 0;
            line-height: 1.25;
        }
        .welcome-subtitle {
            font-size: 13px;
            color: var(--dash-muted);
            margin: 4px 0 0 0;
            font-weight: 500;
        }
        .live-date-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--dash-radius-sm);
            font-size: 12px;
            font-weight: 600;
            color: var(--dash-slate);
            flex-shrink: 0;
        }
        .live-date-pill i {
            color: var(--dash-primary);
            font-size: 14px;
        }

        /* Box Cards & Section Headers */
        .modern-box-card {
            background: #ffffff;
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius) !important;
            box-shadow: var(--dash-shadow);
            overflow: hidden;
            margin-bottom: 24px;
            transition: box-shadow 0.25s ease;
        }
        .modern-box-card:hover {
            box-shadow: var(--dash-shadow-hover);
        }
        .modern-box-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: var(--dash-radius) var(--dash-radius) 0 0 !important;
        }
        .modern-box-header .header-left-title {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0;
        }
        .modern-box-header .header-left-title i {
            font-size: 18px;
            color: #38bdf8;
        }
        .modern-box-header .header-count-pill {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            line-height: 1.5;
        }
        .modern-box-header .btn-header-add {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .modern-box-header .btn-header-add:hover {
            background: rgba(255, 255, 255, 0.28);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Employee Profile Info Rows */
        .emp-info-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .emp-info-row:last-child {
            margin-bottom: 0;
        }
        .emp-info-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(1, 138, 200, 0.1);
            color: var(--dash-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .emp-info-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--dash-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .emp-info-value {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--dash-dark);
            word-break: break-word;
        }

        /* Custom Modern Table */
        .modern-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .modern-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 12px 18px;
            border: none;
            border-bottom: 1px solid #edf2f7;
            white-space: nowrap;
        }
        .modern-table tbody td {
            padding: 13px 18px;
            font-size: 13px;
            color: var(--dash-dark);
            border: none;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-weight: 500;
        }
        .modern-table tbody tr {
            transition: background 0.15s ease;
        }
        .modern-table tbody tr:hover {
            background: #f8fafc;
        }
        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Pills */
        .pill-absent {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
            font-weight: 600;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .pill-absent::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ef4444;
        }
        .badge-modern-success {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-modern-warning {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-modern-primary {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        /* List Groups (Projects, Notices) */
        .modern-list-item {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none !important;
            transition: all 0.2s ease;
            position: relative;
        }
        .modern-list-item:last-child {
            border-bottom: none;
        }
        .modern-list-item:hover {
            background: #f8fafc;
            padding-left: 22px;
        }
        .modern-list-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--dash-primary);
            flex-shrink: 0;
            margin-right: 14px;
            transition: transform 0.2s ease;
        }
        .modern-list-item:hover .modern-list-icon {
            transform: scale(1.08);
            background: rgba(1, 138, 200, 0.1);
        }
        .modern-list-chevron {
            color: #cbd5e1;
            font-size: 16px;
            transition: transform 0.2s, color 0.2s;
            flex-shrink: 0;
        }
        .modern-list-item:hover .modern-list-chevron {
            color: var(--dash-primary);
            transform: translateX(3px);
        }

        /* Calendar Styling (Matching Company View) */
        .fc-prev-button, .fc-next-button {
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            font-size: 13px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 50% !important;
            color: var(--dash-slate) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
            transition: all 0.2s ease !important;
        }
        .fc-prev-button:hover, .fc-next-button:hover {
            background: var(--dash-primary) !important;
            color: #ffffff !important;
            border-color: var(--dash-primary) !important;
            transform: scale(1.06);
        }
        .fc .fc-toolbar {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 14px 16px 10px !important;
            margin-bottom: 0.5em !important;
        }
        .fc .fc-toolbar-title {
            font-size: 14px !important;
            margin: 0 !important;
            font-weight: 800 !important;
            color: var(--dash-dark) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        .fc .fc-col-header-cell {
            background: #f8fafc !important;
            padding: 6px 0 !important;
            border-color: #f1f5f9 !important;
        }
        .fc .fc-col-header-cell-cushion {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
        }
        .fc-daygrid-day-number {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: var(--dash-slate) !important;
            padding: 6px !important;
        }
        .fc-day-today {
            background: rgba(1, 138, 200, 0.06) !important;
        }
        .fc-day-today .fc-daygrid-day-number {
            background: var(--dash-primary) !important;
            color: #ffffff !important;
            border-radius: 50% !important;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 2px;
        }
        #calendar {
            margin-bottom: 10px;
        }
        .calendar-navigation {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .fc-daygrid-day.hours-complete,
        .fc-daygrid-day.hours-incomplete {
            background-color: #d4edda !important;
        }
        .fc-daygrid-day.hours-absent {
            background-color: #f8d7da !important;
        }
        .fc-daygrid-day.hours-weekoff {
            background-color: #e9ecef !important;
        }
        .fc-daygrid-day.hours-leave {
            background-color: #fff3cd !important;
        }
        .fc-daygrid-day.hours-holiday {
            background-color: #cce5ff !important;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .modern-welcome-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }
        }
        @media (max-width: 576px) {
            .modern-welcome-hero { padding: 16px 18px; }
            .welcome-title { font-size: 20px; }
        }

        /* Event Card */
        .events-card {
            border: 1px solid var(--dash-border);
            border-radius: var(--dash-radius) !important;
            box-shadow: var(--dash-shadow) !important;
            overflow: hidden;
            background: #ffffff;
        }
        .events-scroll-body::-webkit-scrollbar { width: 4px; }
        .events-scroll-body::-webkit-scrollbar-track { background: #f8fafc; }
        .events-scroll-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .ev-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .ev-row:last-child { border-bottom: none; }
        .ev-row:hover { background: #f8fafc; }
        .ev-row:hover .ev-icon { transform: scale(1.08); box-shadow: 0 5px 16px rgba(0,0,0,0.18) !important; }
        .ev-row:hover .ev-arrow { transform: translateX(3px); color: var(--dash-primary) !important; }

        .ev-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.13);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .ev-icon i { color: #fff; font-size: 16px; }

        .ev-info { flex: 1; overflow: hidden; min-width: 0; }
        .ev-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--dash-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .ev-today-badge {
            font-size: 9px;
            font-weight: 700;
            background: linear-gradient(135deg,#f6d365,#fda085);
            color: #fff;
            padding: 1px 6px;
            border-radius: 20px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .ev-meta { display: flex; align-items: center; gap: 7px; margin-top: 3px; }
        .ev-tag {
            font-size: 10px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .ev-date { font-size: 10px; color: #94a3b8; white-space: nowrap; }
        .ev-arrow { color: #cbd5e1; font-size: 14px; flex-shrink: 0; transition: transform 0.18s, color 0.18s; }

        .ev-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 36px 16px;
            gap: 10px;
        }
        .ev-empty-icon {
            width: 50px; height: 50px;
            background: #f1f5f9;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
        }
        .ev-empty-icon i { font-size: 24px; color: #94a3b8; }
        .ev-empty p { color: #94a3b8; font-size: 12px; font-weight: 500; margin: 0; }

        .ev-avatar-wrap {
            position: relative;
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 50%;
            padding: 2px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .ev-row:hover .ev-avatar-wrap { transform: scale(1.08); }
        .ev-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            display: block;
        }
        .ev-avatar-badge {
            position: absolute;
            bottom: -1px;
            right: -2px;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            background: #fff;
            border: 1.5px solid #e8e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
        }
        .ev-avatar-badge i { font-size: 9px; color: #555; }
    </style>

    <div class="dashboard-container">
        <div class="row">
            @if (session('status'))
                <div class="col-12">
                    <div class="alert alert-success border-0 shadow-sm" style="border-radius: 12px;" role="alert">
                        <i class="ti ti-check me-2"></i> {{ session('status') }}
                    </div>
                </div>
            @endif

            @if (\Auth::user()->type == 'employee')
                {{-- Modern Welcome Hero --}}
                <div class="col-12">
                    <div class="modern-welcome-hero">
                        <div>
                            <div class="welcome-pill-badge">
                                <i class="ti ti-user-check"></i> {{ __('Employee Dashboard') }}
                            </div>
                            <h2 class="welcome-title">{{ __('Welcome') }}, {{ $emp->full_name ?? Auth::user()->name }} 👋</h2>
                            <p class="welcome-subtitle">{{ __('Track your attendance, assigned projects, daily schedules and team updates.') }}</p>
                        </div>
                        <div class="live-date-pill d-none d-md-flex">
                            <i class="ti ti-calendar"></i>
                            <span>{{ \Carbon\Carbon::now()->format('l, d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-9">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                @if($emp)
                                <div class="col-xl-6 mb-4">
                                    <div class="modern-box-card h-100 mb-0">
                                        <div class="modern-box-header">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ asset('storage/uploads/avatar/' . ($emp->user->avatar ?? 'default-avatar.png')) }}"
                                                    alt="Profile Image"
                                                    class="rounded-circle"
                                                    style="object-fit: cover; border: 2px solid rgba(56, 189, 248, 0.7);"
                                                    width="54"
                                                    height="54">
                                                <div>
                                                    <h5 class="mb-0 text-white fw-bold" style="font-size: 16px;">{{ $emp->full_name }}</h5>
                                                    <small style="font-size: 12px; color: rgba(255,255,255,0.75);">
                                                        {{ $emp->department->name ?? 'No Department' }} &bull; {{ $emp->designation->name ?? 'No Designation' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body" style="padding: 20px 22px;">
                                            <div class="emp-info-row">
                                                <div class="emp-info-icon"><i class="ti ti-phone"></i></div>
                                                <div>
                                                    <div class="emp-info-label">{{ __('Phone Number') }}</div>
                                                    <div class="emp-info-value">{{ $emp->phone ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="emp-info-row">
                                                <div class="emp-info-icon"><i class="ti ti-mail"></i></div>
                                                <div>
                                                    <div class="emp-info-label">{{ __('Email Address') }}</div>
                                                    <div class="emp-info-value">{{ $emp->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="emp-info-row">
                                                <div class="emp-info-icon"><i class="ti ti-calendar"></i></div>
                                                <div>
                                                    <div class="emp-info-label">{{ __('Joined On') }}</div>
                                                    <div class="emp-info-value">{{ \Carbon\Carbon::parse($emp->company_doj)->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-6 mb-4">
                                    <div class="modern-box-card h-100 mb-0">
                                        <div class="modern-box-header">
                                            <h5 class="header-left-title">
                                                <i class="ti ti-clock"></i>
                                                <span>{{ __('Attendance Overview') }}</span>
                                            </h5>
                                            <div class="dropdown">
                                                <button class="btn btn-sm dropdown-toggle" type="button" id="attendanceFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:rgba(255,255,255,0.16); color:#fff; border:1px solid rgba(255,255,255,0.2); border-radius:20px; font-size:12px; font-weight:600; padding:4px 12px; box-shadow:none;">
                                                    <span id="selectedFilterText">Today</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="attendanceFilterDropdown">
                                                    <li><a class="dropdown-item attendance-filter-option active" href="javascript:void(0)" data-filter="today">
                                                        <i class="fas fa-calendar-day me-2"></i>Today
                                                    </a></li>
                                                    <li><a class="dropdown-item attendance-filter-option" href="javascript:void(0)" data-filter="date">
                                                        <i class="fas fa-calendar me-2"></i>Select Date
                                                    </a></li>
                                                    <li><a class="dropdown-item attendance-filter-option" href="javascript:void(0)" data-filter="weekly">
                                                        <i class="fas fa-calendar-week me-2"></i>Weekly
                                                    </a></li>
                                                    <li><a class="dropdown-item attendance-filter-option" href="javascript:void(0)" data-filter="monthly">
                                                        <i class="fas fa-calendar-alt me-2"></i>Monthly
                                                    </a></li>
                                                </ul>
                                                <input type="date" id="attendanceDatePicker" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="handleDateSelect(this.value)" oninput="handleDateSelect(this.value)">
                                                <input type="month" id="attendanceMonthPicker" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;" onchange="handleMonthSelect(this.value)" oninput="handleMonthSelect(this.value)">
                                            </div>
                                        </div>
                                        <div class="card-body" style="padding: 20px 22px;">

                                            <!-- Punch In/Out Section - Premium Redesign -->
                                            <div id="punchInOutSection" class="mb-3">

                                                {{-- Status Pill --}}
                                                <div class="text-center mb-3">
                                                @if (!isset($employeeAttendance) || !$employeeAttendance->clock_in)
                                                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);color:#4338ca;font-weight:600;font-size:13px;">
                                                        <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#818cf8;"></span>
                                                        <i class="fas fa-fingerprint me-1"></i> Not Punched In Yet
                                                    </div>
                                                @elseif ($employeeAttendance->clock_out == '00:00:00' || !$employeeAttendance->clock_out)
                                                    @php
                                                        $isLate = false;
                                                        if ($employeeAttendance->clock_in && $employeeAttendance->clock_in != '00:00:00') {
                                                            $employee = \App\Models\Employee::find($employeeAttendance->employee_id);
                                                            if ($employee) {
                                                                $shift = $employee->getShiftTimings($employeeAttendance->date);
                                                                $expectedStartTime = \Carbon\Carbon::parse($employeeAttendance->date . ' ' . $shift['start_time']);
                                                                $graceTime = $expectedStartTime->copy()->addMinutes(10);
                                                                $clockInTime = \Carbon\Carbon::parse($employeeAttendance->date . ' ' . $employeeAttendance->clock_in);
                                                                $isLate = $clockInTime->gt($graceTime);
                                                            } else {
                                                                $companyStartTime = \App\Models\Utility::getValByName('company_start_time');
                                                                if ($companyStartTime) {
                                                                    $clockInTime = \Carbon\Carbon::parse($employeeAttendance->date . ' ' . $employeeAttendance->clock_in);
                                                                    $expectedStartTime = \Carbon\Carbon::parse($employeeAttendance->date . ' ' . $companyStartTime);
                                                                    if (\Carbon\Carbon::parse($employeeAttendance->date)->isSaturday()) {
                                                                        $expectedStartTime = \Carbon\Carbon::parse($employeeAttendance->date . ' 11:00:00');
                                                                    }
                                                                    $isLate = $clockInTime->gt($expectedStartTime);
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if($isLate)
                                                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background:linear-gradient(135deg,#fff1f2,#ffe4e6);color:#dc2626;font-weight:600;font-size:13px;">
                                                        <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#f87171;animation:pulse-dot 1.4s infinite;"></span>
                                                        <i class="fas fa-fingerprint me-1"></i> Punched In at {{ \Carbon\Carbon::parse($employeeAttendance->clock_in)->format('h:i A') }} &nbsp;<span class="badge" style="background:#dc2626;font-size:10px;">Late</span>
                                                    </div>
                                                    @else
                                                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);color:#16a34a;font-weight:600;font-size:13px;">
                                                        <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#4ade80;animation:pulse-dot 1.4s infinite;"></span>
                                                        <i class="fas fa-fingerprint me-1"></i> Punched In at {{ \Carbon\Carbon::parse($employeeAttendance->clock_in)->format('h:i A') }}
                                                    </div>
                                                    @endif
                                                @else
                                                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);color:#475569;font-weight:600;font-size:13px;">
                                                        <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#94a3b8;"></span>
                                                        <i class="fas fa-sign-out-alt me-1"></i> Punched Out at {{ \Carbon\Carbon::parse($employeeAttendance->clock_out)->format('h:i A') }}
                                                    </div>
                                                @endif
                                                </div>

                                                {{-- Hidden status p for JS compatibility --}}
                                                <p id="attendanceStatus" style="display:none;"></p>

                                                {{-- Punch Button --}}
                                                {{ Form::open(['url' => 'attendanceemployee/attendance', 'method' => 'post', 'id' => 'attendanceForm']) }}
                                                <div class="text-center">
                                                    @if (empty($employeeAttendance) || $employeeAttendance->clock_out != '00:00:00')
                                                    <button type="submit" value="0" name="in" id="clock_in" class="btn btn-lg px-5 fw-bold"
                                                        style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:12px;font-size:15px;box-shadow:0 4px 15px rgba(99,102,241,0.4);transition:all 0.2s;"
                                                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(99,102,241,0.5)';"
                                                        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(99,102,241,0.4)';">
                                                        <i class="fas fa-fingerprint me-2"></i>{{ __('Punch In') }}
                                                    </button>
                                                    @else
                                                    <button type="button" value="1" name="out" id="clock_out" class="btn btn-lg px-5 fw-bold"
                                                        style="background:linear-gradient(135deg,#f43f5e,#ec4899);color:#fff;border:none;border-radius:12px;font-size:15px;box-shadow:0 4px 15px rgba(244,63,94,0.4);transition:all 0.2s;"
                                                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(244,63,94,0.5)';"
                                                        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(244,63,94,0.4)';"
                                                        data-bs-toggle="modal" data-bs-target="#confirmClockOutModal">
                                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Punch Out') }}
                                                    </button>
                                                    @endif
                                                </div>
                                                {{ Form::close() }}

                                                {{-- Update Reminder (shown by JS on punch-out fail) --}}
                                                <div id="gpsMessage" class="d-none mt-3 p-3 rounded-3 d-flex align-items-center justify-content-between gap-3" role="alert"
                                                     style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fcd34d;color:#92400e;font-size:13px;font-weight:500;">
                                                </div>
                                            </div>

                                            <!-- Separator -->
                                            <div style="height:1px;background:linear-gradient(to right,transparent,#e2e8f0,transparent);margin:4px 0 16px;"></div>

                                            <!-- Attendance Overview Content -->
                                            <div class="text-center py-4" id="attendanceLoading" style="display: none;">
                                                <div class="spinner-border" role="status" style="color:#6366f1;">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <div id="attendanceOverviewContent">
                                                <!-- Content will be loaded here -->
                                            </div>
                                        </div>

                                        <style>
                                        @keyframes pulse-dot {
                                            0%,100% { opacity:1; transform:scale(1); }
                                            50% { opacity:0.5; transform:scale(1.3); }
                                        }
                                        #gpsMessage a.btn {
                                            font-size: 12px !important;
                                            padding: 4px 14px !important;
                                            border-radius: 8px !important;
                                            background: linear-gradient(135deg,#6366f1,#8b5cf6) !important;
                                            color:#fff !important;
                                            border: none !important;
                                            font-weight: 600 !important;
                                            white-space: nowrap;
                                            box-shadow: 0 2px 8px rgba(99,102,241,0.35);
                                        }
                                        </style>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Project Details -->
                        <div class="col-xl-12">
                            <div class="modern-box-card">
                                <div class="modern-box-header">
                                    <h5 class="header-left-title">
                                        <i class="ti ti-briefcase"></i>
                                        <span>{{ __('Project Details') }}</span>
                                    </h5>
                                    <span class="header-count-pill">{{ count($assignedProjects ?? []) }}</span>
                                </div>
                                <div class="card-body p-0" style="height: 325px; overflow-y: auto; overflow-x: hidden;">
                                    <div class="list-group list-group-flush">
                                        @forelse ($assignedProjects ?? [] as $project)
                                            @php
                                                // Find modules assigned to this employee in the project
                                                $empModules = collect($project->modules)->filter(function($module) use ($emp) {
                                                    return in_array((string)$emp->id, $module->employee_ids ?? []);
                                                });
                                                $moduleNames = $empModules->pluck('module_name')->toArray();
                                                $modulesText = count($moduleNames) > 0 ? implode(', ', $moduleNames) : __('No specific tasks assigned');
                                                
                                                if($project->status == 'completed') {
                                                    $statusClass = 'badge-modern-success';
                                                    $statusText = 'Completed';
                                                } elseif($project->status == 'on_hold') {
                                                    $statusClass = 'badge-modern-warning';
                                                    $statusText = 'On Hold';
                                                } else {
                                                    $statusClass = 'badge-modern-primary';
                                                    $statusText = 'In Progress';
                                                }
                                            @endphp
                                            <a href="{{ route('projects.show', $project->id) }}" class="modern-list-item">
                                                <div class="modern-list-icon">
                                                    <i class="ti ti-briefcase"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden pe-3">
                                                    <h6 class="mb-1 text-dark fw-bold text-truncate" style="font-size: 14px;">{{ $project->project_name }}</h6>
                                                    <p class="text-muted mb-0 text-truncate" style="font-size: 12px; margin-bottom:0;">
                                                        <span class="{{ $statusClass }} me-1">{{ $statusText }}</span>
                                                        <i class="ti ti-calendar ms-1 me-1"></i> {{ \Carbon\Carbon::parse($project->project_startdate)->format('d M') }} - {{ \Carbon\Carbon::parse($project->project_enddate)->format('d M Y') }}
                                                        <span class="ms-2"><i class="ti ti-list-check me-1"></i> {{ $modulesText }}</span>
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-chevron-right modern-list-chevron"></i>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center h-100">
                                                <i class="ti ti-briefcase mb-3" style="font-size: 48px; opacity: 0.3;"></i>
                                                <p class="mb-0 fw-semibold">{{ __('No projects assigned') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="modern-box-card">
                                        <div class="modern-box-header">
                                            <h5 class="header-left-title">
                                                <i class="ti ti-bell"></i>
                                                <span>{{ __('Notices') }}</span>
                                            </h5>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="header-count-pill">{{ count($notices) }}</span>
                                                @if(\Auth::user()->type == 'company' || \Auth::user()->type == 'hr')
                                                <a href="#" data-url="{{ route('notices.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Notice') }}" class="btn-header-add">
                                                    <i class="ti ti-plus"></i><span>{{ __('Add') }}</span>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-0" style="height: 325px; overflow-y: auto; overflow-x: hidden;">
                                            <div class="list-group list-group-flush">
                                                @forelse($notices as $notice)
                                                <a href="#" 
                                                   class="modern-list-item"
                                                   data-url="{{ route('notices.show', $notice->id) }}" 
                                                   data-ajax-popup="true" 
                                                   data-size="lg" 
                                                   data-title="{{ __('Notice Details') }}">
                                                    <div class="modern-list-icon">
                                                        <i class="ti ti-bell"></i>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden pe-3">
                                                        <h6 class="mb-1 text-dark fw-bold text-truncate" style="font-size: 14px;">{{ $notice->title }}</h6>
                                                        <p class="text-muted mb-0 text-truncate" style="font-size: 12px; margin-bottom:0;">{{ strip_tags($notice->description) }}</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <i class="ti ti-chevron-right modern-list-chevron"></i>
                                                    </div>
                                                </a>
                                                @empty
                                                <div class="p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center h-100">
                                                    <i class="ti ti-bell-z mb-3" style="font-size: 48px; opacity: 0.3;"></i>
                                                    <p class="mb-0 fw-semibold">{{ __('No recent notices.') }}</p>
                                                </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="modern-box-card">
                                        <div class="modern-box-header">
                                            <h5 class="header-left-title">
                                                <i class="ti ti-calendar-time"></i>
                                                <span id="scheduleFilterTitle">{{ __('Today\'s Schedules') }}</span>
                                            </h5>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm dropdown-toggle" type="button" id="scheduleFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:rgba(255,255,255,0.16); color:#fff; border:1px solid rgba(255,255,255,0.2); border-radius:20px; font-size:12px; font-weight:600; padding:4px 12px; box-shadow:none;">
                                                        <span id="selectedScheduleFilterText">Today</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="scheduleFilterDropdown">
                                                        <li><a class="dropdown-item schedule-filter-option active" href="javascript:void(0)" data-filter="today"><i class="fas fa-calendar-day me-2"></i>Today</a></li>
                                                        <li><a class="dropdown-item schedule-filter-option" href="javascript:void(0)" data-filter="yesterday"><i class="fas fa-calendar-minus me-2"></i>Yesterday</a></li>
                                                        <li><a class="dropdown-item schedule-filter-option" href="javascript:void(0)" data-filter="custom"><i class="fas fa-calendar me-2"></i>Select Date</a></li>
                                                    </ul>
                                                    <input type="date" id="scheduleCustomDatePicker" style="position: absolute; opacity: 0; width: 1px; height: 1px; top: 0; left: 0; border: none; padding: 0;" onchange="handleScheduleDateSelect(this.value)">
                                                </div>
                                                <button type="button" class="btn-header-add" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                                    <i class="ti ti-plus"></i><span>{{ __('Add') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="padding: 10px; height: 325px; overflow-y: auto; overflow-x: hidden;">
                                            <div id="schedulesContainer">
                                                @include('dashboard.schedules_list')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="modern-box-card">
                                <div class="modern-box-header">
                                    <h5 class="header-left-title">
                                        <i class="ti ti-user-off"></i>
                                        <span>{{ __('Employees On Leave / WeekOff') }}</span>
                                    </h5>
                                    <span class="header-count-pill">{{ count($employeesNotWorkingToday) }}</span>
                                </div>
                                <div class="card-body p-0" style="height: 300px; overflow: auto;">
                                    <div class="table-responsive">
                                        <table class="table modern-table" id="onLeaveTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Employee Name') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($employeesNotWorkingToday as $employee)
                                                    <tr>
                                                        <td class="fw-semibold">{{ $employee['employee_name'] }}</td>
                                                        <td><span class="pill-absent">{{ $employee['status'] }}</span></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted py-4">All employees are working today</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side Calendar -->
                <div class="col-xxl-3" style="z-index: 0;">
                    <div class="d-flex flex-column gap-2 sticky-top" style="">
                        <div class="modern-box-card events-card">
                            {{-- Header --}}
                            <div class="modern-box-header">
                                <h5 class="header-left-title">
                                    <i class="ti ti-calendar-event"></i>
                                    <span>{{ __("Upcoming Events") }}</span>
                                </h5>
                                @if(count($allEvents) > 0)
                                <span class="header-count-pill">{{ count($allEvents) }}</span>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div style="max-height:300px;overflow-y:auto;overflow-x:hidden;" class="events-scroll-body">
                                @if(count($allEvents) > 0)
                                    @foreach($allEvents as $event)
                                        @php
                                            $empName    = $event['employee_name'] ?? '';
                                            $empId      = $event['employee_id'] ?? '';
                                            $eventType  = $event['type'] ?? 'event';
                                            $eventDate  = $event['start'] ?? '';
                                            $years      = $event['years'] ?? null;
                                            $title      = $event['title'] ?? '';
                                            $avatar     = $event['employee_avatar'] ?? null;
                                            $isToday    = \Carbon\Carbon::parse($eventDate)->isToday();
                                            $parsedDate = \Carbon\Carbon::parse($eventDate);

                                            if ($eventType === 'birthday') {
                                                $icon   = 'fa-solid fa-cake-candles';
                                                $iconBg = '#f093fb,#f5576c';
                                                $ring   = 'linear-gradient(135deg,#f093fb,#f5576c)';
                                                $tag    = ['label'=>'Birthday','color'=>'#f5576c','bg'=>'rgba(245,87,108,0.1)'];
                                            } elseif ($eventType === 'anniversary') {
                                                $icon   = 'fa-solid fa-medal';
                                                $iconBg = '#4facfe,#00f2fe';
                                                $ring   = 'linear-gradient(135deg,#4facfe,#00f2fe)';
                                                $tag    = ['label'=>'Anniversary','color'=>'#4facfe','bg'=>'rgba(79,172,254,0.1)'];
                                            } else {
                                                $icon   = 'fa-solid fa-calendar-day';
                                                $iconBg = '#43e97b,#38f9d7';
                                                $ring   = 'linear-gradient(135deg,#43e97b,#38f9d7)';
                                                $tag    = ['label'=>'Event','color'=>'#1db954','bg'=>'rgba(29,185,84,0.1)'];
                                            }
                                        @endphp
                                        <div class="ev-row event-popup-trigger"
                                            data-title="{{ $title }}"
                                            data-type="{{ $eventType }}"
                                            data-name="{{ $empName }}"
                                            data-id="{{ $empId }}"
                                            data-date="{{ $eventDate }}"
                                            data-years="{{ $years }}">

                                            {{-- Avatar or Event Icon --}}
                                            @if($avatar && ($eventType === 'birthday' || $eventType === 'anniversary'))
                                                <div class="ev-avatar-wrap" style="background:{{ $ring }};">
                                                    <img src="{{ $avatar }}" alt="{{ $empName }}"
                                                        onerror="this.parentElement.innerHTML='<div class=\'ev-icon\' style=\'background:linear-gradient(135deg,{{ $iconBg }})\'><i class=\'{{ $icon }}\'></i></div>'"
                                                        class="ev-avatar-img">
                                                </div>
                                            @else
                                                <div class="ev-icon" style="background:linear-gradient(135deg,{{ $iconBg }});">
                                                    <i class="{{ $icon }}"></i>
                                                </div>
                                            @endif

                                            {{-- Text --}}
                                            <div class="ev-info">
                                                <div class="ev-title" title="{{ $title }}">
                                                    {{ Str::limit($title, 22) }}
                                                    @if($isToday)
                                                        <span class="ev-today-badge">Today</span>
                                                    @endif
                                                </div>
                                                <div class="ev-meta">
                                                    <span class="ev-tag" style="color:{{ $tag['color'] }};background:{{ $tag['bg'] }};">{{ $tag['label'] }}</span>
                                                    <span class="ev-date"><i class="ti ti-clock" style="font-size:9px;margin-right:2px;"></i>{{ $parsedDate->format('D, M d') }}</span>
                                                </div>
                                            </div>

                                            {{-- Chevron --}}
                                            <i class="ti ti-chevron-right ev-arrow"></i>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="ev-empty">
                                        <div class="ev-empty-icon"><i class="ti ti-calendar-off"></i></div>
                                        <p>No upcoming events this month</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="modern-box-card flex-grow-1">
                            <div class="modern-box-header">
                                <h5 class="header-left-title">
                                    <i class="ti ti-calendar"></i>
                                    <span>{{ __('Calendar') }}</span>
                                </h5>
                            </div>
                            <div class="card-body" style="padding:14px;">
                                <div id='calendar' class='calendar'></div>
                            </div>
                        </div>


                    </div>
                </div>
        @endif
        </div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="confirmClockOutModal" tabindex="-1" aria-labelledby="confirmClockOutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmClockOutModalLabel">Confirm Clock Out</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to clock out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmClockOutBtn">
                        Yes, Clock Out
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none; padding: 20px 25px;">
                    <h5 class="modal-title text-white d-flex align-items-center" id="addScheduleModalLabel">
                        <i class="ti ti-calendar-plus me-2" style="font-size: 22px; color:#38bdf8;"></i>{{ __('Add Today\'s Task') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <form action="{{ route('todolist.store') }}" method="POST" id="addScheduleForm" onsubmit="if(this.description.value) { this.task.value = this.task.value + '\n\n' + this.description.value; }">
                        @csrf
                        <input type="hidden" name="is_completed" value="0">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Task Title') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" name="task" required placeholder="{{ __('Enter task title...') }}" style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control shadow-sm" name="start_date" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control shadow-sm" name="end_date" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Priority') }} <span class="text-danger">*</span></label>
                                <select class="form-select shadow-sm" name="priority" required style="border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px;">
                                    <option value="" disabled selected>{{ __('Select Priority') }}</option>
                                    <option value="high">{{ __('High') }}</option>
                                    <option value="medium">{{ __('Medium') }}</option>
                                    <option value="low">{{ __('Low') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold" style="color: #495057; font-size: 13px; font-weight: 600;">{{ __('Description') }} <span class="text-muted fw-normal">({{ __('Optional') }})</span></label>
                                <textarea class="form-control shadow-sm" name="description" rows="3" placeholder="{{ __('Enter task details here...') }}" style="border-radius: 8px; border: 1px solid #ced4da; padding: 12px 15px; resize: none;"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 20px;">{{ __('Cancel') }}</button>
                    <button type="submit" form="addScheduleForm" class="btn btn-primary" style="border-radius: 10px; background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%); border: none; font-weight: 600; padding: 8px 20px; box-shadow: 0 4px 12px rgba(1, 138, 200, 0.3);">{{ __('Save Task') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern Event Detail Popup Modal --}}
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px; width: 92%; margin: 1.75rem auto;">
            <div class="modal-content" style="border:none; border-radius:20px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                {{-- Gradient Header --}}
                <div id="eventModalHeader" style="padding: 32px 28px 22px; text-align:center; position:relative;">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute; top:14px; right:18px; opacity:0.5;"></button>
                    <div id="eventModalEmoji" style="font-size:52px; line-height:1; margin-bottom:10px;"></div>
                    <div id="eventModalBadge" style="display:inline-block; padding:4px 16px; border-radius:30px; font-size:12px; font-weight:700; letter-spacing:1px; margin-bottom:12px;"></div>
                    <h4 id="eventModalGreeting" style="color:#fff; font-weight:800; font-size:22px; margin:0; line-height:1.3;"></h4>
                </div>
                {{-- Body --}}
                <div class="modal-body" style="padding: 24px 28px 28px; background:#fff;">
                    <div style="display:flex; flex-direction:column; gap:14px;">
                        <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:#f8f9ff; border-radius:12px;">
                            <div style="width:42px; height:42px; border-radius:50%; background:var(--bs-primary, #018ac8); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="ti ti-user" style="color:#fff; font-size:18px;"></i>
                            </div>
                            <div>
                                <div style="font-size:11px; color:#888; font-weight:600; letter-spacing:0.5px; text-transform:uppercase;">Employee</div>
                                <div id="eventModalName" style="font-size:16px; font-weight:700; color:#222;"></div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:#f8f9ff; border-radius:12px;">
                            <div style="width:42px; height:42px; border-radius:50%; background:var(--bs-primary, #018ac8); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="ti ti-calendar" style="color:#fff; font-size:18px;"></i>
                            </div>
                            <div>
                                <div style="font-size:11px; color:#888; font-weight:600; letter-spacing:0.5px; text-transform:uppercase;">Date</div>
                                <div id="eventModalDate" style="font-size:16px; font-weight:700; color:#222;"></div>
                            </div>
                        </div>
                        <div id="eventModalYearsRow" style="display:none; align-items:center; gap:14px; padding:14px 16px; background:#f8f9ff; border-radius:12px;">
                            <div style="width:42px; height:42px; border-radius:50%; background:var(--bs-primary, #018ac8); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="ti ti-award" style="color:#fff; font-size:18px;"></i>
                            </div>
                            <div>
                                <div style="font-size:11px; color:#888; font-weight:600; letter-spacing:0.5px; text-transform:uppercase;">Years Completed</div>
                                <div id="eventModalYears" style="font-size:16px; font-weight:700; color:#222;"></div>
                            </div>
                        </div>
                        
                        {{-- Action Buttons (Only shown for today's birthdays/anniversaries) --}}
                        <div id="wishesActionContainer" style="display:none; gap:10px; margin-top:10px; flex-direction:column;">
                            <button type="button" class="btn w-100" id="btnSendAutoWishes" style="background:var(--bs-primary, #018ac8); color:#fff; border-radius:10px; padding:10px; font-weight:600;" onclick="sendWishes('auto')">
                                <i class="ti ti-send me-2"></i> Send Wishes
                            </button>
                            <button type="button" class="btn w-100 btn-outline-primary" id="btnOpenCustomWishes" style="border-radius:10px; padding:10px; font-weight:600;" onclick="openCustomWishes()">
                                <i class="ti ti-edit me-2"></i> Send Custom Wishes
                            </button>
                        </div>

                        {{-- Custom Message Form --}}
                        <div id="customWishesForm" style="display:none; margin-top:10px; flex-direction:column;">
                            <label style="font-size:12px; font-weight:600; color:#555; margin-bottom:6px;">Your Custom Message:</label>
                            <textarea id="customWishesMessage" class="form-control" rows="3" placeholder="Write your personalized wishes here..." style="border-radius:10px; resize:none; margin-bottom:12px; box-shadow:none; border:1px solid #ddd;"></textarea>
                            <div style="display:flex; gap:10px;">
                                <button type="button" class="btn w-50 btn-light" style="border-radius:10px; font-weight:600;" onclick="closeCustomWishes()">Cancel</button>
                                <button type="button" class="btn w-50" id="btnSendCustomWishes" style="background:var(--bs-primary, #018ac8); color:#fff; border-radius:10px; font-weight:600;" onclick="sendWishes('custom')">
                                    <i class="ti ti-send me-2"></i> Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('script-page')
        <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

        @if (Auth::user()->type == 'employee')
        <script type="text/javascript">
        $(document).ready(function() {
            get_data();
        });

        // Function to calculate total hours worked in decimal format
        function calculateTotalHours(clockIn, clockOut) {
            if (!clockOut || clockOut === '00:00:00' || clockOut === '00:00') {
                return 0;
            }
            
            if (!clockIn || clockIn === '00:00:00' || clockIn === '00:00') {
                return 0;
            }
            
            // Parse times
            var inParts = clockIn.split(':');
            var outParts = clockOut.split(':');
            
            var inHours = parseInt(inParts[0]);
            var inMinutes = parseInt(inParts[1]);
            var outHours = parseInt(outParts[0]);
            var outMinutes = parseInt(outParts[1]);
            
            if (isNaN(inHours) || isNaN(inMinutes) || isNaN(outHours) || isNaN(outMinutes)) {
                return 0;
            }
            
            var inTotalMinutes = inHours * 60 + inMinutes;
            var outTotalMinutes = outHours * 60 + outMinutes;
            var diffMinutes = outTotalMinutes - inTotalMinutes;
            
            if (diffMinutes < 0) {
                diffMinutes = (24 * 60) - inTotalMinutes + outTotalMinutes;
            }
            
            return diffMinutes / 60;
        }

        function get_data() {
            var calender_type = $('#calender_type :selected').val();

            $('#calendar').removeClass('local_calender google_calender');
            if (!calender_type) {
                calender_type = 'local_calender';
            }
            $('#calendar').addClass(calender_type);

            $.ajax({
                url: '{{ route('event.get_event_data') }}',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'calender_type': calender_type
                },
                success: function(data) {
                    @if(isset($emp) && isset($attendanceData))
                        var attendanceData = @json($attendanceData);
                        var employeeId = {{ $emp->id }};
                    @else
                        var attendanceData = {};
                        var employeeId = null;
                    @endif
                    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                        headerToolbar: {
                            left: 'prev',
                            center: 'title',
                            right: 'next'
                        },
                        themeSystem: 'bootstrap',
                        slotDuration: '00:10:00',
                        allDaySlot: true,
                        navLinks: false,
                        droppable: true,
                        selectable: true,
                        selectMirror: true,
                        dayMaxEvents: true,
                        handleWindowResize: true,
                        height: '360px',
                        showNonCurrentDates: false,
                        events: data,
                        dayCellDidMount: function(info) {
                            if (employeeId && attendanceData[employeeId] && attendanceData[employeeId].data) {
                                var year = info.date.getFullYear();
                                var month = String(info.date.getMonth() + 1).padStart(2, '0');
                                var day = String(info.date.getDate()).padStart(2, '0');
                                var dateStr = year + '-' + month + '-' + day;
                                var dayData = attendanceData[employeeId].data[dateStr];
                                
                                if (dayData) {
                                    if (dayData.type === 'present') {
                                        var clockIn = dayData.clock_in;
                                        var clockOut = dayData.clock_out;
                                        
                                        if (!clockOut || clockOut === '00:00:00' || clockOut === '00:00') {
                                            info.el.classList.add('hours-incomplete');
                                        } else {
                                            var dayOfWeek = info.date.getDay();
                                            var totalHours = calculateTotalHours(clockIn, clockOut);
                                            var requiredHours = (dayOfWeek === 6) ? 4 : 8; // Sat = 4, M-F = 8
                                            
                                            if (totalHours >= requiredHours) {
                                                info.el.classList.add('hours-complete');
                                            } else {
                                                info.el.classList.add('hours-incomplete');
                                            }
                                        }
                                    } else if (dayData.type === 'absent') {
                                        info.el.classList.add('hours-absent');
                                    } else if (dayData.type === 'week_off') {
                                        info.el.classList.add('hours-weekoff');
                                    } else if (dayData.type === 'leave') {
                                        info.el.classList.add('hours-leave');
                                    } else if (dayData.type === 'holiday') {
                                        info.el.classList.add('hours-holiday');
                                    }
                                }
                            }
                        }
                    });
                    calendar.render();
                }
            });
        }
        </script>
        @endif

    @endpush

    @push('script-page')
    <script>
    document.addEventListener("DOMContentLoaded", function () {

        let isPunchInProgress = false;
        let currentAttendanceId = null;

        const clockInBtn  = document.getElementById("clock_in");
        const clockOutBtn = document.getElementById("clock_out");
        const confirmClockOutBtn = document.getElementById("confirmClockOutBtn");

        if (confirmClockOutBtn) {
            confirmClockOutBtn.addEventListener("click", function () {
                // Close modal first
                const modalEl = document.getElementById('confirmClockOutModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Submit punch-out immediately
                submitAttendance('out');
            });
        }

        let cachedCoords = null;
        let cachedCoordsAt = 0;

        // Pre-fetch and keep GPS warm in background so Punch In/Out is instant with no timeout
        if (navigator.geolocation) {
            const updateCachedCoords = (position) => {
                cachedCoords = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                cachedCoordsAt = Date.now();
            };

            navigator.geolocation.getCurrentPosition(
                updateCachedCoords,
                () => {},
                { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 }
            );

            navigator.geolocation.watchPosition(
                updateCachedCoords,
                () => {},
                { enableHighAccuracy: false, maximumAge: 300000 }
            );
        }

        /**
         * Get current browser GPS coordinates for office geofence verification (instant if cached)
         */
        function getCurrentCoordinates() {
            if (cachedCoords && (Date.now() - cachedCoordsAt) < 300000) {
                return Promise.resolve(cachedCoords);
            }
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('Geolocation is not supported by your browser.'));
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        cachedCoords = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };
                        cachedCoordsAt = Date.now();
                        resolve(cachedCoords);
                    },
                    (err) => {
                        if (cachedCoords) {
                            resolve(cachedCoords);
                            return;
                        }
                        if (err.code === err.PERMISSION_DENIED) {
                            reject(new Error('Location permission denied. Please allow location access in your browser to Punch In / Punch Out.'));
                        } else {
                            reject(new Error('Unable to detect your location. Please enable GPS and try again.'));
                        }
                    },
                    { enableHighAccuracy: false, timeout: 12000, maximumAge: 300000 }
                );
            });
        }

        /**
         * Submit attendance - requires office location verification
         */
        async function submitAttendance(type) {
            if (isPunchInProgress) return;
            isPunchInProgress = true;

            const btn = type === 'in' ? clockInBtn : clockOutBtn;
            const originalBtnText = btn.innerHTML;

            // Disable button and show loading state
            btn.disabled = true;
            if (type === 'in') {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Punching In...';
            } else {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Punching Out...';
            }

            clearMessage();
            if (!cachedCoords || (Date.now() - cachedCoordsAt) >= 300000) {
                showMessage('info', '📍 Detecting your location...');
            }

            try {
                const coords = await getCurrentCoordinates();
                showMessage('info', '⏳ Recording your attendance...');

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append(type === 'in' ? 'in' : 'out', '1');
                formData.append('latitude', coords.latitude);
                formData.append('longitude', coords.longitude);

                const response = await fetch("{{ url('attendanceemployee/attendance') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!data.success) {
                    showMessage('warning', data.message || 'Attendance submission failed. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalBtnText;
                    isPunchInProgress = false;
                    return;
                }

                // ✅ SUCCESS - Attendance recorded
                showMessage('success', '✅ ' + (data.message || 'Attendance recorded successfully.'));
                
                // Update button state
                if (type === 'in') {
                    btn.innerHTML = '<i class="fas fa-check"></i> Punched In';
                    if (clockOutBtn) {
                        clockOutBtn.disabled = false;
                        clockOutBtn.style.display = 'inline-block';
                    }
                } else {
                    btn.innerHTML = '<i class="fas fa-check"></i> Punched Out';
                }

                // Reload page immediately
                window.location.reload();

            } catch (error) {
                // Network or other error
                showMessage('danger', '❌ Error: ' + (error.message || 'Network error. Please check your connection and try again.'));
                btn.disabled = false;
                btn.innerHTML = originalBtnText;
                isPunchInProgress = false;
            }
        }

        if (clockInBtn) {
            clockInBtn.addEventListener("click", function (e) {
                e.preventDefault();
                submitAttendance('in');
            });
        }

        function showMessage(type, text) {
            const box = document.getElementById('gpsMessage');
            if (!box) return;

            box.classList.remove('d-none');

            // Reset custom styles from previous state
            box.style.background = '';
            box.style.border = '';
            box.style.color = '';

            const styles = {
                success: { bg: 'linear-gradient(135deg,#f0fdf4,#dcfce7)', border: '1px solid #86efac', color: '#15803d', icon: '✅' },
                warning: { bg: 'linear-gradient(135deg,#fffbeb,#fef3c7)', border: '1px solid #fcd34d', color: '#92400e', icon: '⚠️' },
                danger:  { bg: 'linear-gradient(135deg,#fff1f2,#ffe4e6)', border: '1px solid #fca5a5', color: '#dc2626', icon: '❌' },
                info:    { bg: 'linear-gradient(135deg,#eff6ff,#dbeafe)', border: '1px solid #93c5fd', color: '#1d4ed8', icon: 'ℹ️' },
            };
            const s = styles[type] || styles.info;
            box.style.background = s.bg;
            box.style.border = s.border;
            box.style.color = s.color;

            const formattedText = text.replace(/\n/g, '<br>');
            box.innerHTML = `<span>${formattedText}</span>`;

            // Auto-hide success messages after 3 seconds
            if (type === 'success') {
                setTimeout(() => {
                    box.classList.add('d-none');
                }, 3000);
            }
        }

        function clearMessage() {
            const box = document.getElementById('gpsMessage');
            if (box) box.classList.add('d-none');
        }

// --- Improved Attendance Overview JS (replace previous versions) ---
/* ---------- Enhanced Attendance Overview JS (paste into your Blade) ---------- */

let attendanceWeekOffset = 0; // 0 = week of selected date or current week

function initializeAttendanceOverview() {
    // Add event listeners for filter options
    document.querySelectorAll('.attendance-filter-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const filterType = this.dataset.filter;

            if (filterType === 'date' || filterType === 'monthly') {
                const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('attendanceFilterDropdown'));
                if (dropdown) dropdown.hide();
                setTimeout(() => openPicker(filterType === 'date' ? 'date' : 'month'), 120);
                return false;
            }

            if (filterType === 'weekly') {
                const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('attendanceFilterDropdown'));
                if (dropdown) dropdown.hide();

                const datePicker = document.getElementById('attendanceDatePicker');
                // if selected date exists, use it; else use today
                const refDate = (datePicker && datePicker.value) ? datePicker.value : (new Date()).toISOString().split('T')[0];
                // reset weekOffset to 0 (start from selected reference)
                attendanceWeekOffset = 0;
                setSelectedFilterActive('weekly', 'Week of ' + formatShortDate(refDate));
                loadAttendanceData('weekly', refDate);
                return false;
            }

            // today
            setSelectedFilterActive(filterType);
            loadAttendanceData(filterType);
            return false;
        });
    });

    // pickers hooks
    const datePicker = document.getElementById('attendanceDatePicker');
    if (datePicker) {
        datePicker.addEventListener('change', function() {
            if (this.value) handleDateSelect(this.value);
        });
        datePicker.addEventListener('input', function() {
            if (this.value) handleDateSelect(this.value);
        });
    }
    const monthPicker = document.getElementById('attendanceMonthPicker');
    if (monthPicker) {
        monthPicker.addEventListener('change', function() {
            if (this.value) handleMonthSelect(this.value);
        });
        monthPicker.addEventListener('input', function() {
            if (this.value) handleMonthSelect(this.value);
        });
    }

    // Add prev/next week button listeners (buttons HTML below)
    const prevWeekBtn = document.getElementById('prevWeekBtn');
    const nextWeekBtn = document.getElementById('nextWeekBtn');
    if (prevWeekBtn) {
        prevWeekBtn.addEventListener('click', function() {
            adjustWeekOffset(-1);
        });
    }
    if (nextWeekBtn) {
        nextWeekBtn.addEventListener('click', function() {
            adjustWeekOffset(1);
        });
    }

    loadAttendanceData('today'); // default
}

function setSelectedFilterActive(filterType, labelText = null) {
    document.querySelectorAll('.attendance-filter-option').forEach(o => o.classList.remove('active'));
    const option = document.querySelector(`[data-filter="${filterType}"]`);
    if (option) option.classList.add('active');
    document.getElementById('selectedFilterText').textContent = labelText || (option ? option.textContent.trim() : filterType);
}

function formatShortDate(isoDate /* YYYY-MM-DD */) {
    try {
        const d = new Date(isoDate + 'T00:00:00');
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } catch (e) {
        return isoDate;
    }
}

function adjustWeekOffset(delta) {
    attendanceWeekOffset += delta; // negative -> previous weeks
    // compute reference date from datePicker value or today
    const datePicker = document.getElementById('attendanceDatePicker');
    let base = (datePicker && datePicker.value) ? new Date(datePicker.value + 'T00:00:00') : new Date();
    // shift by 7 * offset days
    const ref = new Date(base);
    ref.setDate(base.getDate() + (attendanceWeekOffset * 7));
    const isoRef = ref.toISOString().split('T')[0];
    setSelectedFilterActive('weekly', 'Week of ' + formatShortDate(isoRef));
    loadAttendanceData('weekly', isoRef);
}

function openPicker(type) {
    const datePicker = document.getElementById('attendanceDatePicker');
    const monthPicker = document.getElementById('attendanceMonthPicker');
    if (type === 'date' && datePicker) {
        datePicker.style.position = 'fixed'; datePicker.style.opacity = '0'; datePicker.style.pointerEvents = 'auto'; datePicker.style.zIndex = '9999';
        if (datePicker.showPicker && typeof datePicker.showPicker === 'function') {
            const pickerPromise = datePicker.showPicker();
            if (pickerPromise && typeof pickerPromise.catch === 'function') {
                pickerPromise.catch(() => datePicker.click()).finally(() => {
                    datePicker.style.position = 'absolute';
                    datePicker.style.opacity = '0';
                    datePicker.style.pointerEvents = 'none';
                    datePicker.style.zIndex = 'auto';
                });
            } else {
                datePicker.click();
                setTimeout(() => {
                    datePicker.style.position = 'absolute';
                    datePicker.style.opacity = '0';
                    datePicker.style.pointerEvents = 'none';
                    datePicker.style.zIndex = 'auto';
                }, 200);
            }
        } else {
            datePicker.click();
            setTimeout(() => {
                datePicker.style.position = 'absolute';
                datePicker.style.opacity = '0';
                datePicker.style.pointerEvents = 'none';
                datePicker.style.zIndex = 'auto';
            }, 200);
        }
    } else if (type === 'month' && monthPicker) {
        monthPicker.style.position = 'fixed'; monthPicker.style.opacity = '0'; monthPicker.style.pointerEvents = 'auto'; monthPicker.style.zIndex = '9999';
        if (monthPicker.showPicker && typeof monthPicker.showPicker === 'function') {
            const pickerPromise = monthPicker.showPicker();
            if (pickerPromise && typeof pickerPromise.catch === 'function') {
                pickerPromise.catch(() => monthPicker.click()).finally(() => {
                    monthPicker.style.position = 'absolute';
                    monthPicker.style.opacity = '0';
                    monthPicker.style.pointerEvents = 'none';
                    monthPicker.style.zIndex = 'auto';
                });
            } else {
                monthPicker.click();
                setTimeout(() => {
                    monthPicker.style.position = 'absolute';
                    monthPicker.style.opacity = '0';
                    monthPicker.style.pointerEvents = 'none';
                    monthPicker.style.zIndex = 'auto';
                }, 200);
            }
        } else {
            monthPicker.click();
            setTimeout(() => {
                monthPicker.style.position = 'absolute';
                monthPicker.style.opacity = '0';
                monthPicker.style.pointerEvents = 'none';
                monthPicker.style.zIndex = 'auto';
            }, 200);
        }
    }
}

function handleDateSelect(dateValue) {
    if (!dateValue) return;
    // when user picks a date we treat it as date filter (single day)
    setSelectedFilterActive('date', formatShortDate(dateValue));
    // reset week offset so prev/next operate from new base
    attendanceWeekOffset = 0;
    loadAttendanceData('date', dateValue);
}

function handleMonthSelect(monthValue) {
    if (!monthValue) return;
    setSelectedFilterActive('monthly', new Date(monthValue + '-01').toLocaleDateString('en-US',{ month:'long', year:'numeric' }));
    attendanceWeekOffset = 0;
    loadAttendanceData('monthly', monthValue);
}

function loadAttendanceData(filterType, dateValue = null) {
    // Stop real-time updates when loading new data
    stopRealTimeUpdates();
    
    const contentDiv = document.getElementById('attendanceOverviewContent');
    const loadingDiv = document.getElementById('attendanceLoading');
    const punchInOutSection = document.getElementById('punchInOutSection');
    
    // Check if elements exist before accessing
    if (!contentDiv) {
        console.error('[Attendance] attendanceOverviewContent element not found');
        return;
    }
    
    // Hide/show punch in/out section based on filter type
    // Check if selected date is today for date filter
    const datePicker = document.getElementById('attendanceDatePicker');
    const selectedDate = datePicker ? datePicker.value : null;
    const today = new Date().toISOString().split('T')[0];
    const isSelectedDateToday = selectedDate === today;
    
    if (punchInOutSection) {
        if (filterType === 'today' || (filterType === 'date' && isSelectedDateToday)) {
            punchInOutSection.style.display = 'block';
        } else {
            punchInOutSection.style.display = 'none';
        }
    }
    
    // Show loading indicator if it exists
    if (loadingDiv) {
        loadingDiv.style.display = 'block';
    }
    
    // Clear content
    contentDiv.innerHTML = '';

    const url = '{{ route("attendance.overview") }}';
    const payload = {
        _token: '{{ csrf_token() }}',
        filter_type: filterType,
        employee_id: {{ optional($employeeAttendance)->employee_id ?? (\Auth::user()->employee->id ?? 0) }}
    };

    if (filterType === 'weekly') {
        if (dateValue) {
            payload.date = dateValue;
            currentWeekDate = dateValue;
        } else {
            const datePicker = document.getElementById('attendanceDatePicker');
            payload.date = (datePicker && datePicker.value) ? datePicker.value : (new Date()).toISOString().split('T')[0];
            currentWeekDate = payload.date;
        }
    } else if (filterType === 'date' && dateValue) {
        payload.date = dateValue;
    } else if (filterType === 'monthly' && dateValue) {
        payload.month = dateValue; // YYYY-MM
        currentMonth = dateValue;
    }

    console.log('[Attendance] Request payload:', payload);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(r => {
        // Hide loading indicator if it exists
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
        if (!r.ok) throw new Error('Network error');
        return r.json();
    })
    .then(json => {
        console.log('[Attendance] Response:', json);
        if (!json.success) {
            contentDiv.innerHTML = '<div class="alert alert-warning">' + (json.message || 'No data') + '</div>';
            return;
        }

        // If weekly, update label with week range returned by server if available:
        if (filterType === 'weekly' && json.data && json.data.week_start && json.data.week_end) {
            const selectedFilterText = document.getElementById('selectedFilterText');
            if (selectedFilterText) {
                selectedFilterText.textContent = json.data.week_start + ' - ' + json.data.week_end;
            }
        }
        if (filterType === 'monthly' && json.data && json.data.month_name) {
            const selectedFilterText = document.getElementById('selectedFilterText');
            if (selectedFilterText) {
                selectedFilterText.textContent = json.data.month_name;
            }
        }

        renderAttendanceOverview(json.data, filterType);
        
        // Update attendance status text if late punch-in (for today view)
        if (filterType === 'today' && json.data.clock_in && json.data.is_late) {
            const attendanceStatus = document.getElementById('attendanceStatus');
            if (attendanceStatus) {
                const timeString = json.data.clock_in;
                attendanceStatus.innerHTML = '<span class="text-danger"><i class="fas fa-fingerprint"></i> Punched In at ' + timeString + '</span>';
            }
        }
    })
    .catch(err => {
        // Hide loading indicator if it exists
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
        if (contentDiv) {
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading attendance data.</div>';
        }
        console.error('[Attendance] fetch error:', err);
    });
}

// Global variables for real-time updates
let attendanceUpdateInterval = null;
let currentAttendanceData = null;
let currentFilterType = null;

// Function to stop real-time updates
function stopRealTimeUpdates() {
    if (attendanceUpdateInterval) {
        clearInterval(attendanceUpdateInterval);
        attendanceUpdateInterval = null;
    }
}

// Function to check if viewing current period
function isCurrentPeriod(filterType, data) {
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    
    if (filterType === 'today') {
        return true;
    }
    
    if (filterType === 'date') {
        const datePicker = document.getElementById('attendanceDatePicker');
        const selectedDate = datePicker ? datePicker.value : null;
        return selectedDate === todayStr;
    }
    
    if (filterType === 'weekly') {
        // Check if current week
        const weekStart = data.week_start ? new Date(data.week_start) : null;
        const weekEnd = data.week_end ? new Date(data.week_end) : null;
        if (weekStart && weekEnd) {
            return today >= weekStart && today <= weekEnd;
        }
    }
    
    if (filterType === 'monthly') {
        // Check if current month
        const monthName = data.month_name || '';
        const currentMonthName = today.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        return monthName === currentMonthName;
    }
    
    return false;
}

// Function to calculate current hours for today
function calculateCurrentHours(data) {
    if (!data.clock_in || data.clock_in === 'N/A') {
        return data.hours_completed || 0;
    }
    
    // If already clocked out, return the completed hours
    if (data.clock_out && data.clock_out !== 'N/A' && data.clock_out !== '00:00:00') {
        return data.hours_completed || 0;
    }
    
    // Use server-calculated hours if available (more accurate)
    // Only recalculate if we need real-time updates and the server value seems stale
    if (data.hours_completed !== undefined && data.hours_completed !== null) {
        // For real-time calculation, add the difference from last server update
        // But cap it at a reasonable maximum (e.g., 24 hours for a single day)
        const maxHoursForDay = 24;
        const serverHours = data.hours_completed || 0;
        
        // If server hours are already calculated and reasonable, use them
        // Only do real-time calculation if it's today and we want live updates
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        
        // Calculate from clock_in to now, using the actual attendance date
        try {
            let clockInTime;
            let clockInDateStr = todayStr; // Default to today
            
            // Use the attendance date from server if available
            if (data.date) {
                clockInDateStr = data.date;
            }
            
            // If we have raw clock_in time (24-hour format), use it for more accurate calculation
            if (data.clock_in_raw) {
                // Use raw time in format "HH:MM:SS" or "HH:MM"
                const timeStr = data.clock_in_raw.length >= 5 ? data.clock_in_raw.substring(0, 5) : data.clock_in_raw;
                clockInTime = new Date(clockInDateStr + 'T' + timeStr + ':00');
            } else if (data.clock_in.includes('AM') || data.clock_in.includes('PM')) {
                // Handle 12-hour format conversion (e.g., "09:30 AM" or "12:19 AM")
                const timeParts = data.clock_in.match(/(\d+):(\d+)\s*(AM|PM)/i);
                if (timeParts) {
                    let hours = parseInt(timeParts[1]);
                    const minutes = parseInt(timeParts[2]);
                    const ampm = timeParts[3].toUpperCase();
                    
                    if (ampm === 'PM' && hours !== 12) hours += 12;
                    if (ampm === 'AM' && hours === 12) hours = 0;
                    
                    clockInTime = new Date(clockInDateStr + 'T' + 
                        String(hours).padStart(2, '0') + ':' + 
                        String(minutes).padStart(2, '0') + ':00');
                } else {
                    return Math.min(serverHours, maxHoursForDay);
                }
            } else {
                // Handle 24-hour format (e.g., "09:30:00" or "09:30")
                clockInTime = new Date(clockInDateStr + 'T' + data.clock_in);
            }
            
            const now = new Date();
            const diffMs = now - clockInTime;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            // Cap at maximum reasonable hours for a single day (24 hours)
            // Also, if the calculated hours are way more than server hours, 
            // it might be a date mismatch - use server hours instead
            const calculatedHours = Math.max(0, diffHours);
            
            // If calculated hours are more than 24, it's likely a date issue
            // Use server hours instead, or cap at 24
            if (calculatedHours > maxHoursForDay) {
                // Likely clocked in yesterday - use server hours or cap
                return Math.min(serverHours, maxHoursForDay);
            }
            
            // Use the calculated hours, capped at max hours for the day
            return Math.min(Math.max(calculatedHours, serverHours), maxHoursForDay);
        } catch (e) {
            console.error('Error calculating current hours:', e);
            return Math.min(serverHours || 0, maxHoursForDay);
        }
    }
    
    // Fallback: return server hours or 0
    return Math.min(data.hours_completed || 0, 24);
}

// Store the date/month used for weekly/monthly to refresh data
let currentWeekDate = null;
let currentMonth = null;

// Function to update progress bar in real-time (smooth, no interruptions)
function updateProgressBarRealTime() {
    if (!currentAttendanceData || !currentFilterType) return;
    
    const contentDiv = document.getElementById('attendanceOverviewContent');
    if (!contentDiv) return;
    
    let hoursCompleted = 0;
    let totalHours = 0;
    let percentage = 0;
    
    if (currentFilterType === 'today' || currentFilterType === 'date') {
        // For today/date, calculate real-time from clock_in
        hoursCompleted = calculateCurrentHours(currentAttendanceData);
        totalHours = currentAttendanceData.total_hours || 8; // Usually 8 or 4
        percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
    } else if (currentFilterType === 'weekly') {
        // For weekly, use stored data and add today's real-time hours if applicable
        const baseHours = currentAttendanceData.hours_completed || 0;
        totalHours = currentAttendanceData.total_hours || 0;
        
        // If today is in the week and user is clocked in, add real-time hours
        if (currentAttendanceData.clock_in && 
            (!currentAttendanceData.clock_out || currentAttendanceData.clock_out === 'N/A')) {
            // User is clocked in today, calculate real-time hours
            const todayHours = calculateCurrentHours(currentAttendanceData);
            const storedTodayHours = currentAttendanceData.today_hours || 0;
            // Replace today's hours with real-time calculation
            hoursCompleted = baseHours - storedTodayHours + todayHours;
        } else {
            hoursCompleted = baseHours;
        }
        
        percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
    } else if (currentFilterType === 'monthly') {
        // For monthly, similar to weekly
        const baseHours = currentAttendanceData.hours_completed || 0;
        totalHours = currentAttendanceData.total_hours || 0;
        
        // If user is clocked in today, add real-time hours
        if (currentAttendanceData.clock_in && 
            (!currentAttendanceData.clock_out || currentAttendanceData.clock_out === 'N/A')) {
            // User is clocked in today, calculate real-time hours
            const todayHours = calculateCurrentHours(currentAttendanceData);
            const storedTodayHours = currentAttendanceData.today_hours || 0;
            // Replace today's hours with real-time calculation
            hoursCompleted = baseHours - storedTodayHours + todayHours;
        } else {
            hoursCompleted = baseHours;
        }
        
        percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
    }
    
    // Update the progress bar elements smoothly without any loading indicators
    const hoursText = contentDiv.querySelector('.h5.mb-0');
    const badge = contentDiv.querySelector('.badge');
    const progressBar = contentDiv.querySelector('.progress-bar');
    const hoursCompletedLabel = contentDiv.querySelector('h6.text-muted.mb-2');
    
    // Check if late punch-in (from currentAttendanceData)
    const isLate = currentAttendanceData && currentAttendanceData.is_late;
    
    if (hoursText) {
        if (currentFilterType === 'today' || currentFilterType === 'date') {
            const h = Math.floor(hoursCompleted);
            const m = Math.round((hoursCompleted - h) * 60);
            hoursText.textContent = `${h} hours ${m} minutes / ${totalHours} hours`;
        } else {
            hoursText.textContent = `${Math.round(hoursCompleted)}/${totalHours} hours`;
        }
    }
    
    if (badge) {
        const displayPct = Math.min(percentage, 100);
        badge.textContent = `${displayPct.toFixed(1)}%`;
        // Update badge color if late
        if (isLate) {
            badge.className = 'badge bg-danger';
        }
    }
    
    if (progressBar) {
        const displayPct = Math.min(percentage, 100);
        // Smooth transition for progress bar width
        progressBar.style.transition = 'width 0.5s ease';
        progressBar.style.width = `${displayPct}%`;
        progressBar.setAttribute('aria-valuenow', Math.min(hoursCompleted, totalHours));
        progressBar.textContent = `${displayPct.toFixed(1)}%`;
        // Update progress bar color if late
        if (isLate) {
            progressBar.className = 'progress-bar bg-danger';
        } else {
            progressBar.className = 'progress-bar bg-primary';
            progressBar.style.backgroundColor = '';
            badge.className = 'badge bg-primary';
            badge.style.backgroundColor = '';
            if (hoursCompleted >= totalHours) badge.style.color = '#fff';
        }
    }

    const overtimeContainer = contentDiv.querySelector('#overtimeContainer');
    if (overtimeContainer) {
        overtimeContainer.innerHTML = buildOvertimeBar(hoursCompleted, totalHours, currentFilterType === 'weekly' || currentFilterType === 'monthly');
    }
    
    // Update hours completed label to show "(Late Punch-In)" if late
    if (hoursCompletedLabel && isLate && !hoursCompletedLabel.innerHTML.includes('Late Punch-In')) {
        hoursCompletedLabel.innerHTML = hoursCompletedLabel.innerHTML.replace('Hours Completed', 'Hours Completed <span class="text-danger">(Late Punch-In)</span>');
    }
}

        function buildOvertimeBar(hoursCompleted, totalHours, isWholeHours = false) {
            if (!totalHours || hoursCompleted <= totalHours) {
                return '';
            }
            const overtimeHours = hoursCompleted - totalHours;
            const overtimePct = (overtimeHours / totalHours) * 100;
            const otText = isWholeHours
                ? `+${Math.round(overtimeHours)} hours`
                : `+${Math.floor(overtimeHours)}h ${Math.round((overtimeHours - Math.floor(overtimeHours)) * 60)}m`;

            return `
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-semibold" style="color: #b45309;">
                            <i class="ti ti-clock-plus me-1"></i>Overtime (${otText})
                        </span>
                        <span class="badge" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff;">+${overtimePct.toFixed(1)}%</span>
                    </div>
                    <div class="progress" style="height: 30px; background-color: #fef3c7;">
                        <div class="progress-bar"
                             role="progressbar"
                             style="width: ${Math.min(overtimePct, 100)}%; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); font-weight: 600;"
                             aria-valuenow="${overtimeHours.toFixed(2)}"
                             aria-valuemin="0"
                             aria-valuemax="${totalHours}">
                            Overtime ${otText}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderAttendanceOverview(data, filterType) {
            const contentDiv = document.getElementById('attendanceOverviewContent');
            const punchInOutSection = document.getElementById('punchInOutSection');
            let html = '';

            // Check if selected date is today
            const datePicker = document.getElementById('attendanceDatePicker');
            const selectedDate = datePicker ? datePicker.value : null;
            const today = new Date().toISOString().split('T')[0];
            const isSelectedDateToday = selectedDate === today;

            // Hide punch in/out section for:
            // - date filter when selected date is NOT today
            // - weekly view
            // - monthly view
            if (filterType === 'weekly' || filterType === 'monthly' || 
                (filterType === 'date' && !isSelectedDateToday)) {
                if (punchInOutSection) {
                    punchInOutSection.style.display = 'none';
                }
            } else {
                // Show punch in/out section for today view or when selected date is today
                if (punchInOutSection) {
                    punchInOutSection.style.display = 'block';
                }
            }

            if (filterType === 'today' || filterType === 'date') {
                // Check if late punch-in
                const isLate = data.is_late || false;
                const totalHours = data.total_hours || 8;
                const hoursCompleted = data.hours_completed || 0;
                const percentage = totalHours > 0 ? (hoursCompleted / totalHours * 100) : 0;
                const mainPct = Math.min(percentage, 100);
                
                const progressBarClass = isLate ? 'bg-danger' : 'bg-primary';
                const badgeClass = isLate ? 'bg-danger' : 'bg-primary';
                const lateText = isLate ? ' <span class="text-danger">(Late Punch-In)</span>' : '';
                const overtimeHtml = buildOvertimeBar(hoursCompleted, totalHours, false);
                
                if (filterType === 'date' && !isSelectedDateToday) {
                    // For selected date (not today), show punch in and punch out times with progress bar
                    html = `
                        <div class="attendance-detail">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Hours Completed${lateText}</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0">${Math.floor(hoursCompleted)} hours ${Math.round((hoursCompleted - Math.floor(hoursCompleted)) * 60)} minutes / ${totalHours} hours</span>
                                    <span class="badge ${badgeClass}">${mainPct.toFixed(1)}%</span>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar ${progressBarClass}" 
                                         role="progressbar" 
                                         style="width: ${mainPct}%;" 
                                         aria-valuenow="${Math.min(hoursCompleted, totalHours)}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="${totalHours}">
                                        ${mainPct.toFixed(1)}%
                                    </div>
                                </div>
                                <div id="overtimeContainer">${overtimeHtml}</div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Punch In Time</small>
                                    <p class="small mb-0 ${isLate ? 'text-danger' : ''}" style="font-size: 0.875rem;">${data.clock_in || 'N/A'}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <small class="text-muted d-block">Punch Out Time</small>
                                    <p class="small mb-0" style="font-size: 0.875rem;">${data.clock_out || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Today view - show hours completed with progress (buttons shown above)
                    html = `
                        <div class="attendance-detail">
                            <div class="mb-2">
                                <h6 class="text-muted mb-2">Hours Completed${lateText}</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0">${Math.floor(hoursCompleted)} hours ${Math.round((hoursCompleted - Math.floor(hoursCompleted)) * 60)} minutes / ${totalHours} hours</span>
                                    <span class="badge ${badgeClass}">${mainPct.toFixed(1)}%</span>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar ${progressBarClass}" 
                                         role="progressbar" 
                                         style="width: ${mainPct}%;" 
                                         aria-valuenow="${Math.min(hoursCompleted, totalHours)}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="${totalHours}">
                                        ${mainPct.toFixed(1)}%
                                    </div>
                                </div>
                                <div id="overtimeContainer">${overtimeHtml}</div>
                            </div>
                            ${data.clock_out ? `<div class="mb-2"><h6 class="text-muted mb-2">Punch Out Time</h6><p class="h6 mb-0">${data.clock_out}</p></div>` : ''}
                        </div>
                    `;
                }
            } else if (filterType === 'weekly') {
                // Weekly view - show hours completed with progress bar, Days Worked and Week Period
                const hoursCompleted = Math.round(data.hours_completed || 0);
                const totalHours = Math.round(data.total_hours || 0);
                const percentage = data.percentage || 0;
                const mainPct = Math.min(percentage, 100);
                const overtimeHtml = buildOvertimeBar(hoursCompleted, totalHours, true);
                
                html = `
                    <div class="attendance-detail">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Hours Completed</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0">${hoursCompleted}/${totalHours} hours</span>
                                <span class="badge bg-primary">${mainPct.toFixed(1)}%</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-primary" 
                                     role="progressbar" 
                                     style="width: ${mainPct}%;" 
                                     aria-valuenow="${Math.min(hoursCompleted, totalHours)}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="${totalHours}">
                                    ${mainPct.toFixed(1)}%
                                </div>
                            </div>
                            <div id="overtimeContainer">${overtimeHtml}</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Days Worked</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.days_worked || 0} days</p>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Week Period</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.week_start || ''} - ${data.week_end || ''}</p>
                            </div>
                        </div>
                        <!-- Week Navigation -->
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="prevWeekBtn" style="width: auto !important; height: auto !important; padding: 6px 14px !important; border-radius: 10px !important; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; font-size: 12px; font-weight: 600;">
                                <i class="fas fa-chevron-left"></i> Previous Week
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="nextWeekBtn" style="width: auto !important; height: auto !important; padding: 6px 14px !important; border-radius: 10px !important; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; font-size: 12px; font-weight: 600;">
                                Next Week <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                // Re-attach week navigation listeners after rendering
                setTimeout(() => {
                    const prevWeekBtn = document.getElementById('prevWeekBtn');
                    const nextWeekBtn = document.getElementById('nextWeekBtn');
                    if (prevWeekBtn) {
                        prevWeekBtn.addEventListener('click', function() {
                            adjustWeekOffset(-1);
                        });
                    }
                    if (nextWeekBtn) {
                        nextWeekBtn.addEventListener('click', function() {
                            adjustWeekOffset(1);
                        });
                    }
                }, 100);
            } else if (filterType === 'monthly') {
                // Monthly view - show hours completed with progress bar, Days Worked and Month Name
                const hoursCompleted = Math.round(data.hours_completed || 0);
                const totalHours = Math.round(data.total_hours || 0);
                const percentage = data.percentage || 0;
                const mainPct = Math.min(percentage, 100);
                const overtimeHtml = buildOvertimeBar(hoursCompleted, totalHours, true);
                
                html = `
                    <div class="attendance-detail">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Hours Completed</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0">${hoursCompleted}/${totalHours} hours</span>
                                <span class="badge bg-primary">${mainPct.toFixed(1)}%</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-primary" 
                                     role="progressbar" 
                                     style="width: ${mainPct}%;" 
                                     aria-valuenow="${Math.min(hoursCompleted, totalHours)}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="${totalHours}">
                                    ${mainPct.toFixed(1)}%
                                </div>
                            </div>
                            <div id="overtimeContainer">${overtimeHtml}</div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Days Worked</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.days_worked || 0} days</p>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Month Name</small>
                                <p class="small mb-0" style="font-size: 0.875rem;">${data.month_name || ''}</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            contentDiv.innerHTML = html;
            
            // Store current data for real-time updates
            currentAttendanceData = data;
            currentFilterType = filterType;
            
            // Stop any existing interval
            stopRealTimeUpdates();
            
            // Start real-time updates if viewing current period
            if (isCurrentPeriod(filterType, data)) {
                // Update immediately
                updateProgressBarRealTime();
                
                // Update every 5 seconds for smooth continuous progress
                // This ensures the progress bar updates continuously without interruptions
                attendanceUpdateInterval = setInterval(() => {
                    // Always update the progress bar smoothly without reloading
                    // This runs every 5 seconds for smooth continuous progress
                    updateProgressBarRealTime();
                }, 5000); // Update every 5 seconds for smooth continuous progress
                
                // For weekly/monthly, also refresh data from server every 2 minutes in background
                // but don't show loading - just update the stored data silently
                if (filterType === 'weekly' || filterType === 'monthly') {
                    setInterval(() => {
                        const url = '{{ route("attendance.overview") }}';
                        const payload = {
                            _token: '{{ csrf_token() }}',
                            filter_type: filterType,
                            employee_id: {{ optional($employeeAttendance)->employee_id ?? (\Auth::user()->employee->id ?? 0) }}
                        };
                        
                        if (filterType === 'weekly' && currentWeekDate) {
                            payload.date = currentWeekDate;
                        } else if (filterType === 'monthly' && currentMonth) {
                            payload.month = currentMonth;
                        }
                        
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type':'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(r => r.json())
                        .then(json => {
                            if (json.success && json.data) {
                                // Update stored data silently without reloading UI
                                currentAttendanceData = json.data;
                                // Progress bar will update on next interval automatically
                            }
                        })
                        .catch(err => {
                            console.error('Background refresh error:', err);
                        });
                    }, 120000); // Refresh every 2 minutes in background
                }
            }
        }
        
        // initialize attendance overview when dom is ready
        initializeAttendanceOverview();
        
        // ── Schedules Filter Logic ──
        $('.schedule-filter-option').on('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all options
            $('.schedule-filter-option').removeClass('active');
            $(this).addClass('active');
            
            const filterType = $(this).data('filter');
            
            if (filterType === 'custom') {
                try {
                    $('#scheduleCustomDatePicker')[0].showPicker();
                } catch (e) {
                    $('#scheduleCustomDatePicker').focus().click();
                }
                return; // Wait for date selection
            }
            
            // Update dropdown text immediately for predefined options
            $('#selectedScheduleFilterText').text($(this).text().trim());
            
            loadSchedulesData(filterType);
        });

        // Expose handleScheduleDateSelect to global scope for the inline onchange handler
        window.handleScheduleDateSelect = function(dateStr) {
            if (!dateStr) return;
            
            $('.schedule-filter-option').removeClass('active');
            $('.schedule-filter-option[data-filter="custom"]').addClass('active');
            
            $('#selectedScheduleFilterText').text(dateStr);
            
            loadSchedulesData('custom', dateStr);
        };
        
        function loadSchedulesData(filterType, customDate = null) {
            // Show loading state gracefully if you want, or just wait for AJAX
            let url = '{{ route("dashboard.filter") }}';
            let data = {
                _token: '{{ csrf_token() }}',
                filter_type: filterType
            };
            if (customDate) {
                data.custom_date = customDate;
            }
            
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        // Update Schedules HTML
                        if (response.schedulesHtml !== undefined) {
                            $('#schedulesContainer').html(response.schedulesHtml);
                        }
                        
                        // Update Title
                        let scheduleTitle = "Today's Schedules";
                        if (filterType === 'yesterday') {
                            scheduleTitle = 'Yesterday Schedules';
                        } else if (filterType === 'custom' && customDate) {
                            scheduleTitle = customDate + ' Schedules';
                        }
                        $('#scheduleFilterTitle').text(scheduleTitle);
                    }
                },
                error: function(xhr) {
                    console.error('Schedule filter error:', xhr);
                }
            });
        }
    });

    $(document).ready(function() {
        // ── Event Detail Popup ──
        $(document).on('click', '.event-popup-trigger', function() {
            const type     = $(this).data('type');
            const name     = $(this).data('name');
            const dateStr  = $(this).data('date');
            const years    = $(this).data('years');
            const title    = $(this).data('title');
            const id       = $(this).data('id');

            let formatted = dateStr;
            try {
                const dateObj  = new Date(dateStr + 'T00:00:00');
                formatted = dateObj.toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
            } catch(e) {}

            let emoji, greeting, bgGrad, badgeText, badgeBg;
            const currentEmployeeId = {{ \Auth::user()->employee->id ?? 0 }};
            const isSelf = (id == currentEmployeeId);
            
            if (type === 'birthday') {
                emoji     = '🎂';
                greeting  = isSelf ? 'Happy Birthday to You!' : (name ? 'Happy Birthday, ' + name + '!' : title);
                bgGrad    = 'linear-gradient(135deg, #e47477ff 0%, #ff9ee1ff 99%, #f8bee6ff 100%)';
                badgeText = '🎉 Birthday';
                badgeBg   = 'rgba(255,255,255,0.3)';
            } else if (type === 'anniversary') {
                emoji     = '🎉';
                greeting  = isSelf ? 'Happy Work Anniversary to You!' : (name ? 'Work Anniversary, ' + name + '!' : title);
                bgGrad    = 'linear-gradient(135deg, #836addff 0%, #e09ccdff 100%)';
                badgeText = '🎊 Work Anniversary';
                badgeBg   = 'rgba(255,255,255,0.3)';
            } else {
                emoji     = '📅';
                greeting  = title;
                bgGrad    = 'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)';
                badgeText = 'COMPANY EVENT';
                badgeBg   = 'rgba(255,255,255,0.3)';
            }

            $('#eventModalHeader').css('background', bgGrad);
            $('#eventModalEmoji').text(emoji);
            $('#eventModalBadge').text(badgeText).css({'background': badgeBg, 'color':'#fff', 'border':'1px solid rgba(255,255,255,0.4)'});
            $('#eventModalGreeting').text(greeting);
            $('#eventModalName').text(name || '—');
            $('#eventModalDate').text(formatted);

            if (type === 'anniversary' && years) {
                $('#eventModalYears').text(years + ' Year' + (years > 1 ? 's' : ''));
                $('#eventModalYearsRow').css('display','flex');
            } else {
                $('#eventModalYearsRow').css('display','none');
            }

            const todayStr = new Date().toLocaleDateString('en-CA');
            if ((type === 'birthday' || type === 'anniversary') && dateStr === todayStr && id && !isSelf) {
                $('#wishesActionContainer').css('display', 'flex');
                window.currentEventData = {
                    employee_id: id,
                    event_type: type,
                    years: years,
                    event_date: dateStr
                };
            } else {
                $('#wishesActionContainer').css('display', 'none');
            }
            
            $('#customWishesForm').css('display', 'none');

            const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
            modal.show();
        });

        // ── Wishes Functions ──
        window.openCustomWishes = function() {
            $('#wishesActionContainer').css('display', 'none');
            $('#customWishesForm').css('display', 'flex');
            $('#customWishesMessage').val('');
            $('#customWishesMessage').focus();
        };

        window.closeCustomWishes = function() {
            $('#customWishesForm').css('display', 'none');
            $('#wishesActionContainer').css('display', 'flex');
        };

        window.sendWishes = function(mode) {
            if (!window.currentEventData) return;
            
            let message = null;
            if (mode === 'custom') {
                message = $('#customWishesMessage').val().trim();
                if (!message) return;
            }

            const data = {
                _token: '{{ csrf_token() }}',
                employee_id: window.currentEventData.employee_id,
                event_type: window.currentEventData.event_type,
                years: window.currentEventData.years,
                event_date: window.currentEventData.event_date,
                custom_message: message
            };

            const btnId = mode === 'custom' ? '#btnSendCustomWishes' : '#btnSendAutoWishes';
            const originalHtml = $(btnId).html();
            $(btnId).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...').prop('disabled', true);

            $.ajax({
                url: '{{ route("dashboard.sendWishes") }}',
                type: 'POST',
                data: data,
                success: function(response) {
                    window.location.href = window.location.href;
                },
                error: function(xhr) {
                    window.location.href = window.location.href;
                }
            });
        };
    });
    </script>
    <style>
        .attendance-filter-option:hover {
            background-color: #f8f9fa;
        }
    </style>
    @endpush
