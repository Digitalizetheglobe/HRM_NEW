<?php

namespace App\Http\Controllers;

use App\Models\AccountList;
use App\Models\Announcement;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\Event;
use App\Models\LandingPageSection;
use App\Models\Meeting;
use App\Models\Job;
use App\Models\Order;
use App\Models\Payees;
use App\Models\Payer;
use App\Models\Plan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use App\Models\DailyQuote;  
use App\Models\Department; 
use App\Models\Site;
use App\Models\LeaveType;  
use App\Models\Project;  
use App\Models\ToDoList;  
use Carbon\Carbon;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Holiday;
use App\Models\Notice;
use App\Models\TimeSheet; // Make sure to import the TimeSheet model at the top
use App\Models\Leave;
use Illuminate\Http\Request;
use App\Models\Termination;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->type == 'employee') {
                $emp = Employee::with(['user', 'designation'])->where('user_id', '=', $user->id)->first();
                
                // Check if employee record exists
                if (!$emp) {
                    // Return a view with empty data if employee record doesn't exist
                    return view('dashboard.dashboard', [
                        'employeesOnWeekOffIds' => collect(),
                        'allEvents' => [],
                        'employeesNotWorkingToday' => collect(),
                        'notices' => collect(),
                        'arrEvents' => [],
                        'announcements' => collect(),
                        'employees' => collect(),
                        'meetings' => collect(),
                        'employeeAttendance' => null,
                        'officeTime' => ['startTime' => '', 'endTime' => ''],
                        'quote' => null,
                        'emp' => null,
                        'clockInTime' => null,
                        'todos' => collect(),
                        'attendanceData' => [],
                        'pendingLeaveCount' => 0,
                    ]);
                }
                
                $announcements = Announcement::orderBy('announcements.id', 'desc')
                    ->take(5)
                    ->leftJoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')
                    ->where('announcement_employees.employee_id', '=', $emp->id)
                    ->orWhere(function ($q) {
                        $q->where('announcements.department_id', 0)->where('announcements.employee_id', 0);
                    })
                    ->get();
                
                $employees = Employee::get();
                $meetings = Meeting::orderBy('meetings.id', 'desc')
                        ->leftJoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')
                        ->where('meeting_employees.employee_id', '=', $emp->id)
                        ->orWhere(function ($q) {
                            $q->where('meetings.department_id', 0)->where('meetings.employee_id');
                        })
                        ->take(5)
                        ->get();
                
                
                $events = Event::select('events.*', 'events.id as event_id', 'event_employees.*')
                    ->leftJoin('event_employees', 'events.id', '=', 'event_employees.event_id')
                    ->where('event_employees.employee_id', '=', $emp->id)
                    ->orWhere(function ($q) {
                        $q->where('events.department_id', 0)->where('events.employee_id', 0);
                    })
                    ->get();
                
                $arrEvents = [];
                
                foreach ($events as $event) {
                    $arr['id'] = $event['event_id'];
                    $arr['title'] = $event['title'];
                    $arr['start'] = $event['start_date'];
                    $arr['end'] = $event['end_date'];
                    $arr['className'] = $event['color'];
                    $arr['url'] = (!empty($event['event_id'])) ? route('eventsshow', $event['event_id']) : '0';
                    $arrEvents[] = $arr;
                }
                
                $date = date("Y-m-d");

                // Fetch the latest attendance record for today
                $employeeAttendance = AttendanceEmployee::where('employee_id', '=', $emp->id ?? 0)
                    ->where('date', '=', $date)
                    ->first();

                // Pass clock-in time if available
                $clockInTime = $employeeAttendance ? $employeeAttendance->clock_in : null;    
                

                $officeTime['startTime'] = Utility::getValByName('company_start_time');
                $officeTime['endTime'] = Utility::getValByName('company_end_time');
                
                // Fetch a random daily quote
                $quote = DailyQuote::inRandomOrder()->first();

                $todos = ToDoList::where('user_id', Auth::id())
                ->whereDate('end_date', Carbon::today()) // Filter by today's scheduled date
                ->get();



                $today = Carbon::today();

                $notices = Notice::select('id', 'title', 'description', 'notice_startdate', 'notice_enddate')
                    ->where('created_by', '=', \Auth::user()->creatorId())
                    ->whereDate('notice_startdate', '<=', $today) // Show only if started
                    ->whereDate('notice_enddate', '>=', $today) // Show only notices with an end date today or in the future
                    ->orderBy('notice_startdate', 'asc') // Sort by start date in ascending order
                    ->take(5) // Limit to the latest 5 notices
                    ->get();
                
                // Add employeesNotWorkingToday logic for employee dashboard
                $currentDate = Carbon::today()->format('Y-m-d');
                
                // Get terminated employee IDs to exclude them from all queries
                $terminatedEmployeeIds = Termination::where('created_by', \Auth::user()->creatorId())
                    ->pluck('employee_id')
                    ->toArray();
                
                // Get employees who have clocked in today
                $clockedInEmployees = AttendanceEmployee::where('date', '=', $currentDate)
                    ->whereNotNull('clock_in')
                    ->where('clock_in', '!=', '00:00:00')
                    ->pluck('employee_id');

                $notClockIn = AttendanceEmployee::where('date', '=', $currentDate)->pluck('employee_id');


                // Get employees on approved leave today (excluding current employee and terminated employees)
                $employeesOnLeaveToday = Leave::where('created_by', \Auth::user()->creatorId())
                    ->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate)
                    ->where('status', 'approved')
                    ->where('employee_id', '!=', $emp->id) // Exclude current employee
                    ->whereNotIn('employee_id', $terminatedEmployeeIds) // Exclude terminated employees
                    ->pluck('employee_id');

                // Get employees who have week off today (excluding current employee and terminated employees)
                $employeesOnWeekOffIds = collect(); // Disabled - week_off_day column not found

                // Get employees with full month present - exclude them from "Yet To Arrive"
                $fullMonthPresentEmployees = collect(); // Disabled - clock_in_location column not found

                $isHolidayToday = \App\Models\Holiday::where('date', $currentDate)->first();

                if ($isHolidayToday) {
                    $notClockIns = collect();
                } else {
                    $notClockIns = Employee::where('created_by', '=', \Auth::user()->creatorId())
                        ->whereNotIn('id', $clockedInEmployees) // Not clocked in
                        ->whereNotIn('id', $employeesOnLeaveToday) // Not on leave
                        ->whereNotIn('id', $employeesOnWeekOffIds) // Not on week off
                        ->whereNotIn('id', $fullMonthPresentEmployees) // Not full month present
                        ->whereNotIn('id', $terminatedEmployeeIds) // Not terminated
                        ->get();
                }

                    

                // Now get full employee objects for week off (for display)
                $employeesOnWeekOff = collect(); // Disabled - week_off_day column not found

                // Get employees on approved leave (with relationships for display)
                $onLeaveEmployees = Leave::with(['employees', 'leaveType'])
                    ->where('created_by', \Auth::user()->creatorId())
                    ->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate)
                    ->where('status', 'approved')
                    ->where('employee_id', '!=', $emp->id) // Exclude current employee
                    ->whereNotIn('employee_id', $terminatedEmployeeIds) // Exclude terminated employees
                    ->get();

                // Prepare the final list for display
                $employeesNotWorkingToday = collect();

                // Add leave employees
                foreach ($onLeaveEmployees as $leave) {
                    if ($leave->employees) {
                        $employeesNotWorkingToday->push([
                            'employee_name' => $leave->employees->name ?? 'N/A',
                            'status' => $leave->leaveType->title ?? 'Leave'
                        ]);
                    }
                }

                // Add week off employees (excluding duplicates)
                foreach ($employeesOnWeekOff as $employee) {
                    $exists = $employeesNotWorkingToday->contains(function ($item) use ($employee) {
                        return $item['employee_name'] === $employee->name;
                    });
                    
                    if (!$exists) {
                        $employeesNotWorkingToday->push([
                            'employee_name' => $employee->name,
                            'status' => 'Week Off'
                        ]);
                    }
                }

                if ($isHolidayToday) {
                    $employeesNotWorkingToday->push([
                        'employee_name' => 'All Employees',
                        'status' => 'Holiday (' . $isHolidayToday->occasion . ')'
                    ]);
                }

                        $today = Carbon::today();
                        $currentMonth = $today->month;
                        $currentYear = $today->year;
                        
                        // Get birthdays this month (not passed yet) - only active employees
                        $birthdays = Employee::where('created_by', \Auth::user()->creatorId())
                            ->whereMonth('dob', $currentMonth)
                            ->whereNotIn('id', $terminatedEmployeeIds) // Exclude terminated employees
                            ->get()
                            ->map(function ($employee) use ($today, $currentYear, $emp) {
                                $birthdayThisYear = Carbon::create($currentYear, date('m', strtotime($employee->dob)), date('d', strtotime($employee->dob)));
                                if ($birthdayThisYear >= $today) {
                                    $isSelf = ($emp && $emp->id == $employee->id);
                                    if ($isSelf) {
                                        return null; // Do not show own birthday
                                    }
                                    return [
                                        'title' => $employee->name . "'s Birthday",
                                        'start' => $birthdayThisYear->format('Y-m-d'),
                                        'className' => 'bg-success',
                                        'allDay' => true,
                                        'url' => route('employee.show', $employee->id),
                                        'type' => 'birthday',
                                        'employee_name' => $employee->name,
                                        'employee_id' => $employee->id,
                                        'event_date' => $birthdayThisYear->format('Y-m-d'),
                                        'is_self' => false,
                                    ];
                                }
                                return null;
                            })->filter()->values()->toArray();
                        
                        // Get anniversaries this month (completed 1 year or more and not passed yet) - only active employees
                        $anniversaries = Employee::where('created_by', \Auth::user()->creatorId())
                            ->whereMonth('company_doj', $currentMonth)
                            ->whereYear('company_doj', '<=', $currentYear - 1)
                            ->whereNotIn('id', $terminatedEmployeeIds) // Exclude terminated employees
                            ->get()
                            ->map(function ($employee) use ($today, $currentYear, $emp) {
                                $anniversaryThisYear = Carbon::create($currentYear, date('m', strtotime($employee->company_doj)), date('d', strtotime($employee->company_doj)));
                                if ($anniversaryThisYear >= $today) {
                                    $yearsCompleted = $anniversaryThisYear->year - Carbon::parse($employee->company_doj)->year;
                                    $isSelf = ($emp && $emp->id == $employee->id);
                                    if ($isSelf) {
                                        return null; // Do not show own anniversary
                                    }
                                    return [
                                        'title' => $employee->name . "'s Anniversary",
                                        'start' => $anniversaryThisYear->format('Y-m-d'),
                                        'className' => 'bg-primary',
                                        'allDay' => true,
                                        'url' => route('employee.show', $employee->id),
                                        'type' => 'anniversary',
                                        'employee_name' => $employee->name,
                                        'employee_id' => $employee->id,
                                        'event_date' => $anniversaryThisYear->format('Y-m-d'),
                                        'years' => $yearsCompleted,
                                        'is_self' => false,
                                    ];
                                }
                                return null;
                            })->filter()->values()->toArray();
                        
                        // Filter existing events to only current month and future dates
                        $filteredEvents = [];
                        foreach ($events as $event) {
                            $eventDate = Carbon::parse($event->start_date);
                            if ($eventDate->month == $currentMonth && $eventDate >= $today) {
                                $filteredEvents[] = [
                                    'id' => $event['id'],
                                    'title' => $event['title'],
                                    'start' => $event['start_date'],
                                    'end' => $event['end_date'],
                                    'className' => $event['color'],
                                    'url' => route('event.edit', $event['id']),
                                    'type' => 'event'
                                ];
                            }
                        }
                        
                        // Merge all events and sort by date
                        $allEvents = array_merge($filteredEvents, $birthdays, $anniversaries);
                        usort($allEvents, function($a, $b) {
                            return strtotime($a['start']) - strtotime($b['start']);
                        });
                        
                        // Fetch attendance data for calendar (fetch 3 months for smooth navigation)
                        $attendanceData = [];
                        if ($emp) {
                            $currentDate = Carbon::now();
                            // Fetch data for previous month, current month, and next month
                            $startRange = $currentDate->copy()->subMonth()->startOfMonth();
                            $endRange = $currentDate->copy()->addMonth()->endOfMonth();
                            
                            // Get all attendance records for the range
                            $attendances = AttendanceEmployee::where('employee_id', $emp->id)
                                ->whereBetween('date', [$startRange->format('Y-m-d'), $endRange->format('Y-m-d')])
                                ->get();
                            
                            $employeeData = [];
                            foreach ($attendances as $attendance) {
                                $dateFormatted = $attendance->date;
                                $employeeData[$dateFormatted] = [
                                    'type' => 'present',
                                    'clock_in' => $attendance->clock_in,
                                    'clock_out' => $attendance->clock_out
                                ];
                            }
                            
                            // Get leaves for the range
                            $leaves = Leave::where('employee_id', $emp->id)
                                ->where('status', 'approved')
                                ->where(function($query) use ($startRange, $endRange) {
                                    $query->whereBetween('start_date', [$startRange->format('Y-m-d'), $endRange->format('Y-m-d')])
                                          ->orWhereBetween('end_date', [$startRange->format('Y-m-d'), $endRange->format('Y-m-d')])
                                          ->orWhere(function($q) use ($startRange, $endRange) {
                                              $q->where('start_date', '<=', $startRange->format('Y-m-d'))
                                                ->where('end_date', '>=', $endRange->format('Y-m-d'));
                                          });
                                })
                                ->get();
                            
                            foreach ($leaves as $leave) {
                                $start = Carbon::parse($leave->start_date);
                                $end = Carbon::parse($leave->end_date);
                                
                                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                                    $formattedDate = $date->format('Y-m-d');
                                    if ($date->between($startRange, $endRange)) {
                                        if (!isset($employeeData[$formattedDate])) {
                                            $employeeData[$formattedDate] = [
                                                'type' => 'leave',
                                                'reason' => $leave->leave_reason ?? ''
                                            ];
                                        }
                                    }
                                }
                            }
                            // Get holidays for the range
                            $holidays = Holiday::where('created_by', '=', \Auth::user()->creatorId())
                                ->whereBetween('date', [$startRange->format('Y-m-d'), $endRange->format('Y-m-d')])
                                ->get();
                            
                            foreach ($holidays as $holiday) {
                                $formattedDate = $holiday->date;
                                if (!isset($employeeData[$formattedDate])) {
                                    $employeeData[$formattedDate] = [
                                        'type' => 'holiday',
                                        'title' => $holiday->occurrence ?? ''
                                    ];
                                }
                            }
                            
                            // Mark absent/week_off for past days and Sundays
                            $today = Carbon::today();
                            for ($date = $startRange->copy(); $date->lte($endRange); $date->addDay()) {
                                $dateFormatted = $date->format('Y-m-d');
                                $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
                                if (!isset($employeeData[$dateFormatted])) {
                                    if ($dayOfWeek == 0) { // Sunday is week off
                                        $employeeData[$dateFormatted] = ['type' => 'week_off'];
                                    } else if ($date->lte($today)) {
                                        $employeeData[$dateFormatted] = ['type' => 'absent'];
                                    }
                                }
                            }
                            
                            $attendanceData[$emp->id] = [
                                'name' => $emp->name,
                                'data' => $employeeData
                            ];
                        }

                // Get pending leave requests count for the employee
                $pendingLeaveCount = Leave::where('employee_id', $emp->id)
                    ->where('status', 'Pending')
                    ->count();

                $assignedProjects = Project::where(function($q) use ($emp) {
                    $q->whereJsonContains('assigned_data', [['employee_ids' => [(string)$emp->id]]])
                      ->orWhereJsonContains('assigned_data', [['employee_ids' => [$emp->id]]])
                      ->orWhereJsonContains('assigned_data', ['employee_ids' => (string)$emp->id])
                      ->orWhereJsonContains('assigned_data', ['employee_ids' => $emp->id]);
                })->with('modules')->get();

                // Pass employee details to the dashboard
                return view('dashboard.dashboard', compact('employeesOnWeekOffIds', 'allEvents', 'employeesNotWorkingToday', 'notices', 'arrEvents', 'announcements', 'employees', 'meetings', 'employeeAttendance', 'officeTime', 'quote', 'emp', 'clockInTime', 'todos', 'attendanceData', 'pendingLeaveCount', 'assignedProjects'));
            }
            else if ($user->type == 'super admin') {
                $user                       = \Auth::user();
                $user['total_user']         = $user->countCompany();
                $user['total_paid_user']    = $user->countPaidCompany();
                $user['total_orders']       = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan']         = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->name : '');

                $chartData = $this->getOrderChart(['duration' => 'week']);
                // **Daily Quote Logic for Super Admin Dashboard**
                $quote = DailyQuote::inRandomOrder()->first();

                return view('dashboard.super_admin', compact('user', 'chartData', 'quote'));


            } 
            else if ($user->type == 'company' || $user->type == 'hr'|| $user->type == 'Director') {

                $today = Carbon::today();
                $startOfMonth = $today->copy()->startOfMonth();
                $endOfMonth = $today->copy()->endOfMonth();

                $events = Event::where('created_by', '=', \Auth::user()->creatorId())
                    ->whereBetween('start_date', [$startOfMonth, $endOfMonth]) // Filter for current month
                    ->whereDate('start_date', '>=', $today) // Only today or future events
                    ->orderBy('start_date', 'asc') // Sort so today comes first, then future events
                    ->get();

                $arrEvents = [];

                foreach ($events as $event) {
                    $arr['id']    = $event['id'];
                    $arr['title'] = $event['title'];
                    $arr['start'] = $event['start_date'];
                    $arr['end']   = $event['end_date'];
                    $arr['className'] = $event['color'];
                    $arr['employee'] = $event['employee_id'];
                    $arr['url']   = route('event.edit', $event['id']);

                    $arrEvents[] = $arr;
                }



                $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->where('created_by', '=', \Auth::user()->creatorId())->get();

                // Get terminated employee IDs to exclude them from count
                $terminatedEmployeeIds = Termination::where('created_by', \Auth::user()->creatorId())
                    ->pluck('employee_id')
                    ->toArray();

                // Count only active employees (excluding terminated ones)
                $countEmployee = Employee::where('created_by', \Auth::user()->creatorId())
                    ->whereNotIn('id', $terminatedEmployeeIds)
                    ->count();

                

                $user      = User::where('type', '!=', 'employee')->where('created_by', '=', \Auth::user()->creatorId())->get();
                $countUser = count($user);
                $countTicket      = Ticket::where('created_by', '=', \Auth::user()->creatorId())->count();
                $countOpenTicket  = Ticket::where('status', '=', 'open')->where('created_by', '=', \Auth::user()->creatorId())->count();
                $countCloseTicket = Ticket::where('status', '=', 'close')->where('created_by', '=', \Auth::user()->creatorId())->count();

                $currentDate = Carbon::today()->format('Y-m-d');

                // Get employees who have clocked in today
                $clockedInEmployees = AttendanceEmployee::where('date', '=', $currentDate)
                    ->whereNotNull('clock_in')
                    ->where('clock_in', '!=', '00:00:00')
                    ->pluck('employee_id');

                // $employees     = User::where('type', '=', 'employee')->where('created_by', '=', \Auth::user()->creatorId())->get();
                // $countEmployee = count($employees);
                $notClockIn = AttendanceEmployee::where('date', '=', $currentDate)->pluck('employee_id');

                // Get employees on approved leave today (excluding terminated employees)
                $employeesOnLeaveToday = Leave::where('created_by', \Auth::user()->creatorId())
                    ->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate)
                    ->where('status', 'approved')
                    ->whereNotIn('employee_id', $terminatedEmployeeIds) // Exclude terminated employees
                    ->pluck('employee_id');

                // Merge both to exclude from "not clock in" list
                $excludeIds = $notClockIn->merge($employeesOnLeaveToday)->unique();


                // Get employees on approved leave today
                $onLeaveEmployees = Leave::with(['employees', 'leaveType'])
                    ->where('created_by', \Auth::user()->creatorId())
                    ->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate)
                    ->where('status', 'approved')
                    ->whereNotIn('employee_id', $clockedInEmployees) // Exclude those who clocked in
                    ->get();

                // Get employees who have week off today (just IDs first)
                $employeesOnWeekOffIds = collect(); // Disabled - week_off_day column not found

                // Get employees with full month present - exclude them from "Yet To Arrive"
                $fullMonthPresentEmployees = collect(); // Disabled - clock_in_location column not found

                $notClockIns = Employee::where('created_by', '=', \Auth::user()->creatorId())
                    ->whereNotIn('id', $clockedInEmployees) // Not clocked in
                    ->whereNotIn('id', $employeesOnLeaveToday) // Not on leave
                    ->whereNotIn('id', $employeesOnWeekOffIds) // Not on week off
                    ->whereNotIn('id', $fullMonthPresentEmployees) // Not full month present
                    ->whereNotIn('id', $terminatedEmployeeIds) // Not terminated
                    ->get();

                // Now get full employee objects for week off (for display)
                $employeesOnWeekOff = collect(); // Disabled - week_off_day column not found

                // Get employees on approved leave (with relationships for display)
                $onLeaveEmployees = Leave::with(['employees', 'leaveType'])
                    ->where('created_by', \Auth::user()->creatorId())
                    ->where('start_date', '<=', $currentDate)
                    ->where('end_date', '>=', $currentDate)
                    ->where('status', 'approved')
                    ->whereNotIn('employee_id', $terminatedEmployeeIds) // Exclude terminated employees
                    ->get();

                // Prepare the final list for display
                $employeesNotWorkingToday = collect();

                // Add leave employees (who didn't clock in)
                foreach ($onLeaveEmployees as $leave) {
                    if ($leave->employees) {
                        $employeesNotWorkingToday->push([
                            'employee_name' => $leave->employees->name ?? 'N/A',
                            'status' => $leave->leaveType->title ?? 'Leave'
                        ]);
                    }
                }

                // Add week off employees (who didn't clock in)
                foreach ($employeesOnWeekOff as $employee) {
                    $exists = $employeesNotWorkingToday->contains(function ($item) use ($employee) {
                        return $item['employee_name'] === $employee->name;
                    });
                    
                    if (!$exists) {
                        $employeesNotWorkingToday->push([
                            'employee_name' => $employee->name,
                            'status' => 'Week Off'
                        ]);
                    }
                }



                // Fetch present employees based on today's date
               // Get the total number of active employees (excluding terminated ones)
                $totalEmployees = Employee::where('created_by', \Auth::user()->creatorId())
                    ->whereNotIn('id', $terminatedEmployeeIds)
                    ->count();
                
                

                // Get present employees for today
                $presentEmployees = AttendanceEmployee::where('date', '=', $currentDate)
                    ->whereNotNull('clock_in')
                    ->where('clock_in', '!=', '00:00:00')
                    ->get();

                // Calculate the attendance percentage
                $attendancePercentage = $totalEmployees > 0 ? (count($presentEmployees) / $totalEmployees) * 100 : 0;

                // Get employees who are present and their clock-in time
                // Note: Location is optional - show all employees with clock_in regardless of location
                $presentEmployeesWithClockIn = AttendanceEmployee::where('date', '=', $currentDate)
                    ->whereNotNull('clock_in')
                    ->where('clock_in', '!=', '00:00:00')
                    ->with('employee')
                    ->get()
                    ->map(function ($attendance) {
                        return [
                            'employee' => $attendance->employee,
                            'clock_in' => $attendance->clock_in,
                            'clock_in_location' => $attendance->clock_in_location ?? 'Location not captured yet',
                            'clock_in_latitude' => $attendance->clock_in_latitude,
                            'clock_in_longitude' => $attendance->clock_in_longitude,
                            'clock_in_location_captured_at' => $attendance->clock_in_location_captured_at,
                            'clock_out' => ($attendance->clock_out && $attendance->clock_out != '00:00:00') ? $attendance->clock_out : '--:--',
                            'clock_out_location' => $attendance->clock_out_location ?? 'Location not captured yet',
                            'clock_out_latitude' => $attendance->clock_out_latitude,
                            'clock_out_longitude' => $attendance->clock_out_longitude,
                            'clock_out_location_captured_at' => $attendance->clock_out_location_captured_at,
                        ];
                    });

                $accountBalance = AccountList::where('created_by', '=', \Auth::user()->creatorId())->sum('initial_balance');
                $activeJob   = Job::where('status', 'active')->where('created_by', '=', \Auth::user()->creatorId())->count();
                $inActiveJOb = Job::where('status', 'in_active')->where('created_by', '=', \Auth::user()->creatorId())->count();

                $totalPayee = Payees::where('created_by', '=', \Auth::user()->creatorId())->count();
                $totalPayer = Payer::where('created_by', '=', \Auth::user()->creatorId())->count();

                // $meetings = Meeting::where('created_by', '=', \Auth::user()->creatorId())->limit(8)->get();

                $meetings = Meeting::where('created_by', Auth::id())
                 ->whereDate('created_at', Carbon::today()) // Filter by today's date
                 ->get();
                

                $users = User::find(\Auth::user()->creatorId());
                $plan = Plan::find($users->plan);
                if ($plan->storage_limit > 0) {
                    $storage_limit = ($users->storage_limit / $plan->storage_limit) * 100;
                } else {
                    $storage_limit = 0;
                } 
                 // **Daily Quote Logic for Other Users Dashboard**
                 $quote = DailyQuote::inRandomOrder()->first();


                 $totalDepartment = Department::where('created_by', '=', \Auth::user()->creatorId())->count();

                 $today = Carbon::today()->toDateString();
                 $totalleaves = Leave::where('created_by', '=', \Auth::user()->creatorId())
                     ->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today)
                     ->where('status', 'approved')
                     ->count();

                 $projects = Project::all(); // Gets all projects without any conditions
                 
                 // Prepare employee data for projects (similar to ProjectController)
                 $departmentIds = [];
                 $employeeIds = [];
                 
                 foreach ($projects as $project) {
                     if (is_array($project->assigned_data)) {
                         foreach ($project->assigned_data as $assignment) {
                             if (!empty($assignment['department_id'])) {
                                 $departmentIds[] = $assignment['department_id'];
                             }
                             if (!empty($assignment['employee_ids']) && is_array($assignment['employee_ids'])) {
                                 $employeeIds = array_merge($employeeIds, $assignment['employee_ids']);
                             }
                         }
                     }
                 }
                 
                 // Get unique IDs
                 $departmentIds = array_unique($departmentIds);
                 $employeeIds = array_unique($employeeIds);
                 
                 // Preload data
                 $departments = Department::whereIn('id', $departmentIds)->get()->keyBy('id');
                 $employees = Employee::with('user')->whereIn('id', $employeeIds)->get()->keyBy('id');
                 
                 
                 $totalProjects = Project::count();

                 $totalHolidays = Holiday::count();

                 $todos = ToDoList::where('user_id', Auth::id())
                 ->whereDate('end_date', Carbon::today()) // Filter by today's scheduled date
                 ->get();
             

                    // Fetch income and expense data for the current month
                    $currentMonth = date('m');
                    $currentYear = date('Y');

                    // Fetch income data for the current month
                    $incomeData = Deposit::where('created_by', \Auth::user()->creatorId())
                        ->whereMonth('date', $currentMonth)
                        ->whereYear('date', $currentYear)
                        ->get()
                        ->groupBy(function ($date) {
                            return \Carbon\Carbon::parse($date->date)->format('d M Y'); // Group by day
                        })
                        ->map(function ($row) {
                            return $row->sum('amount');
                        });

                    // Fetch expense data for the current month
                    $expenseData = Expense::where('created_by', \Auth::user()->creatorId())
                        ->whereMonth('date', $currentMonth)
                        ->whereYear('date', $currentYear)
                        ->get()
                        ->groupBy(function ($date) {
                            return \Carbon\Carbon::parse($date->date)->format('d M Y'); // Group by day
                        })
                        ->map(function ($row) {
                            return $row->sum('amount');
                        });

                    // Prepare labels (dates) for the chart
                    $labels = $incomeData->keys()->merge($expenseData->keys())->unique()->sort();

                    // Prepare data for the chart
                    $incomeChartData = $labels->map(function ($date) use ($incomeData) {
                        return $incomeData->has($date) ? $incomeData[$date] : 0;
                    });

                    $expenseChartData = $labels->map(function ($date) use ($expenseData) {
                        return $expenseData->has($date) ? $expenseData[$date] : 0;
                    });

                    // Format data for the chart (same as income&expense.blade.php)
                    $data = [
                        [
                            'name' => 'Income',
                            'data' => $incomeChartData->values(),
                        ],
                        [
                            'name' => 'Expense',
                            'data' => $expenseChartData->values(),
                        ],
                    ];

                    // Pass data to the view
                    $chartData = [
                        'labels' => $labels->values(),
                        'data' => $data,
                    ];

                    $notices = Notice::select('id', 'title', 'description', 'notice_startdate', 'notice_enddate')
                    ->where('created_by', '=', \Auth::user()->creatorId())
                    ->orderBy('notice_startdate', 'desc')
                    ->take(5) // Limit to the latest 5 notices
                    ->get();


                    $todayEnquiryCount = TimeSheet::whereDate('created_at', Carbon::today())->count();


                    // Get current date and month
                        $today = Carbon::today();
                        $currentMonth = $today->month;
                        $currentYear = $today->year;
                        
                        // Get birthdays this month (not passed yet) - only active employees
                        $birthdays = Employee::where('created_by', \Auth::user()->creatorId())
                            ->whereMonth('dob', $currentMonth)
                            ->whereNotIn('id', $terminatedEmployeeIds) // Exclude terminated employees
                            ->get()
                            ->map(function ($employee) use ($today, $currentYear) {
                                $birthdayThisYear = Carbon::create($currentYear, date('m', strtotime($employee->dob)), date('d', strtotime($employee->dob)));
                                if ($birthdayThisYear >= $today) {
                                     return [
                                        'title' => $employee->name . "'s Birthday",
                                        'start' => $birthdayThisYear->format('Y-m-d'),
                                        'className' => 'bg-success',
                                        'allDay' => true,
                                        'url' => route('employee.show', $employee->id),
                                        'type' => 'birthday',
                                        'employee_name' => $employee->name,
                                        'employee_id' => $employee->id,
                                        'event_date' => $birthdayThisYear->format('Y-m-d'),
                                        'employee_avatar' => (!empty($employee->user->avatar)) ? asset(\Storage::url('uploads/avatar')) . '/' . $employee->user->avatar : asset(\Storage::url('uploads/avatar')) . '/avatar.png',
                                    ];
                                }
                                return null;
                            })->filter()->values()->toArray();
                        
                        // Get anniversaries this month (completed 1 year or more and not passed yet) - only active employees
                        $anniversaries = Employee::where('created_by', \Auth::user()->creatorId())
                            ->whereMonth('company_doj', $currentMonth)
                            ->whereYear('company_doj', '<=', $currentYear - 1)
                            ->whereNotIn('id', $terminatedEmployeeIds) // Exclude terminated employees
                            ->get()
                            ->map(function ($employee) use ($today, $currentYear) {
                                $anniversaryThisYear = Carbon::create($currentYear, date('m', strtotime($employee->company_doj)), date('d', strtotime($employee->company_doj)));
                                if ($anniversaryThisYear >= $today) {
                                    $yearsCompleted = $anniversaryThisYear->year - Carbon::parse($employee->company_doj)->year;
                                return [
                                        'title' => $employee->name . "'s Anniversary",
                                        'start' => $anniversaryThisYear->format('Y-m-d'),
                                        'className' => 'bg-primary',
                                        'allDay' => true,
                                        'url' => route('employee.show', $employee->id),
                                        'type' => 'anniversary',
                                        'employee_name' => $employee->name,
                                        'employee_id' => $employee->id,
                                        'event_date' => $anniversaryThisYear->format('Y-m-d'),
                                        'years' => $yearsCompleted,
                                        'employee_avatar' => (!empty($employee->user->avatar)) ? asset(\Storage::url('uploads/avatar')) . '/' . $employee->user->avatar : asset(\Storage::url('uploads/avatar')) . '/avatar.png',
                                    ];
                                }
                                return null;
                            })->filter()->values()->toArray();
                        
                        // Filter existing events to only current month and future dates
                        $filteredEvents = [];
                        foreach ($events as $event) {
                            $eventDate = Carbon::parse($event->start_date);
                            if ($eventDate->month == $currentMonth && $eventDate >= $today) {
                                $filteredEvents[] = [
                                    'id' => $event['id'],
                                    'title' => $event['title'],
                                    'start' => $event['start_date'],
                                    'end' => $event['end_date'],
                                    'className' => $event['color'],
                                    'url' => route('event.edit', $event['id']),
                                    'type' => 'event'
                                ];
                            }
                        }
                        
                        // Merge all events and sort by date
                        $allEvents = array_merge($filteredEvents, $birthdays, $anniversaries);
                        usort($allEvents, function($a, $b) {
                            return strtotime($a['start']) - strtotime($b['start']);
                        });
                        

                    



                return view('dashboard.company', compact('employeesOnWeekOffIds', 'allEvents', 'employeesNotWorkingToday', 'todayEnquiryCount','notices','totalHolidays', 'arrEvents', 'announcements', 'employees', 'activeJob', 'inActiveJOb', 'meetings', 'countEmployee', 'countUser', 'countTicket', 'countOpenTicket', 'countCloseTicket', 'notClockIns','onLeaveEmployees', 'accountBalance', 'totalPayee', 'totalPayer', 'users', 'plan', 'storage_limit', 'quote','attendancePercentage', 'presentEmployeesWithClockIn', 'totalEmployees', 'totalDepartment', 'totalleaves', 'projects', 'todos','chartData', 'totalProjects'));
            }
        } 
    }


   public function filterDashboardData(Request $request)
{
    $filterType = $request->filter_type;
    $customDate = $request->custom_date ?? null;
    
    // Determine the date to filter by
    if ($filterType === 'yesterday') {
        $date = Carbon::yesterday();
    } elseif ($filterType === 'custom' && $customDate) {
        $date = Carbon::parse($customDate);
    } else {
        $date = Carbon::today(); // Default to today
    }
    
    $dateString = $date->format('Y-m-d');
    
    // Get terminated employee IDs to exclude them from all queries
    $terminatedEmployeeIds = Termination::where('created_by', \Auth::user()->creatorId())
        ->pluck('employee_id')
        ->toArray();
    
    // Get data for the selected date
    $todayEnquiryCount = TimeSheet::whereDate('created_at', $date)->count();
    
    // Get attendance data for the selected date
    // Location is optional - show all employees with clock_in regardless of location
    $presentEmployeesWithClockIn = AttendanceEmployee::where('date', '=', $dateString)
        ->whereNotNull('clock_in')
        ->where('clock_in', '!=', '00:00:00')
        ->with('employee')
        ->get()
        ->map(function ($attendance) {
            $timeline = '--:--';
            if (!empty($attendance->clock_in) && $attendance->clock_in !== '00:00:00') {
                try {
                    $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                    if (!empty($attendance->clock_out) && $attendance->clock_out !== '00:00:00') {
                        $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                        $diff = $clockIn->diff($clockOut);
                    } else {
                        $diff = $clockIn->diff(\Carbon\Carbon::now());
                    }
                    $timeline = $diff->h . ' hours ' . $diff->i . ' minutes';
                } catch (\Exception $e) {
                    $timeline = '--:--';
                }
            }

            return [
                'employee' => $attendance->employee,
                'clock_in' => $attendance->clock_in,
                'clock_in_location' => $attendance->clock_in_location ?? 'Location not captured yet',
                'clock_in_latitude' => $attendance->clock_in_latitude,
                'clock_in_longitude' => $attendance->clock_in_longitude,
                'clock_in_location_captured_at' => $attendance->clock_in_location_captured_at,
                'clock_out' => ($attendance->clock_out && $attendance->clock_out != '00:00:00') ? $attendance->clock_out : '--:--',
                'clock_out_location' => $attendance->clock_out_location ?? 'Location not captured yet',
                'clock_out_latitude' => $attendance->clock_out_latitude,
                'clock_out_longitude' => $attendance->clock_out_longitude,
                'clock_out_location_captured_at' => $attendance->clock_out_location_captured_at,
                'total_working_hours' => $timeline,
            ];
        })->toArray();
    
    // Get employees who have clocked in (location is optional)
    $clockedInEmployees = AttendanceEmployee::where('date', '=', $dateString)
        ->whereNotNull('clock_in')
        ->where('clock_in', '!=', '00:00:00')
        ->pluck('employee_id');
    
    // Get employees on approved leave today
    $employeesOnLeaveToday = Leave::where('created_by', \Auth::user()->creatorId())
        ->where('start_date', '<=', $dateString)
        ->where('end_date', '>=', $dateString)
        ->where('status', 'approved')
        ->whereNotIn('employee_id', $terminatedEmployeeIds)
        ->pluck('employee_id');
    
    // Get employees who have week off today - Disabled - week_off_day column not found
    $employeesOnWeekOffIds = collect();
    
    // All employees with clock_in are considered present (location is optional)
    $fullMonthPresentEmployees = collect([]); // Empty - location is no longer a requirement
    
    // Get not clocked in employees (excluding those on leave, week off, full month present, or terminated)
    $notClockIns = Employee::where('created_by', '=', \Auth::user()->creatorId())
        ->whereNotIn('id', $clockedInEmployees)
        ->whereNotIn('id', $employeesOnLeaveToday)
        ->whereNotIn('id', $employeesOnWeekOffIds)
        ->whereNotIn('id', $fullMonthPresentEmployees)
        ->whereNotIn('id', $terminatedEmployeeIds)
        ->get()
        ->map(function ($employee) {
            return [
                'name' => $employee->name,
                'id' => $employee->id
            ];
        })->toArray();
    
    // Get employees on approved leave (with relationships for display)
    $onLeaveEmployees = Leave::with(['employees', 'leaveType'])
        ->where('created_by', \Auth::user()->creatorId())
        ->where('start_date', '<=', $dateString)
        ->where('end_date', '>=', $dateString)
        ->where('status', 'approved')
        ->whereNotIn('employee_id', $terminatedEmployeeIds)
        ->get();
    
    // Get employees on week off - Disabled - week_off_day column not found
    $employeesOnWeekOff = collect();
    
    // Prepare the final list for display (matching the structure from main dashboard)
    $employeesNotWorkingToday = collect();
    
    // Add leave employees
    foreach ($onLeaveEmployees as $leave) {
        if ($leave->employees) {
            $employeesNotWorkingToday->push([
                'employee_name' => $leave->employees->full_name ?? 'N/A',
                'status' => $leave->leaveType->title ?? 'Leave'
            ]);
        }
    }
    
    // Add week off employees (excluding duplicates)
    foreach ($employeesOnWeekOff as $employee) {
        $exists = $employeesNotWorkingToday->contains(function ($item) use ($employee) {
            return $item['employee_name'] === $employee->full_name;
        });
        
        if (!$exists) {
            $employeesNotWorkingToday->push([
                'employee_name' => $employee->full_name,
                'status' => 'Week Off'
            ]);
        }
    }
    
    // Count leaves active on the selected date
    $totalLeaves = Leave::where('created_by', \Auth::user()->creatorId())
        ->where('start_date', '<=', $dateString)
        ->where('end_date', '>=', $dateString)
        ->where('status', 'approved')
        ->whereNotIn('employee_id', $terminatedEmployeeIds)
        ->count();

    // Get todos for the selected date
    $todos = ToDoList::where('user_id', \Auth::id())
        ->whereDate('end_date', $dateString)
        ->get();
        
    $schedulesHtml = view('dashboard.schedules_list', compact('todos'))->render();

    return response()->json([
        'success' => true,
        'todayEnquiryCount' => $todayEnquiryCount,
        'presentEmployeesWithClockIn' => $presentEmployeesWithClockIn,
        'notClockIns' => $notClockIns,
        'employeesNotWorkingToday' => $employeesNotWorkingToday->toArray(),
        'selectedDate' => $dateString,
        'totalLeaves' => $totalLeaves,
        'schedulesHtml' => $schedulesHtml,
    ]);
}
    

    /**
     * Send birthday or anniversary wishes to an employee.
     */
    public function sendWishes(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'event_type'  => 'required|in:birthday,anniversary',
        ]);

        $recipientEmployee = Employee::with(['designation', 'department'])->find($request->employee_id);

        if (!$recipientEmployee || empty($recipientEmployee->email)) {
            return response()->json(['success' => false, 'message' => 'Recipient email not found.'], 422);
        }

        // Sender info — the logged-in user's employee record
        $senderUser     = Auth::user();
        $senderEmployee = Employee::with('designation')->where('user_id', $senderUser->id)->first();

        $senderName        = $senderEmployee ? $senderEmployee->name : $senderUser->name;
        $senderDesignation = ($senderEmployee && $senderEmployee->designation) ? $senderEmployee->designation->name : '';
        $companyName       = config('app.name', 'HR Portal');

        $mailData = [
            'event_type'        => $request->event_type,
            'recipient_name'    => $recipientEmployee->name,
            'sender_name'       => $senderName,
            'sender_designation'=> $senderDesignation,
            'company_name'      => $companyName,
            'custom_message'    => $request->input('custom_message'),
            'years'             => $request->input('years'),
            'event_date'        => $request->input('event_date'),
        ];

        try {
            \Mail::to($recipientEmployee->email)
                ->send(new \App\Mail\WishesMail($mailData));

            // Send Push Notification
            $recipientUser = User::find($recipientEmployee->user_id);
            if ($recipientUser && $recipientUser->device_token) {
                $title = $request->event_type == 'birthday' ? 'Happy Birthday!' : 'Happy Work Anniversary!';
                $message = $request->input('custom_message') ?: 'Best wishes from ' . $companyName . '!';
                \App\Helpers\OneSignalHelper::sendPushNotification(
                    $recipientUser->device_token,
                    $title,
                    $message
                );
            }

            return response()->json(['success' => true, 'message' => 'Wishes sent successfully!']);
        } catch (\Exception $e) {
            \Log::error('WishesMail failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }


    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if ($employee) {
            $attendance = AttendanceEmployee::where('employee_id', $employee->id)
                ->where('date', date('Y-m-d'))
                ->first();

            if ($attendance && !$attendance->clock_out) {
                $attendance->clock_out = now()->format('H:i:s');
                $attendance->save();

                return redirect()->back()->with('success', 'You have successfully clocked out.');
            } else {
                return redirect()->back()->with('error', 'You have already clocked out today.');
            }
        }

        return redirect()->back()->with('error', 'Employee not found.');
    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration']) {
            if ($arrParam['duration'] == 'week') {
                $previous_week = strtotime("-2 week +1 day");
                for ($i = 0; $i < 14; $i++) {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach ($arrDuration as $date => $label) {

            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }

    private function extractMainLocation($fullLocation)
{
    if (empty($fullLocation)) {
        return '';
    }

    // Example 1: If location is comma-separated (like "Building A, Floor 3, Room 101")
    $parts = explode(',', $fullLocation);
    return trim($parts[0]); // Returns "Building A"

    // OR Example 2: If you have specific logic to determine main location
    // return your_custom_logic_to_extract_main_location($fullLocation);
    
    // OR Example 3: If location is JSON, decode it first
    // $locationData = json_decode($fullLocation, true);
    // return $locationData['main_location'] ?? $locationData['building'] ?? '';
}
}