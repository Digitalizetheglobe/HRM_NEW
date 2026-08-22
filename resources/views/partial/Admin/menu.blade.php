    @php

    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::GetLogo();
    $users = \Auth::user();
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
    $currantLang = $users->currentLanguage();
    $emailTemplate = App\Models\EmailTemplate::getemailTemplate();
    $lang = Auth::user()->lang;
    $userType = auth()->user()->type;

@endphp

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
<nav class="dash-sidebar light-sidebar transprent-bg" 
    style="background: #111135; color: white;" >
    
    @else
@endif

{{-- <nav class="dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}"> --}}

<div class="navbar-wrapper">
    <div class="m-header main-logo mt-1">
        <a href="{{ route('dashboard') }}" class="b-brand">
            <!-- ========   change your logo hear   ============ -->
            <img src="{{ $logo . (isset($company_logo) && !empty($company_logo) ? $company_logo . '?' . time() : 'logo-dark.png' . '?' . time()) }}"
                alt="{{ config('app.name', 'HRMGo') }}" class="logo logo-lg" style="max-height: 200px; width: auto;">
        </a>
    </div>
    <div class="navbar-content">
        @php
            $isApprovedEmployee = true;
            if (\Auth::check() && \Auth::user()->type === 'employee') {
                $employeeCheck = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
                if ($employeeCheck && $employeeCheck->approval_status !== 'approved') {
                    $isApprovedEmployee = false;
                }
            }
        @endphp

        <ul class="dash-navbar">
        @if($isApprovedEmployee)

            <!-- dashboard-->
            @if (\Auth::user()->type != 'company')
            <li class="dash-item" style="color: white;" >
                <a href="{{ route('dashboard') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                    <span class="dash-micon shadow-none" style="background: none;"><i class="ti ti-home text-white text-2xl"></i></span>
                    <span class="dash-mtext" style="color: white;">{{ __('Dashboard') }}</span>
                </a>
            </li>

            @endif
            @if (\Auth::user()->type == 'company')
                <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'null' ? 'active dash-trigger' : '' }}">
                    <a href="{{ route('dashboard') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                        <span class="dash-micon  shadow-none" style="background: none;">
                            <i class="ti ti-home text-white text-2xl"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Dashboard') }}</span>

                    </a>
                </li>
            @endif

            <!--dashboard-->

            <!-- user-->
            <!-- @if (\Auth::user()->type == 'super admin')
                <li class="dash-item text-white">
                    <a href="{{ route('user.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                        <span class="dash-micon shadow-none" style="background: none;"><i class="ti ti-user text-white text-2xl"></i></span>
                        <span class="dash-mtext">{{ __('Companies') }}</span>
                    </a>
                </li>
            @else
                @if (Gate::check('Manage User') ||
                        Gate::check('Manage Role') ||
                        Gate::check('Manage Employee Profile') ||
                        Gate::check('Manage Employee Last Login'))
                    <li class="dash-item dash-hasmenu text-white 
                        {{ Request::segment(1) == 'user' || Request::segment(1) == 'roles' || Request::segment(1) == 'lastlogin'
                            ? 'active dash-trigger'
                            : '' }}">
                            <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                                <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                                    <i class="ti ti-users text-white text-2xl"></i>
                                </span>
                                <span class="dash-mtext">{{ __('Staff') }}</span>
                                <span class="dash-arrow text-white text-base shadow-none" style="background: none;">
                                    <i data-feather="chevron-right"></i>
                                </span>
                            </a>

                        <ul class="dash-submenu 
                            {{ Request::route()->getName() == 'user.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'user.edit' || Request::route()->getName() == 'lastlogin'
                                ? 'active'
                                : '' }}">
                            @can('Manage User')
                                <li class="dash-item {{ Request::segment(1) == 'lastlogin' ? 'active' : '' }}">
                                    <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" 
                                    href="{{ route('user.index') }}">
                                    {{ __('User') }}
                                    </a>
                                </li>
                            @endcan

                            @can('Manage Employee Profile')
                                <li class="dash-item">
                                    <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" 
                                    href="{{ route('employee.profile') }}">
                                    {{ __('Employee Profile') }}
                                    </a>
                                </li>
                            @endcan
                            {{-- Uncomment if needed
                            @can('Manage Employee Last Login')
                                <li class="dash-item">
                                    <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" 
                                    href="{{ route('lastlogin') }}">
                                    {{ __('Last Login') }}
                                    </a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endif
            @endif -->

            <!-- user-->

            <!-- employee-->
            @if (Gate::check('Manage Employee'))
                @if (\Auth::user()->type == 'employee')
                    @php
                        $employee = App\Models\Employee::where('user_id', \Auth::user()->id)->first();
                    @endphp
                    <li class="dash-item {{ Request::segment(1) == 'employee' ? 'active' : '' }}">
                        <a href="{{ route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id)) }}"
                            class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                                    <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                                        <i class="ti ti-users text-white text-2xl"></i>
                                    </span>
                            <span class="dash-mtext">{{ __('Employee') }}</span>
                        </a>

                    </li>
                @else
                    <li class="dash-item {{ Request::segment(1) == 'employee' ? 'active' : '' }}">
                        <a href="{{ route('employee.index') }}"
                            class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                            <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                                        <i class="ti ti-users text-white text-2xl"></i>
                            </span>
                            <span class="dash-mtext">{{ __('Employee') }}</span>
                        </a>
                    </li>
                @endif
            @endif


            <!-- employee-->


            <!-- attendance -->
            @if (Gate::check('Manage Attendance') || Gate::check('Create Attendance') || Gate::check('Manage Biometric Attendance'))
                <li class="dash-item dash-hasmenu {{ Request::is('attendance*') || Request::is('biometric-attendance*') ? 'dash-trigger active' : '' }}">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                        <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-clock text-white text-2xl"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Attendance') }}</span>
                        <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                    </a>

                    <ul class="dash-submenu">

                       <li class="dash-item {{ Request::segment(1) == 'attendance-calendar' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('attendance.calendar') }}">{{ __('Attendance Calendar') }}</a>
                        </li>

                        @can('Manage Attendance')
                            <li class="dash-item {{ Request::route()->getName() == 'attendanceemployee.index' ? ' active' : '' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('attendanceemployee.index') }}">{{ __('Marked Attendance') }}</a>
                            </li>
                        @endcan

                        @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'employee')
                            <li class="dash-item {{ Request::route()->getName() == 'attendance.report' ? ' active' : '' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('attendance.report') }}">{{ __('Employee Report') }}</a>
                            </li>
                        @endif

                        @can('Create Attendance')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('attendanceemployee.bulkattendance') }}">{{ __('Bulk Attendance') }}</a>
                            </li>
                        @endcan

                        @can('Manage Biometric Attendance')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('biometric-attendance.index') }}">{{ __('Biometric Attendance') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            <!-- Attendance -->

             <!-- Timesheet -->
                <li class="dash-item">
                    <a href="{{ route('timesheet.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                        <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="fas fa-question-circle text-white text-2xl"></i>
                        </span>
                        <span class="dash-mtext">{{ __('TimeSheet') }}</span>
                    </a>
                </li>
            <!-- Timesheet -->


            <!-- Leave -->
            
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'calender' && Request::segment(2) == 'leave' ? 'dash-trigger active' : '' }}">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-clock text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Leave') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">
                        
                        @can('Manage Leave')
                                
                            <li class="dash-item {{ Request::segment(1) == 'calender' ? ' active' : '' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('leave.index') }}">{{ __('Manage Leave') }}</a>
                            </li>

                            @if(\Auth::user()->type == 'company')
                                <li class="dash-item {{ Request::segment(1) == 'leave-details' ? ' active' : '' }}">
                                    <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('leave.details') }}">{{ __('Leave Details') }}</a>
                                </li>
                            @endif

                        @endcan
                        
                    </ul>
                </li>
                
            <!-- Leave -->

            <!-- Project -->
            <li class="dash-item dash-hasmenu {{ in_array(Request::segment(1), ['projects', 'my-projects', 'my-daily-updates']) ? 'dash-trigger active' : '' }}">
                <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                    <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                    </span>
                    <span class="dash-mtext">{{ __('Project') }}</span>
                    <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                </a>
                <ul class="dash-submenu">
                    @php
                        $isDesigner = false;
                        $isTester = false;
                        if(\Auth::user()->type == 'employee') {
                            $employee = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
                            if($employee) {
                                $dept = \App\Models\Department::find($employee->department_id);
                                if($dept) {
                                    if (in_array($dept->name, ['UI-UX Designer', 'UI/UX Designer'])) {
                                        $isDesigner = true;
                                    }
                                    if (in_array($dept->name, ['Tester', 'Quality Assurance', 'QA'])) {
                                        $isTester = true;
                                    }
                                }
                            }
                        }
                        $hasProjectAccess = \Auth::user()->type == 'company' || \Auth::user()->can('Create Employee') || $isTester;
                    @endphp
                    @if($hasProjectAccess)
                        <li class="dash-item {{ Request::segment(1) == 'projects' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('projects.index') }}">{{ __('Manage Projects') }}</a>
                        </li>
                        <li class="dash-item {{ Request::segment(1) == 'report' && Request::segment(2) == 'projects' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('report.projects') }}">{{ __('Project Reports') }}</a>
                        </li>
                        @if(\Auth::user()->type == 'company' || $isTester)
                        <li class="dash-item {{ Request::segment(1) == 'report' && Request::segment(2) == 'employee-daily' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('report.employee_daily') }}">{{ __('Employee Daily Reports') }}</a>
                        </li>
                        @endif
                    @endif
                    @if($hasProjectAccess || $isDesigner)
                        <li class="dash-item {{ Request::segment(1) == 'designs' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('designs.index') }}">{{ __('Design Reviews') }}</a>
                        </li>
                    @endif
                    
                    @if(\Auth::user()->type == 'company' || (isset($isTester) && $isTester))
                        <li class="dash-item {{ Request::segment(1) == 'project-links' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('project-links.index') }}">{{ __('Project Links') }}</a>
                        </li>
                    @endif
                    
                    @if(\Auth::user()->type == 'employee')
                        <li class="dash-item {{ Request::segment(1) == 'my-projects' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('projects.my_projects') }}">{{ __('My Projects') }}</a>
                        </li>
                        <li class="dash-item {{ Request::segment(1) == 'my-daily-updates' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('projects.my_updates') }}">{{ __('My Daily Work') }}</a>
                        </li>
                    @endif
                </ul>
            </li>
            <!-- Project -->


            <!-- To-Do -->
            <li class="dash-item">
                <a href="{{ route('todo.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                <i class="fas fa-tasks text-white text-2xl"></i></span>
                    <span class="dash-mtext">{{ __('To-Do List') }}</span>
                </a>
            </li>

            <!-- recruitment-->
            @if (Gate::check('Manage Job') ||
                    Gate::check('Manage Job Application') ||
                    Gate::check('Manage Job OnBoard') ||
                    Gate::check('Manage Custom Question') ||
                    Gate::check('Manage Interview Schedule') ||
                    Gate::check('Manage Career'))
                <li
                    class="dash-item dash-hasmenu  {{ Request::segment(1) == 'job' || Request::segment(1) == 'job-application' ? 'dash-trigger active' : '' }} ">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-license text-white text-2xl "></i></span><span
                            class="dash-mtext">{{ __('Recruitment') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">
                        @can('Manage Job')
                            <li
                                class="dash-item {{ Request::route()->getName() == 'job.index' ? 'active' : 'dash-hasmenu' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('job.index') }}">{{ __('Jobs') }}</a>
                            </li>
                        @endcan
                        @can('Manage Job')
                            <li
                                class="dash-item {{ Request::route()->getName() == 'job.create' ? 'active' : 'dash-hasmenu' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('job.create') }}">{{ __('Job Create ') }}</a>
                            </li>
                        @endcan
                        @can('Manage Job Application')
                            <li class="dash-item {{ request()->is('job-application*') ? 'active' : '' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('job-application.index') }}">{{ __('Job Application') }}</a>
                            </li>
                        @endcan
                        @can('Manage Job Application')

                            <li class="dash-item {{ request()->is('candidates-job-applications') ? 'active' : '' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('job.application.candidate') }}">{{ __('Job Candidate') }}</a>
                            </li>
                        @endcan

                        @can('Manage Job OnBoard')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('job.on.board') }}">{{ __('Job On-Boarding') }}</a>
                            </li>
                        @endcan

                        @can('Manage Custom Question')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('custom-question.index') }}">{{ __('Custom Question') }}</a>
                            </li>
                        @endcan

                        @can('Manage Interview Schedule')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('interview-schedule.index') }}">{{ __('Interview Schedule') }}</a>
                            </li>
                        @endcan

                        @can('Manage Career')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('career', [\Auth::user()->creatorId(), $lang]) }}"
                                    target="_blank">{{ __('Career') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            <!-- recruitment-->

            <!-- payroll-->
            @if (Gate::check('Manage Set Salary') || Gate::check('Manage Pay Slip'))
                <li
                    class="dash-item dash-hasmenu  {{ Request::segment(1) == 'setsalary' ? 'dash-trigger active' : '' }}">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                        <span class="dash-micon text-[50px] shadow-none" style="background: none;">
                            <i class="ti ti-receipt text-white text-2xl">
                            </i>
                        </span>
                        <span class="dash-mtext">
                            {{ __('Payroll') }}
                        </span>
                        <span class="dash-arrow"><i data-feather="chevron-right">
                            </i>
                        </span>
                    </a>
                    <ul class="dash-submenu ">
                        <li class="dash-item {{ Request::segment(1) == 'setsalary' ? 'active' : '-' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('setsalary.index') }}">{{ __('Set Salary') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('payslip.index') }}">{{ __('Payslip') }}</a>
                        </li>

                    </ul>
                </li>
            @endif
            <!-- payroll-->

            @if (\Auth::user()->type == 'employee')
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'setsalary' ? 'dash-trigger active' : '' }}">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-receipt text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Payroll') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">
                        <li class="dash-item {{ Request::segment(1) == 'setsalary' ? 'active' : '-' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('setsalary.show', \Illuminate\Support\Facades\Crypt::encrypt(\Auth::user()->id)) }}">{{ __('Salary') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('payslip.index') }}">{{ __('Payslip') }}</a>
                        </li>
                    </ul>
                </li>
            @endif
            <!--employee payroll -->


            @if (\Auth::user()->type != 'super admin')
                <li class="dash-item {{ Request::segment(1) == 'chats' ? 'active' : '' }}">
                    <a href="{{ url('chats') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-messages text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Messenger') }}</span></a>
                </li>
            @endif
            <!--  -->


             <!-- Notice -->
            <li class="dash-item">
                <a href="{{ route('notices.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                <i class="fas fa-bell text-white text-2xl"></i></span>
                    <span class="dash-mtext">{{ __('Notice') }}</span>
                </a>
            </li>


            <!-- offboard-->
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'setsalary' ? 'dash-trigger active' : '' }}">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="fas fa-user-slash text-white text-2xl"></i>
                          </span><span
                            class="dash-mtext">{{ __('Offboard ') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('resignation.index') }}">{{ __('Resignation ') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('termination.index') }}">{{ __('Exit Formalities') }}</a>
                        </li>
                    </ul>
                </li>
            <!-- offboard-->

            <li class="dash-item {{ Request::segment(1) == 'holiday' ? ' active' : '' }}">
                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2" href="{{ route('holiday.index') }}">
                    <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                        <i class="ti ti-calendar-event text-white text-2xl"></i>
                    </span>
                    <span class="dash-mtext">{{ __('Holidays') }}</span>
                </a>
            </li>

            <!-- ticket-->
            @can('Manage Ticket')
                <li class="dash-item {{ Request::segment(1) == 'ticket' ? 'active' : '' }}">
                    <a href="{{ route('ticket.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-ticket  text-white text-2xl"></i></span><span class="dash-mtext">{{ __('Ticket') }}</span></a>
                </li>
            @endcan
            <!-- ticket -->


            <!-- Company Policy for Employees -->
            @if (\Auth::user()->type == 'employee')
                <li class="dash-item {{ Request::segment(1) == 'company-policy' ? 'active' : '' }}">
                    <a href="{{ route('company-policy.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                        <span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-pray text-white text-2xl"></i>
                        </span>
                        <span class="dash-mtext">{{ __('Company Policy') }}</span>
                    </a>
                </li>
            @endif
            <!-- Company Policy for Employees -->

            <!-- Company Policy -->
            @if (Gate::check('Manage Company Policy'))
                <li class="dash-item">
                    <a href="{{ route('company-policy.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-pray text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Company Policy') }}</span></a>
                </li>
            @endcan
            <!-- Company Policy -->

































            <!-- performance-->
            <!-- @if (Gate::check('Manage Indicator') || Gate::check('Manage Appraisal') || Gate::check('Manage Goal Tracking'))
                <li class="dash-item dash-hasmenu">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-3d-cube-sphere text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Performance') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">
                        @can('Manage Indicator')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('indicator.index') }}">{{ __('Indicator') }}</a>
                            </li>
                        @endcan

                        @can('Manage Appraisal')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('appraisal.index') }}">{{ __('Appraisal') }}</a>
                            </li>
                        @endcan

                        @can('Manage Goal Tracking')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('goaltracking.index') }}">{{ __('Goal Tracking') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif -->
            <!--performance-->

            <!--fianance-->
            <!-- @if (Gate::check('Manage Account List') ||
                    Gate::check('Manage Payee') ||
                    Gate::check('Manage Payer') ||
                    Gate::check('Manage Deposit') ||
                    Gate::check('Manage Expense') ||
                    Gate::check('Manage Transfer Balance'))
                <li class="dash-item dash-hasmenu">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-wallet text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Finance') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">
                        @can('Manage Account List')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('accountlist.index') }}">{{ __('Account List') }}</a>
                            </li>
                        @endcan
                        @can('View Balance Account List')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('accountbalance') }}">{{ __('Account Balance') }}</a>
                            </li>
                        @endcan
                        @can('Manage Payee')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('payees.index') }}">{{ __('Payees') }}</a>
                            </li>
                        @endcan

                        @can('Manage Payer')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('payer.index') }}">{{ __('Payers') }}</a>
                            </li>
                        @endcan

                        @can('Manage Deposit')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('deposit.index') }}">{{ __('Deposit') }}</a>
                            </li>
                        @endcan

                        @can('Manage Expense')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('expense.index') }}">{{ __('Expense') }}</a>
                            </li>
                        @endcan

                        @can('Manage Transfer Balance')
                            <li class="dash-item">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                    href="{{ route('transferbalance.index') }}">{{ __('Transfer Balance') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif -->
            <!-- fianance-->

            <!--trainning-->
            <!-- @if (Gate::check('Manage Trainer') || Gate::check('Manage Training'))
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'training' ? 'dash-trigger active' : '' }}">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-school text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Training') }}</span><span class="dash-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="dash-submenu">
                        @can('Manage Training')
                            <li class="dash-item {{ Request::segment(1) == 'training' ? ' active' : '' }}">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('training.index') }}">{{ __('Training List') }}</a>
                            </li>
                        @endcan

                        @can('Manage Trainer')
                            <li class="dash-item ">
                                <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('trainer.index') }}">{{ __('Trainer') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif -->

            <!-- tranning-->


            <!-- HR-->
            <!-- @if (Gate::check('Manage Awards') ||
                    Gate::check('Manage Transfer') ||
                    Gate::check('Manage Resignation') ||
                    Gate::check('Manage Travels') ||
                    Gate::check('Manage Promotion') ||
                    Gate::check('Manage Complaint') ||
                    Gate::check('Manage Warning') ||
                    Gate::check('Manage Termination') ||
                    Gate::check('Manage Announcement') ||
                    Gate::check('Manage Holiday'))
                <li
                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'holiday' ? 'dash-trigger active' : '' }}">
                    <a href="#!" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-user-plus text-white text-2xl"></i>
                        </span>
                        <span class="dash-mtext">{{ __('HR Admin Setup') }}</span>
                        <span class="dash-arrow">
                            <i data-feather="chevron-right"></i>
                        </span>
                    </a>
                    <ul class="dash-submenu">
                        <li class="dash-item {{ Request::segment(1) == 'award' ? 'active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('award.index') }}">{{ __('Award') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('transfer.index') }}">{{ __('Transfer') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('resignation.index') }}">{{ __('Resignation') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('travel.index') }}">{{ __('Trip') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('promotion.index') }}">{{ __('Promotion') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('complaint.index') }}">{{ __('Complaints') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('warning.index') }}">{{ __('Warning') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('termination.index') }}">{{ __('Termination') }}</a>
                        </li>
                        <li class="dash-item">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base"
                                href="{{ route('announcement.index') }}">{{ __('Announcement') }}</a>
                        </li>
                        <li class="dash-item {{ Request::segment(1) == 'holiday' ? ' active' : '' }}">
                            <a class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base" href="{{ route('holiday.index') }}">{{ __('Holidays') }}</a>
                        </li>
                    </ul>
                </li>
            @endif -->
            <!-- HR-->







            
            <!--contract-->
            <!-- @can('Manage Contract')
                <li
                    class="dash-item {{ Request::route()->getName() == 'contract.index' || Request::route()->getName() == 'contract.show' ? 'active' : '' }}">
                    <a href="{{ route('contract.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                        <i class="ti ti-device-floppy text-white text-2xl">
                        </i></span><span class="dash-mtext">{{ __('Contracts') }}</span></a>
                </li>
            @endcan -->

            <!--end-->





            <!-- Event-->
            <!-- @can('Manage Event')
                <li class="dash-item">
                    <a href="{{ route('event.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-basen flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-calendar-event text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Event') }}</span>
                    </a>
                </li>
            @endcan -->
            <!--meeting-->
            <!-- @can('Manage Meeting')
                <li
                    class="dash-item {{ Request::segment(1) == 'meeting' || Request::segment(2) == 'meeting' ? 'active' : '' }}">
                    <a href="{{ route('meeting.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-calendar-time text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Meeting') }}</span></a>
                </li>
            @endcan
 -->

            <!-- Zoom meeting-->
            <!-- @can('Manage Zoom meeting')
                @if (\Auth::user()->type != 'super admin')
                    <li class="dash-item {{ Request::segment(1) == 'zoommeeting' ? 'active' : '' }}">
                        <a href="{{ route('zoom-meeting.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                    class="ti ti-video text-white text-2xl"></i></span><span
                                class="dash-mtext">{{ __('Zoom Meeting') }}</span></a>
                    </li>
                @endif
            @endcan -->

            <!-- assets-->
            <!-- @if (Gate::check('Manage Assets'))
                <li class="dash-item">
                    <a href="{{ route('account-assets.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-medical-cross text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Assets') }}</span></a>
                </li>
            @endcan
 -->

            <!-- document-->
            <!-- @if (Gate::check('Manage Document'))
                <li class="dash-item">
                    <a href="{{ route('document-upload.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-baseflex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-file text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Document') }}</span></a>
                </li>
            @endcan -->

            <!--company policy-->



            <!-- @if (Gate::check('Manage Company Policy'))
                <li class="dash-item">
                    <a href="{{ route('company-policy.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-pray text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Company Policy') }}</span></a>
                </li>
            @endcan -->
            <!--chats-->


            <!-- @if (\Auth::user()->type == 'company')
                <li
                    class="dash-item {{ Request::route()->getName() == 'notification-templates.update' || Request::segment(1) == 'notification-templates' ? 'active' : '' }}">
                    <a href="{{ route('notification-templates.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text- flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-bell text-white text-[30]"></i></span><span
                            class="dash-mtext">{{ __('Notification Template') }}</span></a>
                </li>
            @endif -->

            <!-- @if (\Auth::user()->type == 'super admin')
                @if (Gate::check('Manage Plan'))
                    <li class="dash-item ">
                        <a href="{{ route('plans.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                                <i class=" ti ti-trophy text-white text-2xl"></i></span><span
                                class="dash-mtext">{{ __('Plan') }}</span></a>

                    </li>
                @endif
            @endif
            @if (\Auth::user()->type == 'super admin')
                <li class="dash-item ">
                    <a href="{{ route('plan_request.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                            <i class="ti ti-arrow-down-right-circle text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Plan Request') }}</span></a>

                </li>
            @endif
            @if (\Auth::user()->type == 'super admin')
                <li class="dash-item dash-hasmenu  {{ Request::segment(1) == '' ? 'active' : '' }}">
                    <a href="{{ route('referral-program.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                        <i class="ti ti-discount-2 text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Referral Program') }}</span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->type == 'super admin')
                @if (Gate::check('manage coupon'))
                    <li
                        class="dash-item dash-hasmenu {{ Request::segment(1) == 'coupons' ? 'active' : '' }}">
                        <a href="{{ route('coupons.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                                <i class="ti ti-gift text-white text-2xl"></i></span><span
                                class="dash-mtext">{{ __('Coupon') }}</span></a>

                    </li>
                @endif
            @endif
            @if (\Auth::user()->type == 'super admin')
                {{-- @if (Gate::check('Manage Order')) --}}
                <li class="dash-item ">
                    <a href="{{ route('order.index') }}"
                        class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2 {{ request()->is('orders*') ? 'active' : '' }}"><span
                            class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i class="ti ti-shopping-cart text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Order') }}</span></a>

                </li>
                {{-- @endif --}}
            @endif

            @if (\Auth::user()->type == 'super admin')
                <li
                    class="dash-item {{ Request::route()->getName() == 'email_template.show' || Request::segment(1) == 'email_template_lang' || Request::route()->getName() == 'manageemail.lang' ? 'active' : '' }}">
                    <a href="{{ route('manage.email.language', [$emailTemplate->id, \Auth::user()->lang]) }}"
                        class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i
                                class="ti ti-template text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Email Templates') }}</span></a>
                </li>
            @endif -->
            <!--report-->
  

            <!--constant-->
            @if (Gate::check('Manage Department') ||
                    Gate::check('Manage Designation') ||
                    Gate::check('Manage Document Type') ||
                    Gate::check('Manage Branch') ||
                    Gate::check('Manage Site') ||
                    Gate::check('Manage Award Type') ||
                    Gate::check('Manage Termination Types') ||
                    Gate::check('Manage Payslip Type') ||
                    Gate::check('Manage Allowance Option') ||
                    Gate::check('Manage Loan Options') ||
                    Gate::check('Manage Deduction Options') ||
                    Gate::check('Manage Expense Type') ||
                    Gate::check('Manage Income Type') ||
                    Gate::check('Manage Payment Type') ||
                    Gate::check('Manage Leave Type') ||
                    Gate::check('Manage Training Type') ||
                    Gate::check('Manage Job Category') ||
                    Gate::check('Manage Job Stage'))
                <li
                    class="dash-item dash-hasmenu {{ Request::route()->getName() == 'branch.index'||Request::route()->getName() == 'site.index'  ||Request::route()->getName() == 'department.index' ||Request::route()->getName() == 'designation.index' ||Request::route()->getName() == 'leavetype.index' ||Request::route()->getName() == 'document.index' ||Request::route()->getName() == 'paysliptype.index' ||Request::route()->getName() == 'allowanceoption.index' ||Request::route()->getName() == 'loanoption.index' ||Request::route()->getName() == 'deductionoption.index' ||Request::route()->getName() == 'goaltype.index' ||Request::route()->getName() == 'trainingtype.index' ||Request::route()->getName() == 'awardtype.index' ||Request::route()->getName() == 'terminationtype.index' ||Request::route()->getName() == 'job-category.index' ||Request::route()->getName() == 'job-stage.index' ||Request::route()->getName() == 'performanceType.index' ||Request::route()->getName() == 'competencies.index' ||Request::route()->getName() == 'expensetype.index' ||Request::route()->getName() == 'incometype.index' ||Request::route()->getName() == 'paymenttype.index' ||Request::route()->getName() == 'contract_type.index'? ' active': '' }}">
                    <a href="{{ route('branch.index') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2"><span class="dash-micon text-white text-2xl shadow-none" style="background: none;">
                               <i class="ti ti-table text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('HRM System Setup') }}</span></a>
                </li>

            @endif

            @if (\Auth::user()->type == 'super admin')
                @include('landingpage::menu.landingpage')
            @endif

            @if (Gate::check('Manage System Settings'))
                <li class="dash-item ">
                    <a href="{{ route('settings.index') }}" class="dash-link text-white hover:text-white text-base flex items-center space-x-2"><span
                            class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i class="ti ti-settings text-white text-2xl"></i></span><span
                            class="dash-mtext">{{ __('Settings') }}</span></a>
                </li>
            @endif
            <!--------------------- Start System Setup ----------------------------------->

            @if (\Auth::user()->type != 'super admin')

                @if (Gate::check('Manage Plan') || Gate::check('Manage Order') || Gate::check('Manage Company Settings'))
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ url('/settings') }}" class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">
                            <span class="dash-micon text-white text-2xl shadow-none" style="background: none;"><i class="ti ti-settings text-white text-2xl"></i></span><span
                                class="dash-mtext">{{ __('System Setup') }}</span><span
                                class="dash-arrow">
                                <!-- <i data-feather="chevron-right"></i></span> -->
                        </a>
                        <!-- <ul class="dash-submenu">
                            @if (Gate::check('Manage Company Settings'))
                                <li
                                    class="dash-item dash-hasmenu {{ Request::segment(1) == 'company-setting' ? ' active' : '' }}">
                                    <a href="{{ route('settings.index') }}"
                                        class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base flex items-center space-x-2">{{ __('System Settings') }}</a>
                                </li>
                            @endif
                            @if (Gate::check('Manage Plan'))
                                <li
                                    class="dash-item{{ Request::route()->getName() == 'plans.index' || Request::route()->getName() == 'stripe' ? ' active' : '' }}">
                                    <a href="{{ route('plans.index') }}"
                                        class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base">{{ __('Setup Subscription Plan') }}</a>
                                </li>
                            @endif
                            <li
                                class="dash-item{{ Request::route()->getName() == 'referral-program.company' ? ' active' : '' }}">
                                <a href="{{ route('referral-program.company') }}"
                                    class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base">{{ __('Referral Program') }}</a>
                            </li>
                            @if (\Auth::user()->type == 'super admin' || \Auth::user()->type == 'company')
                                <li
                                    class="dash-item {{ Request::segment(1) == 'order' ? 'active' : '' }}">
                                    <a href="{{ route('order.index') }}"
                                        class="dash-link text-white hover:text-white hover:bg-[#001a3b] text-base">{{ __('Order') }}</a>
                                </li>
                            @endif
                        </ul> -->
                    </li>
                @endif
            @endif

            <!--------------------- End System Setup ----------------------------------->
        @endif
        </ul>

    </div>
</div>
</nav>
