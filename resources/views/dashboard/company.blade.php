@extends('layouts.admin')



@php
    $setting = App\Models\Utility::settings();
@endphp

@section('content')
<style>

    /* ===== Events Card ===== */
    .events-card {
        border: none;
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

    /* Custom hover card */
    .hover-employee-card .hover-department-card .hover-leave-card .hover-holiday-card .hover-ticket-card .hover-project-card {
        transition: background-color 0.3s ease;
    }
    .hover-employee-card:hover {
        background-color: #ead9ecff !important;
    }

    .hover-department-card:hover {
        background-color: #d1e6edff !important;
    }

    .hover-leave-card:hover {
        background-color: #ceefd5ff !important;
    }

    .hover-holiday-card:hover {
        background-color: #d0cdedff !important;
    }

    .hover-ticket-card:hover {
        background-color: #f1d1e0ff !important;
    }  
    
    .hover-project-card:hover {
        background-color: #f4e0d7ff !important;
    }

    .fc-prev-button, .fc-next-button {
        padding: 6px 10px !important;
        font-size: 14px !important;
        background-color: #007bff !important;
        border-radius: 6px !important;
        border: none !important;
        color: white !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s ease;
    }

    .fc-prev-button:hover, .fc-next-button:hover {
        background-color: #0056b3 !important;
        transform: scale(1.05);
    }

    /* FullCalendar Responsive Header */
    .fc .fc-toolbar {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 15px !important;
        margin-bottom: 1.5em !important;
    }

    .fc .fc-toolbar-title {
        font-size: 1.2rem !important;
        margin: 0 !important;
        font-weight: 700 !important;
        color: #333 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
    }

    .fc .fc-button-group {
        display: flex !important;
        gap: 5px !important;
    }

    /* Calendar Grid Responsiveness */
    @media (max-width: 576px) {
        .fc .fc-toolbar-title {
            font-size: 1rem !important;
        }
        .fc .fc-col-header-cell-cushion,
        .fc .fc-daygrid-day-number {
            font-size: 0.8rem !important;
            padding: 4px !important;
        }
        .fc .fc-daygrid-day-top {
            justify-content: center !important;
        }
    }

    #calendar {
        margin-bottom: 10px;
    }

    .loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }

    .loading:after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7) url('{{ asset("assets/img/loading.gif") }}') no-repeat center;
        background-size: 50px 50px;
        z-index: 1000;
    }

    /* Dash content padding reduction for mobile */

    /* Tablet responsive (up to 1024px) */
    @media (max-width: 1024px) {
        .dash-content {
            padding-top: 10px !important;
        }
    }

    /* Mobile responsive (up to 768px) */
    @media (max-width: 768px) {
        .dash-content {
            padding-top: 8px !important;
        }
        .dashboard-header {
            margin-bottom: 15px !important;
        }
        .dashboard-header h3 {
            font-size: 20px;
        }
    }

    /* Small mobile responsive (up to 576px) */
    @media (max-width: 576px) {
        .dash-content {
            padding-top: 5px !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        .dashboard-header {
            margin-bottom: 10px !important;
            gap: 8px !important;
        }
        .dashboard-header h3 {
            font-size: 18px;
        }
        #customDatePicker {
            max-width: 130px !important;
            min-width: 110px !important;
            font-size: 12px !important;
            padding: 4px 6px !important;
        }
        #dateFilterButton {
            font-size: 12px !important;
            padding: 5px 10px !important;
        }
        .dropdown-menu {
            min-width: auto;
        }
        /* Cards responsive spacing */
        .card {
            margin-bottom: 10px !important;
        }
        .card-body {
            padding: 15px !important;
        }
    }

    @media (max-width: 400px) {
        .dashboard-header h3 {
            font-size: 16px;
        }
        #customDatePicker {
            max-width: 120px !important;
            min-width: 100px !important;
        }
    }

    #customDatePicker {
        height: 31.5px !important;
        padding: 6px 12px !important;
        font-size: 13px !important;
        border: 1px solid #ced4da;
        border-radius: 5px;
        background-color: #fff;
    }

    .dashboard-header h3 {
        transition: all 0.3s ease;
    }

    .dashboard-header.compact h3 {
        font-size: 1.1rem !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px; /* Limit width to ensure picker has room */
    }

    /* Mobile Stat Card Optimizations */
    @media (max-width: 576px) {
        .stat-card-body {
            padding: 12px !important;
        }
        .stat-icon-circle {
            width: 40px !important;
            height: 40px !important;
        }
        .stat-icon-circle i {
            font-size: 18px !important;
        }
        .stat-label h6 {
            font-size: 10px !important;
        }
        .stat-label h4 {
            font-size: 11px !important;
            font-weight: 700 !important;
        }
        .stat-count h4 {
            font-size: 20px !important;
            margin-top: 2px !important;
        }
        .br-mobile {
            display: none !important;
        }
        /* Optional: align items horizontally on mobile if they feel too tall */
        .stat-inner-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stat-data-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .dashboard-header.compact h3 {
            font-size: 0.9rem !important;
            max-width: 100px;
        }
    }
</style>
<div>
    <div class="row">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

    @if (Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'Director')

    <div class="row" style="padding-right: 0px;">
            <div class="d-flex justify-content-between align-items-center w-100 gap-2 dashboard-header" id="dashboardHeader" style="margin-bottom: 30px; flex-wrap: nowrap;">
                <div class="mb-0">
                    <h3 class="mb-0" id="dashboardGreeting">
                        @if(Auth::user()->type == 'employee')
                            {{ __('Welcome') }}, {{ Auth::user()->name }}
                        @else
                            {{ __('Welcome Admin') }}
                        @endif
                    </h3>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2" style="flex-shrink: 0;">
                    <div class="btn-group" style="z-index: 1;">
                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="dateFilterButton" style="white-space: nowrap; font-size: 13px; padding: 6px 12px;">
                            Today
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-value="today">Today</a></li>
                            <li><a class="dropdown-item" href="#" data-value="yesterday">Yesterday</a></li>
                            <li><a class="dropdown-item" href="#" data-value="custom">Select Date</a></li>
                        </ul>
                    </div>
                    <div class="position-relative" id="customDatePickerWrapper" style="display: none;">
                        <input type="date" class="form-control form-control-sm" id="customDatePicker" >
                    </div>
                </div>
            </div>
    </div>  
            <!-- Employee specific content -->
        


            <div class="col-xxl-9">
                <div class="row">
                    <!-- Left Side Cards -->
                    <div class="col-xl-12">

            
                       <div class="row">
                            <div class="col-xxl-12">
                                <div class="col-xl-12">
                                        <div class="row">
                                            <!-- first Card - Employees -->
                                            <div class="col-6 col-lg-4 col-md-4">
                                                <div class="card hover-employee-card" style="border-radius: 10px; border: 3px solid #E7E7E7;  background-color: #fff; cursor: pointer;" onclick="window.location.href='employee'">
                                                    <div class="card-body" style="padding: 20px;">
                                                        <div class="align-items-center">
                                                            <div class="col-auto">
                                                                <div style="background-color: #B55CC4; width: 50px; height: 50px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                                                    <i class="fa-solid fa-user-tie" style="font-size: 25px; color: #fff;"></i>
                                                                </div>
                                                            </div><br>
                                                            <div class="col-auto" style="display: flex; align-items: center; gap: 5px;">
                                                                <h6 style="font-size: 14px; color: #515356; margin: 0;">Total</h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 15px; color:#555657 !important; font-weight: 800; margin: 0;">Employees</h4>
                                                            </div>
                                                            <div class="col-auto">
                                                                <h6 style="font-size: 14px; color: #0569a6;"> </h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 30px; color : #000 !important; "> {{ $countEmployee }}  </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- second Card - Department -->
                                            <div class="col-6 col-lg-4 col-md-4">
                                                <div class="card hover-department-card" style="border-radius: 10px; border: 3px solid #E7E7E7;  background-color: #fff; cursor: pointer;" onclick="window.location.href='{{ url('department') }}'">
                                                    <div class="card-body" style="padding: 20px;">
                                                        <div class="align-items-center">
                                                            <div class="col-auto">
                                                                <div style="background-color: #299dc6; width: 50px; height: 50px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                                                    <i class="fa-solid fa-sitemap"  style="font-size: 25px; color: #fff;"></i>
                                                                </div>
                                                            </div><br>
                                                            <div class="col-auto" style="display: flex; align-items: center; gap: 5px;">
                                                                <h6 style="font-size: 14px; color: #515356; margin: 0;">Total</h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 15px; color: #555657 !important; font-weight: 800; margin: 0;">Department</h4>
                                                            </div>
                                                            <div class="col-auto">
                                                                <h6 style="font-size: 14px; color: #0569a6;"> </h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 30px; color : #000 !important; "> {{ $totalDepartment }}  </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Third Card - Leaves -->
                                            <div class="col-6 col-lg-4 col-md-4">
                                                <div class="card hover-leave-card" style="border-radius: 10px; border: 3px solid #E7E7E7;  background-color: #fff; cursor: pointer;" onclick="window.location.href='leave'">
                                                    <div class="card-body" style="padding: 20px;">
                                                        <div class="align-items-center">
                                                            <div class="col-auto">
                                                                <div style="background-color: #28a745; width: 50px; height: 50px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                                                    <i class="fa-solid fa-calendar" style="font-size: 25px; color: #fff;"></i>
                                                                </div>
                                                            </div><br>
                                                            <div class="col-auto" style="display: flex; align-items: center; gap: 5px;">
                                                                <h6 style="font-size: 14px; color: #515356; margin: 0;">Total</h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 15px; color: #555657 !important; font-weight: 800; margin: 0;">Leaves</h4>
                                                            </div>
                                                            <div class="col-auto">
                                                                <h6 style="font-size: 14px; color: #6c757d;"> </h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 30px; color:#000 !important; " id="totalLeavesCount"> {{ $totalleaves }}  </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Fourth Card - Holidays -->
                                            <div class="col-6 col-lg-4 col-md-4">
                                                <div class="card hover-holiday-card" style="border-radius: 10px; border: 3px solid #E7E7E7;  background-color: #fff; cursor: pointer;" onclick="window.location.href='holiday'">
                                                    <div class="card-body" style="padding: 20px;">
                                                        <div class="align-items-center">
                                                            <div class="col-auto">
                                                                <div style="background-color: #4c39c9; width: 50px; height: 50px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                                                    <i class="fa-solid fa-calendar-days" style="font-size: 25px; color: #fff;"></i>
                                                                </div>
                                                            </div><br>
                                                            <div class="col-auto" style="display: flex; align-items: center; gap: 5px;">
                                                                <h6 style="font-size: 14px; color: #515356; margin: 0;">Total</h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 15px; color: #555657 !important; font-weight: 800; margin: 0;">Holidays</h4>
                                                            </div>
                                                            <div class="col-auto">
                                                                <h6 style="font-size: 14px; color: #0569a6;"> </h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 30px; color : #000 !important; "> {{ $totalHolidays }}  </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <!-- fifth Card - Projects -->
                                            <div class="col-6 col-lg-4 col-md-4">
                                                <div class="card hover-project-card" style="border-radius: 10px; border: 3px solid #E7E7E7;  background-color: #fff; cursor: pointer;" onclick="window.location.href='projects'">
                                                    <div class="card-body" style="padding: 20px;">
                                                        <div class="align-items-center">
                                                            <div class="col-auto">
                                                                <div style="background-color: #F26522; width: 50px; height: 50px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                                                    <i class="fa-solid fa-diagram-project" style="font-size: 25px; color: #fff;"></i>
                                                                </div>
                                                            </div><br>
                                                            <div class="col-auto" style="display: flex; align-items: center; gap: 5px;">
                                                                <h6 style="font-size: 14px; color: #515356; margin: 0;">Total</h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 15px; color: #555657 !important; font-weight: 800; margin: 0;">Projects</h4>
                                                            </div>
                                                            <div class="col-auto">
                                                                <h6 style="font-size: 14px; color: #6c757d;"> </h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 30px; color:#000 !important; "> {{ $totalProjects }}  </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Six Card - Ticket -->
                                            <div class="col-6 col-lg-4 col-md-4">
                                                <div class="card hover-ticket-card" style="border-radius: 10px; border: 3px solid #E7E7E7;  background-color: #fff; cursor: pointer;" onclick="window.location.href='ticket'">
                                                    <div class="card-body" style="padding: 20px;">
                                                        <div class="align-items-center">
                                                            <div class="col-auto">
                                                                <div style="background-color: #FD3995; width: 50px; height: 50px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                                                    <i class="fa-solid fa-ticket" style="font-size: 25px; color: #fff;"></i>
                                                                </div>
                                                            </div><br>
                                                            <div class="col-auto" style="display: flex; align-items: center; gap: 5px;">
                                                                <h6 style="font-size: 14px; color: #515356; margin: 0;">Total</h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 15px; color: #555657 !important; font-weight: 800; margin: 0;">Ticket</h4>
                                                            </div>
                                                            <div class="col-auto">
                                                                <h6 style="font-size: 14px; color: #6c757d;"> </h6>
                                                                <h4 class="m-0 text-primary" style="font-size: 30px; color:#000 !important; "> {{ $countTicket }}  </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>


                        <!-- Today's Attendance & Yet To Arrive Cards in One Line -->
                        <div class="row">
                            <div class="col-12 col-md-8 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-users" style="color:#fff;font-size:20px;"></i>
                                            <span id="attendanceFilterTitle" style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __("Today's Attendance") }}</span>
                                        </div>
                                        <span id="attendanceCount" style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($presentEmployeesWithClockIn) }}</span>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow: auto; padding: ; padding-top:25px;">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-left" id="attendanceTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Employee Name') }}</th>
                                                        <th>{{ __('Punch-In Time') }}</th>
                                                        <th>{{ __('Punch-Out Time') }}</th>
                                                        <th>{{ __('Total Working Hours') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($presentEmployeesWithClockIn as $data)
                                                        @php
                                                            $timeline = '--:--';
                                                            if (!empty($data['clock_in']) && $data['clock_in'] !== '--:--') {
                                                                try {
                                                                    $clockIn = \Carbon\Carbon::parse($data['clock_in']);
                                                                    if (!empty($data['clock_out']) && $data['clock_out'] !== '--:--') {
                                                                        $clockOut = \Carbon\Carbon::parse($data['clock_out']);
                                                                        $diff = $clockIn->diff($clockOut);
                                                                    } else {
                                                                        $diff = $clockIn->diff(\Carbon\Carbon::now());
                                                                    }
                                                                    $timeline = $diff->h . ' hours ' . $diff->i . ' minutes';
                                                                } catch (\Exception $e) {
                                                                    $timeline = '--:--';
                                                                }
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $data['employee']->name ?? 'N/A' }}</td>
                                                            <td>{{ $data['clock_in'] ?? '--:--' }}</td>
                                                            <td>{{ $data['clock_out'] ?? '--:--' }}</td>
                                                            <td>{{ $timeline }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4">{{ __('No attendance records found for today.') }}</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Yet To Arrive Card -->
                            <div class="col-12 col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-user-x" style="color:#fff;font-size:20px;"></i>
                                            <span id="notClockInFilterTitle" style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Yet To Arrive') }}</span>
                                        </div>
                                        <span id="notClockInCount" style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($notClockIns) }}</span>
                                    </div>
                                    <div class="card-body" style="height: 300px; overflow: auto; padding: ; padding-top:25px;">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-left" id="notClockInTable">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Employee Name') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($notClockIns as $employee)
                                                        <tr>
                                                            <td>{{ $employee->full_name ?? 'N/A' }}</td>
                                                            <td style="color: red;">Absent</td>
                                                        </tr>
                                                    @endforeach
                                                    @if(count($notClockIns) == 0)
                                                        <tr>
                                                            <td colspan="2">All employees are present </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-briefcase" style="color:#fff;font-size:20px;"></i>
                                            <span style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Project Details') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="#" data-url="{{ route('projects.create') }}" data-ajax-popup="true" data-title="{{ __('Create Project') }}" data-size="lg" class="btn btn-sm d-flex align-items-center" style="background:rgba(255,255,255,0.2); color:#fff; border-radius: 6px; padding: 4px 12px; border: 1px solid rgba(255,255,255,0.3); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                                <i class="ti ti-plus me-1" style="font-size: 14px;"></i><span style="font-weight: 600;">{{ __('Add') }}</span>
                                            </a>
                                            <span style="background:rgba(255,255,255,0.22);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;line-height:1.6;">{{ count($projects) }}</span>
                                        </div>
                                    </div>
                                <div class="card-body p-0" style="height: 325px; overflow-y: auto; overflow-x: hidden;">
                                    <div class="list-group list-group-flush">
                                        @forelse ($projects as $project)
                                            @php
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

                                                $assignedNames = [];
                                                if(is_array($project->assigned_data)) {
                                                    foreach($project->assigned_data as $assignment) {
                                                        foreach($assignment['employee_ids'] ?? [] as $employeeId) {
                                                            if(isset($employees[$employeeId])) {
                                                                $assignedNames[] = $employees[$employeeId]->user->name ?? __('Unknown');
                                                            }
                                                        }
                                                    }
                                                }
                                                $assignedText = count($assignedNames) > 0 ? implode(', ', $assignedNames) : __('Unassigned');
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
                                                        @if(Auth::user()->type != 'employee')
                                                            <span class="ms-2"><i class="ti ti-users me-1"></i> {{ $assignedText }}</span>
                                                        @endif
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
                            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                                <div class="card-header d-flex align-items-center justify-content-between" style="background:linear-gradient(135deg, #0b0b39ff 0%, #393970ff 100%); border-radius: 10px 10px 0 0; padding: 12px 16px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ti ti-calendar-time" style="color:#fff;font-size:20px;"></i>
                                        <span id="scheduleFilterTitle" style="font-size:14px;font-weight:700;color:#fff;letter-spacing:0.2px;">{{ __('Today\'s Schedules') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm d-flex align-items-center" style="background:rgba(255,255,255,0.2); color:#fff; border-radius: 6px; padding: 4px 12px; border: 1px solid rgba(255,255,255,0.3); transition: background 0.2s;" data-bs-toggle="modal" data-bs-target="#addScheduleModal" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                            <i class="ti ti-plus me-1" style="font-size: 14px;"></i><span style="font-weight: 600;">{{ __('Add') }}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="padding: 20px 20px 20px 20px;">
                                    <div id="schedulesContainer">
                                        @include('dashboard.schedules_list')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
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
                                    <div class="card-body p-0" style="height: 300px; overflow-y: auto; overflow-x: hidden;">
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

                    </div>
                </div>
            </div>

              <!-- Right Side Calendar -->

                <div class="col-xxl-3" style="z-index: 0;">
                    <div class="d-flex flex-column gap-1 sticky-top" style="">
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

                                            {{-- Event Icon --}}
                                            <div class="ev-icon" style="background:linear-gradient(135deg,{{ $iconBg }});">
                                                <i class="{{ $icon }}"></i>
                                            </div>

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
                                    <input type="hidden" id="path_admin" value="{{ url('/') }}">
                                </div>
                                    <div class="col-lg-6">
                                        @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
                                            <select class="form-control" name="calender_type" id="calender_type"
                                                style="float: right; width: 1px;" onchange="get_data()">
                                                <option value="local_calender" selected="true">{{ __('Local Calendar') }}</option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            <div class="card-body " style="padding-top:0px;">
                                <div id='calendar'  class='calendar'></div>
                            </div>
                        </div>

                    </div>
                </div>


        @endif
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
</div>

@push('script-page')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

    @if (Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'Director')
    <script type="text/javascript">
        $(document).ready(function() {
            get_data();
        });

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
                    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                        headerToolbar: {
                            left: 'prev', // Only navigation arrows
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
                        editable: true,
                        dayMaxEvents: true,
                        handleWindowResize: true,
                        height: '360px',
                        showNonCurrentDates: false,
                        events: data,
                    });
                    calendar.render();
                }
            });
        }
    </script>

    @else
        <script>
            $(document).ready(function() {
                get_data();
            });

            function get_data() {
                var calender_type = $('#calender_type :selected').val();

                $('#event_calendar').removeClass('local_calender');
                $('#event_calendar').removeClass('google_calender');
                if (calender_type == undefined) {
                    calender_type = 'local_calender';
                }
                $('#event_calendar').addClass(calender_type);

                $.ajax({
                    url: $("#path_admin").val() + "/event/get_event_data",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'calender_type': calender_type
                    },
                    success: function(data) {
                        var etitle;
                        var etype;
                        var etypeclass;
                        var calendar = new FullCalendar.Calendar(document.getElementById('event_calendar'), {
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            buttonText: {
                                timeGridDay: "{{ __('Day') }}",
                                timeGridWeek: "{{ __('Week') }}",
                                // dayGridMonth: "{{ __('Month') }}"
                            },
                            // slotLabelFormat: {
                            //     hour: '2-digit',
                            //     minute: '2-digit',
                            //     hour12: false,
                            // },
                            themeSystem: 'tailwind',
                            slotDuration: '00:10:00',
                            allDaySlot: true,
                            navLinks: true,
                            droppable: true,
                            selectable: true,
                            selectMirror: true,
                            editable: true,
                            dayMaxEvents: true,
                            handleWindowResize: true,
                            events: data,
                            height: '400px',
                            // timeFormat: 'H(:mm)',

                        });

                        calendar.render();
                    }
                });
            };
        </script>
    @endif

    @if (Auth::user()->type == 'company' || Auth::user()->type == 'hr' || Auth::user()->type == 'Director')
        <script>
            (function() {
                var totalEmployees = {{ $totalEmployees }};
                var presentEmployees = {{ count($presentEmployeesWithClockIn) }};
                var attendancePercentage = {{ round($attendancePercentage, 2) }};
                
                var options = {
                    series: [attendancePercentage],
                    chart: {
                        height: 380,
                        type: 'radialBar',
                        offsetY: -20,
                        sparkline: {
                            enabled: true
                        }
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -90,
                            endAngle: 90,
                            track: {
                                background: "#eef5ff",
                                strokeWidth: '98%',
                                margin: 5,
                            
                            },
                            dataLabels: {
                                name: {
                                    show: true
                                },
                                value: {
                                    offsetY: -50,
                                    fontSize: '20px'
                                }
                            }
                        }
                    },
                    grid: {
                        padding: {
                            top: -10
                        }
                    },
                    colors: ["#68A288"],
                    labels: [''],
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(val) {
                                return `Out of ${totalEmployees} employees, ${presentEmployees} are present.`;
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#attendance-chart"), options);
                chart.render();
            })();
        </script>

        <style>
            .apexcharts-tooltip {
                background: #000 !important;
                color: #fff !important;
                border-radius: 8px;
                font-size: 14px;
            }
        </style>
    @endif

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
                <form action="{{ route('todolist.store') }}" method="POST" id="addScheduleForm">
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
@endpush

@push('script-page')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        (function() {
            var options = {
                chart: {
                    height: 265,
                    type: 'bar',
                    toolbar: {
                        show: false,
                    },
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 4,
                    curve: 'smooth'
                },
                series: {!! json_encode($chartData['data']) !!},
                xaxis: {
                    categories: {!! json_encode($chartData['labels']) !!},
                },
                colors: ['#b4d1c4', '#68a288'],
                fill: {
                    type: 'solid',
                },
                grid: {
                    strokeDashArray: 4,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                },
                markers: {
                    size: 4,
                    colors: ['#000', '#FF3A6E'],
                    opacity: 2.5,
                    strokeWidth: 4,
                    hover: {
                        size: 8,
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#income-expense-chart"), options);
            chart.render();
        })();
    </script>

    <script>
        $(document).ready(function() {

            // Initialize custom date picker with today's date
            const today = new Date().toISOString().split('T')[0];
            const $customPicker = $('#customDatePicker');
            $customPicker.val(today);

            // Trigger the date picker when the input is clicked
            $customPicker.on('click', function() {
                if (this.showPicker) {
                    try {
                        this.showPicker();
                    } catch (e) {
                        $(this).focus();
                    }
                } else {
                    $(this).focus();
                }
            });

            // Handle date filter dropdown selection
            $('#dateFilterButton').closest('.btn-group').find('.dropdown-item').on('click', function(e) {
                e.preventDefault();
                const filterType = $(this).data('value');
                
                if (!filterType) return;
                
                const filterText = $(this).text();
                
                if (filterType === 'custom') {
                    $('#customDatePickerWrapper').show();
                    $('#dashboardHeader').addClass('compact');
                    $customPicker.focus();
                    const selectedDate = $customPicker.val();
                    if (selectedDate) {
                        const dateObj = new Date(selectedDate);
                        const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        $('#dateFilterButton').text(formattedDate);
                        loadDashboardData('custom', selectedDate);
                        if ($customPicker[0].showPicker) {
                            try { $customPicker[0].showPicker(); } catch(e) {}
                        }
                    } else {
                        $('#dateFilterButton').text(filterText);
                    }
                } else if (filterType === 'today' || filterType === 'yesterday') {
                    $('#dateFilterButton').text(filterText);
                    $('#customDatePickerWrapper').hide();
                    $('#dashboardHeader').removeClass('compact');
                    loadDashboardData(filterType);
                }
            });
            
            // Handle custom date selection
            $('#customDatePicker').on('change', function() {
                const selectedDate = $(this).val();
                if (selectedDate) {
                    const dateObj = new Date(selectedDate);
                    const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    $('#dateFilterButton').text(formattedDate);
                    loadDashboardData('custom', selectedDate);
                }
            });

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
            if (type === 'birthday') {
                emoji     = '🎂';
                greeting  = name ? 'Happy Birthday, ' + name + '!' : title;
                bgGrad    = 'linear-gradient(135deg, #e47477ff 0%, #ff9ee1ff 99%, #f8bee6ff 100%)';
                badgeText = '🎉 Birthday';
                badgeBg   = 'rgba(255,255,255,0.3)';
            } else if (type === 'anniversary') {
                emoji     = '🎉';
                greeting  = name ? 'Work Anniversary, ' + name + '!' : title;
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
                if ((type === 'birthday' || type === 'anniversary') && dateStr === todayStr && id) {
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

            function loadDashboardData(filterType, customDate = null) {
                let url = '{{ route("dashboard.filter") }}';
                let data = {
                    _token: '{{ csrf_token() }}',
                    filter_type: filterType
                };
                
                if (filterType === 'custom' && customDate) {
                    data.custom_date = customDate;
                }
                
                $('.card-body').addClass('loading');
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $('#todayEnquiryCount').text(response.todayEnquiryCount);
                            if (typeof response.totalLeaves !== 'undefined') {
                                $('#totalLeavesCount').text(response.totalLeaves);
                            }
                            $('#attendanceCount').text(response.presentEmployeesWithClockIn.length);
                            $('#notClockInCount').text(response.notClockIns.length);
                            updateTable('#attendanceTable tbody', response.presentEmployeesWithClockIn, 'attendance');
                            updateTable('#notClockInTable tbody', response.notClockIns, 'notClockIn');
                            
                            // Update Schedules HTML
                            if (response.schedulesHtml !== undefined) {
                                $('#schedulesContainer').html(response.schedulesHtml);
                            }
                            
                            // Update Titles
                            let scheduleTitle = "Today's Schedules";
                            let attendanceTitle = "Today's Attendance";
                            let notClockInTitle = "Yet To Arrive";
                            
                            if (filterType === 'yesterday') {
                                scheduleTitle = 'Yesterday Schedules';
                                attendanceTitle = 'Yesterday Attendance';
                                notClockInTitle = 'Yet To Arrive Yesterday';
                            } else if (filterType === 'custom' && customDate) {
                                scheduleTitle = customDate + ' Schedules';
                                attendanceTitle = customDate + ' Attendance';
                                notClockInTitle = 'Yet To Arrive ' + customDate;
                            }
                            
                            $('#scheduleFilterTitle').text(scheduleTitle);
                            $('#attendanceFilterTitle').text(attendanceTitle);
                            $('#notClockInFilterTitle').text(notClockInTitle);
                        }
                    },
                    error: function(xhr) {
                        console.error('Dashboard filter error:', xhr);
                    },
                    complete: function() {
                        $('.card-body').removeClass('loading');
                    }
                });
            }

            function updateTable(tableSelector, data, tableType) {
                const $table = $(tableSelector);
                $table.empty();
                
                if (data.length === 0) {
                    let noDataText = 'No data available';
                    let colspan = 3;
                    if (tableType === 'attendance') {
                        noDataText = 'No attendance records found.';
                        colspan = 4;
                    } else if (tableType === 'notClockIn') {
                        noDataText = 'All employees are present';
                        colspan = 2;
                    }
                    $table.append('<tr><td colspan="' + colspan + '">' + noDataText + '</td></tr>');
                    return;
                }
                
                data.forEach(function(item) {
                    let row = '';
                    if (tableType === 'attendance') {
                        row = `<tr>
                            <td>${item.employee ? item.employee.name : 'N/A'}</td>
                            <td>${item.clock_in || '--:--'}</td>
                            <td>${item.clock_out || '--:--'}</td>
                            <td>${item.total_working_hours || '--:--'}</td>
                        </tr>`;
                    } else if (tableType === 'notClockIn') {
                        row = `<tr>
                            <td>${item.name || 'N/A'}</td>
                            <td style="color: red;">Absent</td>
                        </tr>`;
                    }
                    $table.append(row);
                });
            }

            function formatLocationWithTimestamp(location, lat, lng, capturedAt, punchTime) {
                if (!location || location === 'Location not captured yet' || location === 'Location not available') {
                    return '<div>Location pending...</div><small style="font-size: 10px; color: #6c757d;">Location being captured in background</small>';
                }
                
                let locationHtml = '<div>';
                if (lat && lng) {
                    locationHtml += '<a href="https://www.google.com/maps?q='+lat+','+lng+'" target="_blank" title="View on map">';
                    locationHtml += (location.length > 50 ? location.substring(0, 50) + '...' : location);
                    locationHtml += '</a>';
                } else {
                    locationHtml += location;
                }
                locationHtml += '</div>';
                
                if (capturedAt && punchTime) {
                    try {
                        const captured = new Date(capturedAt);
                        const punch = new Date('2000-01-01 ' + punchTime);
                        const delay = Math.floor((captured - punch) / 1000);
                        
                        const capturedTime = captured.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                        locationHtml += '<small style="font-size: 10px; color: #6c757d;">Captured: ' + capturedTime;
                        
                        if (delay > 30) {
                            const delayText = delay > 60 ? Math.round(delay / 60) + ' min later' : delay + ' sec later';
                            locationHtml += ' <span style="color: #ff9800;">(' + delayText + ')</span>';
                        }
                        locationHtml += '</small>';
                    } catch (e) {}
                }
                return locationHtml;
            }

            function formatLocation(location, lat, lng) {
                if (!location || location === 'Location not available' || location === 'Location not captured yet') {
                    if (lat && lng) {
                        return '<a href="https://www.google.com/maps?q='+lat+','+lng+'" target="_blank">View on Map</a>';
                    }
                    return 'Location pending...';
                }
                const truncated = location.length > 50 ? location.substring(0, 50) + '...' : location;
                return '<a href="https://www.google.com/maps?q='+lat+','+lng+'" target="_blank">'+truncated+'</a>';
            }

            function formatDateRange(start, end) {
                try {
                    const startDate = new Date(start);
                    const endDate = new Date(end);
                    return `${startDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })} - ${endDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })}`;
                } catch (e) {
                    console.error('Error formatting date:', e);
                    return '--';
                }
            }
        });
    </script>
@endpush
