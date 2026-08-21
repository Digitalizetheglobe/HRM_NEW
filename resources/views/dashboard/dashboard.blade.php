    @extends('layouts.admin')

    @section('content')
    <style>
        .fc-prev-button, .fc-next-button {
            padding: 5px 8px !important;
            font-size: 14px !important;
            background-color: #007bff !important;
            border-radius: 5px !important;
            border: none !important;
            color: white !important;
        }

        .fc-prev-button:hover, .fc-next-button:hover {
            background-color: #0056b3 !important;
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
            background-color: #d4edda !important; /* Green for present */
        }
        
        .fc-daygrid-day.hours-absent {
            background-color: #f8d7da !important; /* Red for absent */
        }

        .fc-daygrid-day.hours-weekoff {
            background-color: #e9ecef !important; /* Grey for week off */
        }

        .fc-daygrid-day.hours-leave {
            background-color: #fff3cd !important; /* Yellow for leave */
        }

        .fc-daygrid-day.hours-holiday {
            background-color: #cce5ff !important; /* Blue for holiday */
        }

        /* Event Card General */
        .events-card {
            border-radius: 14px !important;
            box-shadow: 0 4px 24px rgba(102,126,234,0.14) !important;
            overflow: hidden;
        }
        .events-scroll-body::-webkit-scrollbar { width: 4px; }
        .events-scroll-body::-webkit-scrollbar-track { background: #f1f3f9; }
        .events-scroll-body::-webkit-scrollbar-thumb { background: #c5c8e8; border-radius: 10px; }

        /* Each event row */
        .ev-row {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f2fa;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .ev-row:last-child { border-bottom: none; }
        .ev-row:hover { background: #f6f7ff; }
        .ev-row:hover .ev-icon { transform: scale(1.08); box-shadow: 0 5px 16px rgba(0,0,0,0.18) !important; }
        .ev-row:hover .ev-arrow { transform: translateX(3px); color: #667eea !important; }

        /* Icon */
        .ev-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.13);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .ev-icon i { color: #fff; font-size: 17px; }

        /* Text block */
        .ev-info { flex: 1; overflow: hidden; min-width: 0; }
        .ev-title {
            font-size: 13px;
            font-weight: 600;
            color: #1a1f36;
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
        .ev-date { font-size: 10px; color: #9faabb; white-space: nowrap; }

        /* Chevron */
        .ev-arrow { color: #d0d7e8; font-size: 13px; flex-shrink: 0; transition: transform 0.18s, color 0.18s; }

        /* Empty state */
        .ev-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
            gap: 10px;
        }
        .ev-empty-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg,#e0e7ff,#c7d2fe);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
        }
        .ev-empty-icon i { font-size: 26px; color: #818cf8; }
        .ev-empty p { color: #9ca3af; font-size: 12px; font-weight: 500; margin: 0; }

        /* Avatar with ring */
        .ev-avatar-wrap {
            position: relative;
            width: 42px;
            height: 42px;
            min-width: 42px;
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

    <div>
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if (\Auth::user()->type == 'employee')

                <div class="col-xxl-9">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                @if($emp)
                                <div class="col-xl-6">
                                    <div class="card">
                                        <div class="card-header d-flex align-items-center" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                            <img src="{{ asset('storage/uploads/avatar/' . ($emp->user->avatar ?? 'default-avatar.png')) }}"
                                                alt="Profile Image"
                                                class="rounded-circle me-4"
                                                width="60"
                                                height="60">
                                            <div>
                                                <h4 class="mb-0" style="color:white;">{{ $emp->full_name }}</h4>
                                                <small style="font-size: 12px; color:rgba(255,255,255,0.8);">{{ $emp->department->name ?? 'No Department' }} Team</small><small style="font-size:16px; color:rgba(255,255,255,0.8);"> &nbsp{{ $emp->designation->name ?? 'No Designation' }}&nbsp</small><br>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Phone Number:<br></strong> {{ $emp->phone ?? 'N/A' }}</p><br>
                                            <p><strong>Email Address:<br></strong> {{ $emp->email ?? 'N/A' }}</p><br>
                                            <p><strong>Joined On:<br></strong> {{ \Carbon\Carbon::parse($emp->company_doj)->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="card h-90">
                                        <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-clock" style="color:#fff;font-size:20px;"></i>
                                                <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Attendance Overview') }}</span>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm dropdown-toggle" type="button" id="attendanceFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:rgba(255,255,255,0.22); color:#fff; border:none; border-radius:20px; font-size:12px; font-weight:600; padding:4px 12px; box-shadow:none;">
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
                            <div class="card">
                                <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-briefcase" style="color:#fff;font-size:20px;"></i>
                                        <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Project Details') }}</span>
                                    </div>
                                    <span style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($assignedProjects ?? []) }}</span>
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
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Completed';
                                                } elseif($project->status == 'on_hold') {
                                                    $statusClass = 'bg-warning text-dark';
                                                    $statusText = 'On Hold';
                                                } else {
                                                    $statusClass = 'bg-primary';
                                                    $statusText = 'In Progress';
                                                }
                                            @endphp
                                            <a href="{{ route('projects.show', $project->id) }}" 
                                               class="list-group-item list-group-item-action d-flex align-items-center p-3 border-bottom text-decoration-none"
                                               style="border-left: 3px solid transparent; transition: all 0.2s ease-in-out;"
                                               onmouseover="this.style.borderLeftColor='#393970ff'; this.style.backgroundColor='#f8f9fa';"
                                               onmouseout="this.style.borderLeftColor='transparent'; this.style.backgroundColor='transparent';">
                                                
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background-color: rgba(57, 57, 112, 0.1);">
                                                    <i class="ti ti-briefcase" style="color: #393970ff; font-size: 20px;"></i>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden pe-3">
                                                    <h6 class="mb-1 text-dark fw-bold text-truncate" style="font-size: 14px;">{{ $project->project_name }}</h6>
                                                    <p class="text-muted mb-0 text-truncate" style="font-size: 12px; margin-bottom:0;">
                                                        <span class="badge {{ $statusClass }} me-1" style="font-size: 10px; padding: 2px 6px;">{{ $statusText }}</span>
                                                        <i class="ti ti-calendar ms-1 me-1"></i> {{ \Carbon\Carbon::parse($project->project_startdate)->format('d M') }} - {{ \Carbon\Carbon::parse($project->project_enddate)->format('d M Y') }}
                                                        <span class="ms-2"><i class="ti ti-list-check me-1"></i> {{ $modulesText }}</span>
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
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
                                    <div class="card">
                                        <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-bell" style="color:#fff;font-size:20px;"></i>
                                                <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Notices') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($notices) }}</span>
                                                @if(\Auth::user()->type == 'company' || \Auth::user()->type == 'hr')
                                                <a href="#" data-url="{{ route('notices.create') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Notice') }}" class="btn btn-sm d-flex align-items-center" style="background:rgba(255,255,255,0.2); color:#fff; border-radius: 6px; padding: 4px 12px; border: 1px solid rgba(255,255,255,0.3); transition: background 0.2s; text-decoration: none;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                                    <i class="ti ti-plus me-1" style="font-size: 14px;"></i><span style="font-weight: 600;">{{ __('Add') }}</span>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-0" style="height: 325px; overflow-y: auto; overflow-x: hidden;">
                                            <div class="list-group list-group-flush">
                                                @forelse($notices as $notice)
                                                <a href="#" 
                                                   class="list-group-item list-group-item-action d-flex align-items-center p-3 border-bottom text-decoration-none"
                                                   data-url="{{ route('notices.show', $notice->id) }}" 
                                                   data-ajax-popup="true" 
                                                   data-size="lg" 
                                                   data-title="{{ __('Notice Details') }}"
                                                   style="border-left: 3px solid transparent; transition: all 0.2s ease-in-out;"
                                                   onmouseover="this.style.borderLeftColor='#393970ff'; this.style.backgroundColor='#f8f9fa';"
                                                   onmouseout="this.style.borderLeftColor='transparent'; this.style.backgroundColor='transparent';">
                                                    
                                                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background-color: rgba(57, 57, 112, 0.1);">
                                                        <i class="ti ti-bell" style="color: #393970ff; font-size: 20px;"></i>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden pe-3">
                                                        <h6 class="mb-1 text-dark fw-bold text-truncate" style="font-size: 14px;">{{ $notice->title }}</h6>
                                                        <p class="text-muted mb-0 text-truncate" style="font-size: 12px; margin-bottom:0;">{{ strip_tags($notice->description) }}</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <i class="ti ti-chevron-right text-muted" style="opacity: 0.5;"></i>
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
                                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                                        <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-calendar-time" style="color:#fff;font-size:20px;"></i>
                                                <span id="scheduleFilterTitle" style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Today\'s Schedules') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm dropdown-toggle" type="button" id="scheduleFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:rgba(255,255,255,0.22); color:#fff; border:none; border-radius:20px; font-size:12px; font-weight:600; padding:4px 12px; box-shadow:none;">
                                                        <span id="selectedScheduleFilterText">Today</span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="scheduleFilterDropdown">
                                                        <li><a class="dropdown-item schedule-filter-option active" href="javascript:void(0)" data-filter="today"><i class="fas fa-calendar-day me-2"></i>Today</a></li>
                                                        <li><a class="dropdown-item schedule-filter-option" href="javascript:void(0)" data-filter="yesterday"><i class="fas fa-calendar-minus me-2"></i>Yesterday</a></li>
                                                        <li><a class="dropdown-item schedule-filter-option" href="javascript:void(0)" data-filter="custom"><i class="fas fa-calendar me-2"></i>Select Date</a></li>
                                                    </ul>
                                                    <input type="date" id="scheduleCustomDatePicker" style="position: absolute; opacity: 0; width: 1px; height: 1px; top: 0; left: 0; border: none; padding: 0;" onchange="handleScheduleDateSelect(this.value)">
                                                </div>
                                                <button type="button" class="btn btn-sm d-flex align-items-center" style="background:rgba(255,255,255,0.2); color:#fff; border-radius: 6px; padding: 4px 12px; border: 1px solid rgba(255,255,255,0.3); transition: background 0.2s;" data-bs-toggle="modal" data-bs-target="#addScheduleModal" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                                    <i class="ti ti-plus me-1" style="font-size: 14px;"></i><span style="font-weight: 600;">{{ __('Add') }}</span>
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
                            <div class="card">
                                        <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-user-off" style="color:#fff;font-size:20px;"></i>
                                                <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Employees On Leave / WeekOff') }}</span>
                                            </div>
                                            <span style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($employeesNotWorkingToday) }}</span>
                                        </div>
                                        <div class="card-body" style="height: 300px; overflow: auto; padding-top:25px;">
                                            <div class="table-responsive">
                                                <table class="table table-bordered text-center" id="onLeaveTable">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Employee Name') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($employeesNotWorkingToday as $employee)
                                                            <tr>
                                                                <td>{{ $employee['employee_name'] }}</td>
                                                                <td>{{ $employee['status'] }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="2">All employees are working today</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-video" style="color:#fff;font-size:20px;"></i>
                                        <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Meeting List') }}</span>
                                    </div>
                                    <span style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($meetings) }}</span>
                                </div>
                                <div class="card-body" style="height: 324px; overflow:auto;">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Meeting Title') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Time') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($meetings as $meeting)
                                                    <tr>
                                                        <td>{{ $meeting->title }}</td>
                                                        <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                                        <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                                    </tr>
                                                @endforeach

                                                @if ($meetings->isEmpty())
                                                    <tr>
                                                        <td colspan="3" class="text-center">{{ __('No meetings found') }}</td>
                                                    </tr>
                                                @endif
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
                        <div class="card events-card" style="height:180px;">
                            {{-- Header --}}
                            <div class="events-card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-calendar-event" style="color:#fff;font-size:20px;"></i>
                                    <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __("Upcoming Events") }}</span>
                                </div>
                                @if(count($allEvents) > 0)
                                <span style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($allEvents) }}</span>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div style="max-height:300px;overflow-y:auto;overflow-x:hidden; height:100%;" class="events-scroll-body">
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

                        <div class="card flex-grow-1">
                            <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-calendar" style="color:#fff;font-size:20px;"></i>
                                    <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Calendar') }}</span>
                                </div>
                            </div>
                            <div class="card-body" style="padding-top:0px;">
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
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-bottom: none; padding: 20px 25px;">
                    <h5 class="modal-title text-white d-flex align-items-center" id="addScheduleModalLabel">
                        <i class="ti ti-calendar-plus me-2" style="font-size: 22px;"></i>{{ __('Add Today\'s Task') }}
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
                    <button type="submit" form="addScheduleForm" class="btn btn-primary" style="border-radius: 8px; background: #0b0b39ff; border-color: #0b0b39ff; font-weight: 600; padding: 8px 20px; box-shadow: 0 4px 10px rgba(11, 11, 57, 0.2);">{{ __('Save Task') }}</button>
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

        /**
         * Submit attendance - simplified without location requirements
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
            showMessage('info', '⏳ Recording your attendance...');

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append(type === 'in' ? 'in' : 'out', '1');

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

                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

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
            
            // Use the calculated hours, but don't let it exceed server hours by too much
            // (server hours are more accurate as they use the correct attendance date)
            if (calculatedHours > serverHours + 0.5) {
                // If calculated is significantly more than server, there's a date mismatch
                // Use server hours
                return Math.min(serverHours, maxHoursForDay);
            }
            
            return Math.min(calculatedHours, maxHoursForDay);
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
        badge.textContent = `${percentage.toFixed(1)}%`;
        // Update badge color if late
        if (isLate) {
            badge.className = 'badge bg-danger';
        }
    }
    
    if (progressBar) {
        // Smooth transition for progress bar width
        progressBar.style.transition = 'width 0.5s ease';
        progressBar.style.width = `${Math.min(percentage, 100)}%`;
        progressBar.setAttribute('aria-valuenow', hoursCompleted);
        progressBar.textContent = `${percentage.toFixed(1)}%`;
        // Update progress bar color if late
        if (isLate) {
            progressBar.className = 'progress-bar bg-danger';
        } else {
            progressBar.className = `progress-bar ${hoursCompleted >= totalHours ? 'bg-primary' : 'bg-primary'}`;
            progressBar.style.backgroundColor = '';
            badge.className = `badge ${hoursCompleted >= totalHours ? 'bg-primary' : 'bg-primary'}`;
            badge.style.backgroundColor = '';
            if (hoursCompleted >= totalHours) badge.style.color = '#fff';
        }
    }
    
    // Update hours completed label to show "(Late Punch-In)" if late
    if (hoursCompletedLabel && isLate && !hoursCompletedLabel.innerHTML.includes('Late Punch-In')) {
        hoursCompletedLabel.innerHTML = hoursCompletedLabel.innerHTML.replace('Hours Completed', 'Hours Completed <span class="text-danger">(Late Punch-In)</span>');
    }
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
                
                const progressBarClass = isLate ? 'bg-danger' : 'bg-primary';
                const badgeClass = isLate ? 'bg-danger' : 'bg-primary';
                const lateText = isLate ? ' <span class="text-danger">(Late Punch-In)</span>' : '';
                
                if (filterType === 'date' && !isSelectedDateToday) {
                    // For selected date (not today), show punch in and punch out times with progress bar
                    html = `
                        <div class="attendance-detail">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Hours Completed${lateText}</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0">${Math.floor(hoursCompleted)} hours ${Math.round((hoursCompleted - Math.floor(hoursCompleted)) * 60)} minutes / ${totalHours} hours</span>
                                    <span class="badge ${badgeClass}">${percentage.toFixed(1)}%</span>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar ${progressBarClass}" 
                                         role="progressbar" 
                                         style="width: ${Math.min(percentage, 100)}%;" 
                                         aria-valuenow="${hoursCompleted}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="${totalHours}">
                                        ${percentage.toFixed(1)}%
                                    </div>
                                </div>
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
                                    <span class="badge ${badgeClass}">${percentage.toFixed(1)}%</span>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar ${progressBarClass}" 
                                         role="progressbar" 
                                         style="width: ${Math.min(percentage, 100)}%;" 
                                         aria-valuenow="${hoursCompleted}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="${totalHours}">
                                        ${percentage.toFixed(1)}%
                                    </div>
                                </div>
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
                
                html = `
                    <div class="attendance-detail">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Hours Completed</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0">${hoursCompleted}/${totalHours} hours</span>
                                <span class="badge bg-primary">${percentage.toFixed(1)}%</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-primary" 
                                     role="progressbar" 
                                     style="width: ${Math.min(percentage, 100)}%;" 
                                     aria-valuenow="${hoursCompleted}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="${totalHours}">
                                    ${percentage.toFixed(1)}%
                                </div>
                            </div>
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
                            <button type="button" class="btn btn-sm btn-outline-primary" id="prevWeekBtn">
                                <i class="fas fa-chevron-left"></i> Previous Week
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="nextWeekBtn">
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
                
                html = `
                    <div class="attendance-detail">
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Hours Completed</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0">${hoursCompleted}/${totalHours} hours</span>
                                <span class="badge bg-primary">${percentage.toFixed(1)}%</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-primary" 
                                     role="progressbar" 
                                     style="width: ${Math.min(percentage, 100)}%;" 
                                     aria-valuenow="${hoursCompleted}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="${totalHours}">
                                    ${percentage.toFixed(1)}%
                                </div>
                            </div>
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
