@extends('layouts.admin')
@section('page-title')
    {{ __('Attendance Calendar') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendanceemployee.index') }}">{{ __('Attendance List') }}</a></li>
    <li class="breadcrumb-item">{{ __('Calendar') }}</li>
@endsection

@php
    $months = [
        '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
        '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
        '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
    ];
    $years = range(date('Y') - 5, date('Y') + 5);
@endphp

@section('content')
<div class="row g-4">

    {{-- ── Filter Card ── --}}
    <div class="col-12">
        <div class="attn-filter-card">
            {{ Form::open(['route' => ['attendance.calendar'], 'method' => 'get', 'id' => 'attendance_calendar_filter']) }}
            <div class="d-flex flex-wrap align-items-end gap-3">

                @if(\Auth::user()->type != 'employee')
                <div class="filter-field flex-grow-1">
                    <label class="filter-label">
                        <i class="ti ti-user me-1"></i>{{ __('Employee') }}
                    </label>
                    <select name="employee_id" class="form-control select2" id="employee_id">
                        <option value="">{{ __('Select Employee') }}</option>
                        @foreach($allEmployees as $employee)
                            <option value="{{ $employee->id }}" {{ ($selectedEmployee && $selectedEmployee->id == $employee->id) ? 'selected' : '' }}>
                                {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="filter-field">
                    <label class="filter-label">
                        <i class="ti ti-calendar me-1"></i>{{ __('Month') }}
                    </label>
                    <select name="month" class="form-control" id="month">
                        @foreach($months as $key => $name)
                            <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>{{ __($name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-end gap-2 flex-grow-1">
                    <div class="filter-field flex-grow-1">
                        <label class="filter-label">
                            <i class="ti ti-calendar-stats me-1"></i>{{ __('Year') }}
                        </label>
                        <select name="year" class="form-control" id="year">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-actions d-flex gap-2 flex-shrink-0">
                        <button type="submit" class="btn btn-primary btn-filter" data-bs-toggle="tooltip" title="{{ __('Apply Filter') }}">
                            <i class="ti ti-search"></i><span class="d-none d-sm-inline ms-1">{{ __('Apply') }}</span>
                        </button>
                        <a href="{{ route('attendance.calendar') }}" class="btn btn-outline-secondary btn-filter" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                            <i class="ti ti-refresh"></i><span class="d-none d-sm-inline ms-1">{{ __('Reset') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    {{-- ── Calendar Card ── --}}
    @if($selectedEmployee)
    <div class="col-12">
        <div class="card attn-calendar-card">

            {{-- Card Header --}}
            <div class="attn-calendar-header">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="attn-emp-info d-flex align-items-center min-w-0">
                        <div class="text-truncate">
                            <h5 class="mb-0 fw-bold text-truncate">{{ $selectedEmployee->full_name }}</h5>
                            <span class="attn-month-badge text-truncate">
                                <i class="ti ti-calendar me-1"></i>{{ __($months[$currentMonth]) }} {{ $currentYear }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-shrink-0">
                        <a href="{{ route('attendance.calendar', ['employee_id' => $selectedEmployee->id, 'month' => $previousMonth, 'year' => $previousYear]) }}"
                           class="btn btn-sm btn-nav-month">
                            <i class="ti ti-chevron-left"></i><span class="d-none d-sm-inline ms-1">{{ __('Prev') }}</span>
                        </a>
                        <a href="{{ route('attendance.calendar', ['employee_id' => $selectedEmployee->id, 'month' => $nextMonth, 'year' => $nextYear]) }}"
                           class="btn btn-sm btn-nav-month">
                            <span class="d-none d-sm-inline me-1">{{ __('Next') }}</span><i class="ti ti-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            @php
                $totalPresent = 0;
                $totalAbsent = 0;
                $totalHalfDay = 0;
                $totalLeave = 0;
                $totalWeekOff = 0;
                $totalLate = 0;
                $totalHoliday = 0;
                
                $calendarAttendance = $attendanceData[$selectedEmployee->id]['data'] ?? [];
                foreach ($calendarAttendance as $dateKey => $dayVal) {
                    if (strpos($dateKey, "$currentYear-$currentMonth") === 0) {
                        if (isset($dayVal['type'])) {
                            if ($dayVal['type'] === 'present') {
                                $totalPresent++;
                            } elseif ($dayVal['type'] === 'half_day' || $dayVal['type'] === 'single_punch') {
                                $totalHalfDay++;
                            } elseif ($dayVal['type'] === 'leave') {
                                $totalLeave++;
                            } elseif ($dayVal['type'] === 'absent') {
                                $totalAbsent++;
                            } elseif ($dayVal['type'] === 'week_off') {
                                $totalWeekOff++;
                            } elseif ($dayVal['type'] === 'Holiday' || $dayVal['type'] === 'holiday') {
                                $totalHoliday++;
                            }
                        }
                        if (!empty($dayVal['late']) && $dayVal['late'] !== '00:00:00') {
                            $totalLate++;
                        }
                    }
                }
                
                $totalPayable = $totalPresent + ($totalHalfDay * 0.5) + $totalLeave + $totalWeekOff + $totalHoliday;
            @endphp

            {{-- Legend --}}
            <div class="attn-legend-bar d-flex flex-wrap align-items-center gap-3">
                <div class="attn-legend-item">
                    <span class="legend-dot" style="background:rgba(40,167,69,0.85)"></span>
                    <span>{{ __('Present') }} ({{ $totalPresent }})</span>
                </div>
                <div class="attn-legend-item">
                    <span class="legend-dot" style="background:rgba(220,53,69,0.85)"></span>
                    <span>{{ __('Absent') }} ({{ $totalAbsent }})</span>
                </div>
                <div class="attn-legend-item">
                    <span class="legend-dot" style="background:var(--color-customColor, #5c59e8);opacity:0.85"></span>
                    <span>{{ __('Half Day') }} ({{ $totalHalfDay }})</span>
                </div>
                <div class="attn-legend-item">
                    <span class="legend-dot" style="background:rgba(23,162,184,0.85)"></span>
                    <span>{{ __('Leave') }} ({{ $totalLeave }})</span>
                </div>
                <div class="attn-legend-item">
                    <span class="legend-dot" style="background:rgba(108,117,125,0.75)"></span>
                    <span>{{ __('Week Off') }} ({{ $totalWeekOff }})</span>
                </div>
                <div class="attn-legend-item">
                    <span class="badge-pill-tag" style="background:rgba(220,53,69,0.15);color:#bd2130;">L</span>
                    <span>{{ __('Late') }} ({{ $totalLate }})</span>
                </div>
                <div class="attn-legend-item ms-md-auto">
                    <span class="badge" style="background:#28a745; color: white; font-weight: 700; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; box-shadow: 0 2px 4px rgba(40,167,69,0.2);">
                        <i class="fas fa-money-bill-wave me-1"></i>{{ __('Payable Days') }}: {{ $totalPayable }}
                    </span>
                </div>
            </div>

            {{-- Calendar Grid --}}
            <div class="attn-cal-body">
                @php
                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
                    $firstDay    = date('N', strtotime("$currentYear-$currentMonth-01"));
                    $attendance  = $attendanceData[$selectedEmployee->id]['data'] ?? [];
                @endphp

                <div class="attn-cal-grid">
                    {{-- Day headers --}}
                    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dayHead)
                    <div class="attn-day-head {{ in_array($dayHead, ['Sat','Sun']) ? 'weekend-head' : '' }}">
                        {{ __($dayHead) }}
                    </div>
                    @endforeach

                    {{-- Empty leading cells --}}
                    @for($i = 1; $i < $firstDay; $i++)
                        <div class="attn-day attn-day--empty"></div>
                    @endfor

                    {{-- Day cells --}}
                    @php $runningLateCount = 0; @endphp
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateString  = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                            $dayData     = $attendance[$dateString] ?? null;
                            $statusClass = '';
                            $statusIcon  = '';
                            $title       = '';

                            if ($dayData) {
                                $isLate = !empty($dayData['late']) && $dayData['late'] != '00:00:00';
                                if ($isLate) {
                                    $runningLateCount++;
                                }

                                switch($dayData['type']) {
                                    case 'present':
                                        $statusClass = 'day--present';
                                        $statusIcon  = 'ti-circle-check';
                                        $title = __('Clock In: ') . $dayData['clock_in'] . "\n" . __('Clock Out: ') . $dayData['clock_out'];
                                        break;
                                    case 'half_day':
                                    case 'single_punch':
                                        if (!empty($dayData['status_reason']) && strpos($dayData['status_reason'], 'Late Mark') !== false) {
                                            $statusClass = 'day--late-halfday';
                                            $statusIcon  = 'ti-alert-triangle';
                                            $title = __('Half Day (Late Mark Deduction)') . "\n" . __('Reason: ') . $dayData['status_reason'] . "\n" . __('Clock In: ') . $dayData['clock_in'];
                                            if (!empty($dayData['clock_out']) && $dayData['clock_out'] != '00:00:00') {
                                                $title .= "\n" . __('Clock Out: ') . $dayData['clock_out'];
                                            }
                                        } else {
                                            $statusClass = 'day--halfday';
                                            $statusIcon  = 'ti-clock-half-2';
                                            $title = __('Half Day / Single Punch') . "\n" . __('Clock In: ') . $dayData['clock_in'];
                                            if (!empty($dayData['clock_out']) && $dayData['clock_out'] != '00:00:00') {
                                                $title .= "\n" . __('Clock Out: ') . $dayData['clock_out'];
                                            }
                                            if (!empty($dayData['status_reason'])) {
                                                $title .= "\n" . __('Reason: ') . $dayData['status_reason'];
                                            }
                                        }
                                        break;
                                    case 'leave':
                                        $statusClass = 'day--leave';
                                        $statusIcon  = 'ti-beach';
                                        $title = __('Leave: ') . ($dayData['leave_type'] ?? '') . "\n" . ($dayData['reason'] ?? '');
                                        break;
                                    case 'absent':
                                        $statusClass = 'day--absent';
                                        $statusIcon  = 'ti-circle-x';
                                        $title = __('Absent');
                                        break;
                                    case 'week_off':
                                        $statusClass = 'day--weekoff';
                                        $statusIcon  = 'ti-sun-off';
                                        $title = __('Week Off');
                                        break;
                                }
                            }

                            $isToday   = $dateString == date('Y-m-d');
                            $dayOfWeek = date('N', strtotime($dateString));
                            $isWeekend = ($dayOfWeek >= 6);
                        @endphp

                        <div class="attn-day {{ $statusClass }} {{ $isToday ? 'day--today' : '' }} {{ $isWeekend && !$dayData ? 'day--weekend-bg' : '' }}"
                             title="{{ $title }}">

                            {{-- Day number --}}
                            <div class="day-num {{ $isToday ? 'day-num--today' : '' }}">
                                {{ $day }}
                                @if($isToday)
                                    <span class="today-indicator"></span>
                                @endif
                            </div>

                            {{-- Status content --}}
                            @if($dayData)
                                <div class="day-content">

                                    {{-- Status icon --}}
                                    @if($statusIcon)
                                        <div class="status-icon-wrap">
                                            <i class="ti {{ $statusIcon }}"></i>
                                        </div>
                                    @endif

                                    {{-- Times / label --}}
                                    @if(in_array($dayData['type'], ['present', 'half_day', 'single_punch']))
                                        <div class="time-row">
                                            <i class="ti ti-login time-icon in-icon"></i>
                                            <span>{{ $dayData['clock_in'] != '00:00:00' ? date('H:i', strtotime($dayData['clock_in'])) : '--' }}</span>
                                        </div>
                                        @if(!empty($dayData['clock_out']) && $dayData['clock_out'] != '00:00:00')
                                        <div class="time-row">
                                            <i class="ti ti-logout time-icon out-icon"></i>
                                            <span>{{ date('H:i', strtotime($dayData['clock_out'])) }}</span>
                                        </div>
                                        @endif
                                    @elseif($dayData['type'] == 'leave')
                                        <div class="day-label">{{ $dayData['leave_type'] }}</div>
                                    @else
                                        <div class="day-label">{{ __(ucfirst(str_replace('_', ' ', $dayData['type']))) }}</div>
                                    @endif

                                    {{-- Indicator badges --}}
                                     <div class="indicator-row">
                                         @if(!empty($dayData['late']) && $dayData['late'] != '00:00:00')
                                             @if($runningLateCount <= 3)
                                                 <span class="badge-pill-tag" style="background:rgba(253,126,20,0.15);color:#d05300;" title="{{ __('Late Mark #') . $runningLateCount . ' (Grace Warning) - Late by: ' . $dayData['late'] }}">L</span>
                                                 @php $title .= "\n" . __('Late Mark #') . $runningLateCount . ' (Grace Warning) - Late by: ' . $dayData['late']; @endphp
                                             @else
                                                 <span class="badge-pill-tag" style="background:rgba(220,53,69,0.15);color:#bd2130;" title="{{ __('Late Mark #') . $runningLateCount . ' (Half Day Deducted) - Late by: ' . $dayData['late'] }}">L</span>
                                                 @php $title .= "\n" . __('Late Mark #') . $runningLateCount . ' (Half Day Deducted) - Late by: ' . $dayData['late']; @endphp
                                             @endif
                                         @endif
                                         @if($dayData['type'] == 'half_day')
                                             @if(!empty($dayData['status_reason']) && strpos($dayData['status_reason'], 'Late Mark') !== false)
                                                 <span class="badge-pill-tag" style="background:#fd7e14;color:#fff;" title="{{ $dayData['status_reason'] }} (Late Policy Deduction)">H (Late)</span>
                                             @else
                                                 <span class="badge-pill-tag" style="background:var(--color-customColor, #5c59e8);color:#fff;" title="{{ !empty($dayData['status_reason']) ? $dayData['status_reason'] : __('Half Day') }}">H</span>
                                             @endif
                                         @endif
                                     </div>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>{{-- /attn-cal-grid --}}
            </div>{{-- /attn-cal-body --}}
        </div>{{-- /card --}}
    </div>

    @else
    {{-- No employee selected --}}
    <div class="col-12">
        <div class="attn-empty-state">
            <div class="attn-empty-icon">
                <i class="ti ti-calendar-search"></i>
            </div>
            <h5>{{ __('Select an employee to view their attendance calendar') }}</h5>
            <p class="text-muted mb-0">{{ __('Use the filter above to choose an employee, month and year.') }}</p>
        </div>
    </div>
    @endif

</div>{{-- /row --}}

<style>
/* =========================================================
   ATTENDANCE CALENDAR – MODERN UI
   Colors via CSS variable --color-customColor (theme engine)
   ========================================================= */

:root {
    --cal-primary: var(--color-customColor, #5c59e8);
    --cal-present-bg:  rgba(40, 167, 69, 0.10);
    --cal-present-bdr: rgba(40, 167, 69, 0.35);
    --cal-present-clr: #1a7a38;
    --cal-absent-bg:   rgba(220, 53, 69, 0.10);
    --cal-absent-bdr:  rgba(220, 53, 69, 0.35);
    --cal-absent-clr:  #b02a37;
    --cal-halfday-bg:  rgba(92, 89, 232, 0.10);
    --cal-halfday-bdr: rgba(92, 89, 232, 0.35);
    --cal-halfday-clr: #4543b5;
    --cal-leave-bg:    rgba(23, 162, 184, 0.10);
    --cal-leave-bdr:   rgba(23, 162, 184, 0.35);
    --cal-leave-clr:   #0f6674;
    --cal-weekoff-bg:  rgba(108, 117, 125, 0.08);
    --cal-weekoff-bdr: rgba(108, 117, 125, 0.25);
    --cal-weekoff-clr: #4a5058;
    --cal-cell-bdr: #e9ecef;
    --cal-head-bg: #f7f8fc;
}

/* ── Filter card ── */
.attn-filter-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--cal-cell-bdr);
    padding: 22px 26px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.filter-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}
.filter-field {
    min-width: 160px;
}
.btn-filter {
    height: 38px;
    padding: 0 18px;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

/* ── Calendar card ── */
.attn-calendar-card {
    border-radius: 16px;
    border: 1px solid var(--cal-cell-bdr);
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    overflow: hidden;
}

/* ── Calendar header ── */
.attn-calendar-header {
    background: linear-gradient(135deg, var(--cal-primary) 0%, color-mix(in srgb, var(--cal-primary) 70%, #000 30%) 100%);
    padding: 22px 26px;
    color: #fff;
}
/* fallback for browsers without color-mix */
@supports not (background: color-mix(in srgb, red 50%, blue 50%)) {
    .attn-calendar-header {
        background: linear-gradient(135deg, var(--cal-primary) 0%, #3a38a8 100%);
    }
}
.attn-emp-avatar {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(255,255,255,0.22);
    border: 2px solid rgba(255,255,255,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: 1px;
    backdrop-filter: blur(4px);
    flex-shrink: 0;
}
.attn-emp-info h5 { color: #fff; }
.attn-month-badge {
    font-size: 0.8rem;
    background: rgba(255,255,255,0.18);
    color: rgba(255,255,255,0.9);
    border-radius: 20px;
    padding: 3px 10px;
    display: inline-block;
    margin-top: 4px;
}
.btn-nav-month {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff !important;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.82rem;
    padding: 6px 14px;
    transition: background 0.2s, transform 0.15s;
    display: inline-flex;
    align-items: center;
    backdrop-filter: blur(4px);
}
.btn-nav-month:hover {
    background: rgba(255,255,255,0.28);
    transform: translateY(-1px);
}

/* ── Legend bar ── */
.attn-legend-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 18px;
    padding: 14px 22px;
    background: var(--cal-head-bg);
    border-bottom: 1px solid var(--cal-cell-bdr);
    font-size: 0.78rem;
    color: #555;
}
.attn-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}
.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
    display: inline-block;
}
.badge-pill-tag {
    font-size: 0.65rem;
    font-weight: 700;
    border-radius: 6px;
    padding: 2px 5px;
    line-height: 1.4;
}

/* ── Calendar body & grid ── */
.attn-cal-body {
    padding: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.attn-cal-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    border-left: 1px solid var(--cal-cell-bdr);
    border-top: 1px solid var(--cal-cell-bdr);
    min-width: 630px;
}

/* Day headers */
.attn-day-head {
    padding: 10px 6px;
    text-align: center;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6c757d;
    background: var(--cal-head-bg);
    border-right: 1px solid var(--cal-cell-bdr);
    border-bottom: 1px solid var(--cal-cell-bdr);
}
.attn-day-head.weekend-head { color: #c0392b; }

/* Day cells */
.attn-day {
    min-height: 108px;
    padding: 10px 9px 8px;
    border-right: 1px solid var(--cal-cell-bdr);
    border-bottom: 1px solid var(--cal-cell-bdr);
    position: relative;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    background: #fff;
}
.attn-day:hover:not(.attn-day--empty) {
    transform: scale(1.02);
    z-index: 2;
    box-shadow: 0 6px 18px rgba(0,0,0,0.10);
}

.attn-day--empty {
    background: #fafbfc;
    min-height: 108px;
}
.day--weekend-bg { background: #fafafa; }

/* Status backgrounds */
.day--present  { background: var(--cal-present-bg);  border-left: 3px solid var(--cal-present-bdr); }
.day--absent   { background: var(--cal-absent-bg);   border-left: 3px solid var(--cal-absent-bdr); }
.day--halfday  { background: var(--cal-halfday-bg);  border-left: 3px solid var(--cal-halfday-bdr); }
.day--late-halfday { background: rgba(253, 126, 20, 0.08) !important; border-left: 3px solid rgba(253, 126, 20, 0.5) !important; color: #bc5000 !important; }
.day--late-halfday .status-icon-wrap i { color: #fd7e14; }
.day--leave    { background: var(--cal-leave-bg);    border-left: 3px solid var(--cal-leave-bdr); }
.day--weekoff  { background: var(--cal-weekoff-bg);  border-left: 3px solid var(--cal-weekoff-bdr); }

/* Today */
.day--today {
    box-shadow: inset 0 0 0 2px var(--cal-primary) !important;
    border-left-width: 3px;
    z-index: 1;
}
.day-num {
    font-size: 0.92rem;
    font-weight: 700;
    color: #344050;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1;
}
.day-num--today { color: var(--cal-primary); }
.today-indicator {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--cal-primary);
    display: inline-block;
    animation: pulse-dot 1.6s infinite;
}
@keyframes pulse-dot {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.4); opacity: 0.7; }
}

/* Day content area */
.day-content {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.status-icon-wrap {
    font-size: 1.05rem;
    line-height: 1;
    margin-bottom: 1px;
}
.day--present  .status-icon-wrap { color: var(--cal-present-clr); }
.day--absent   .status-icon-wrap { color: var(--cal-absent-clr); }
.day--halfday  .status-icon-wrap { color: var(--cal-halfday-clr); }
.day--leave    .status-icon-wrap { color: var(--cal-leave-clr); }
.day--weekoff  .status-icon-wrap { color: var(--cal-weekoff-clr); }

/* Time rows */
.time-row {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #444;
    line-height: 1.3;
}
.time-icon { font-size: 0.7rem; }
.in-icon  { color: #28a745; }
.out-icon { color: #dc3545; }

/* Status label */
.day-label {
    font-size: 0.68rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #555;
    max-width: 100%;
}

/* Indicator badges */
.indicator-row {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    margin-top: 3px;
}
.ind-badge {
    font-size: 0.6rem;
    font-weight: 800;
    border-radius: 5px;
    padding: 1px 5px;
    line-height: 1.5;
    letter-spacing: 0.03em;
}
.ind-late    { background: rgba(220,53,69,0.15);  color: #bd2130; }
.ind-nearlate { background: rgba(230,184,0,0.2);   color: #8a6d00; }
.ind-early   { background: rgba(255,193,7,0.2);   color: #856404; }

/* ── Empty state ── */
.attn-empty-state {
    background: #fff;
    border-radius: 16px;
    border: 2px dashed var(--cal-cell-bdr);
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.attn-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(92,89,232,0.12) 0%, rgba(23,162,184,0.12) 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    font-size: 2.4rem;
    color: var(--cal-primary);
}
.attn-empty-state h5 {
    font-weight: 700;
    color: #344050;
    margin-bottom: 8px;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .attn-filter-card { padding: 16px; }
    .filter-field { min-width: 100px; }
    .btn-filter { padding: 0 12px; }
    .attn-cal-grid { min-width: 100%; }
    .attn-day { min-height: 72px; padding: 6px 5px; }
    .attn-day:hover { transform: none; }
    .attn-day-head { padding: 8px 2px; font-size: 0.65rem; }
    .day-num { font-size: 0.78rem; }
    .time-row { font-size: 0.6rem; }
    .status-icon-wrap { font-size: 0.85rem; }
    .attn-legend-bar { padding: 10px 14px; gap: 5px 12px; font-size: 0.72rem; }
    .attn-calendar-header { padding: 16px 18px; }
    .attn-emp-avatar { width: 42px; height: 42px; font-size: 0.9rem; border-radius: 12px; }
    .btn-nav-month { padding: 6px 10px; font-size: 0.8rem; }
}
@media (max-width: 480px) {
    .attn-cal-grid { min-width: 100%; }
    .attn-day { min-height: 54px; padding: 4px 2px; }
    .day-num { font-size: 0.7rem; margin-bottom: 2px; }
    .time-row { display: none; }
    .day-label { font-size: 0.58rem; }
    .ind-badge { padding: 1px 3px; font-size: 0.55rem; }
    .attn-emp-info h5 { font-size: 1rem; }
    .attn-calendar-header { padding: 12px 14px; }
}
</style>
@endsection
