<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Employee;
use App\Models\ProjectDailyUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProjectReportController extends Controller
{
    public function index()
    {
        if (Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        $projects = Project::with(['modules', 'dailyUpdates'])->get();
        $employees = Employee::whereHas('dailyUpdates')->with(['dailyUpdates' => function($q) {
            $q->where('status', 'approved');
        }])->get();

        return view('reports.project_reports', compact('projects', 'employees'));
    }

    public function employeeDailyReports(Request $request)
    {
        if (Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        $updatesQuery = ProjectDailyUpdate::with(['employee.user', 'project', 'module'])
            ->whereHas('employee', function($q) {
                $q->notTerminated();
            });

        $generalTasksQuery = \App\Models\GeneralDailyTask::with(['employee.user'])
            ->whereHas('employee', function($q) {
                $q->notTerminated();
            });

        if ($request->filled('employee_id')) {
            $updatesQuery->where('employee_id', $request->employee_id);
            $generalTasksQuery->where('employee_id', $request->employee_id);
        }

        $startDate = $request->input('start_date', \Carbon\Carbon::now()->format('Y-m-d'));
        $endDate = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));

        if ($startDate && $endDate) {
            $updatesQuery->whereBetween('work_date', [$startDate, $endDate]);
            $generalTasksQuery->whereBetween('work_date', [$startDate, $endDate]);
        }

        $updatesQuery->orderBy('created_at', 'desc');
        $updates = $updatesQuery->get();

        $generalTasksQuery->orderBy('created_at', 'desc');
        $generalTasks = $generalTasksQuery->get();

        $allEmployees = Employee::where('created_by', Auth::user()->creatorId())
            ->notTerminated()
            ->whereHas('user', function($q) {
                $q->where('type', 'employee');
            })->pluck('name', 'id');

        $totalWorkingHours = 0;
        $totalAvailableHours = 0;
        $remainingHours = 0;

        if ($request->filled('employee_id') && $startDate && $endDate) {
            $totalWorkingHours = $updates->sum('hours_worked') + $generalTasks->sum('duration');

            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                if ($date->isWeekday()) {
                    $totalAvailableHours += 8;
                } elseif ($date->isSaturday()) {
                    $totalAvailableHours += 4;
                }
            }
            $remainingHours = $totalAvailableHours - $totalWorkingHours;
        }

        // Group project updates by employee + date
        $groupedUpdates = $updates->groupBy(function($u) {
            return $u->employee_id . '|' . $u->work_date;
        })->map(function($group) {
            $first = $group->first();
            return [
                'employee_id'   => $first->employee_id,
                'employee'      => $first->employee,
                'work_date'     => $first->work_date,
                'total_hours'   => $group->sum('hours_worked'),
                'entries'       => $group,
            ];
        })->values();

        // Group general tasks by employee + date
        $groupedTasks = $generalTasks->groupBy(function($t) {
            return $t->employee_id . '|' . $t->work_date;
        })->map(function($group) {
            $first = $group->first();
            return [
                'employee_id' => $first->employee_id,
                'employee'    => $first->employee,
                'work_date'   => $first->work_date,
                'total_hours' => $group->sum('duration'),
                'entries'     => $group,
            ];
        })->values();

        // Combine project updates and general tasks by employee + date for consolidated view
        $combined = collect();

        foreach ($updates as $update) {
            $key = $update->employee_id . '|' . $update->work_date;
            if (!$combined->has($key)) {
                $combined->put($key, [
                    'employee_id' => $update->employee_id,
                    'employee' => $update->employee,
                    'work_date' => $update->work_date,
                    'project_entries' => collect(),
                    'general_entries' => collect(),
                ]);
            }
            $combined->get($key)['project_entries']->push($update);
        }

        foreach ($generalTasks as $task) {
            $key = $task->employee_id . '|' . $task->work_date;
            if (!$combined->has($key)) {
                $combined->put($key, [
                    'employee_id' => $task->employee_id,
                    'employee' => $task->employee,
                    'work_date' => $task->work_date,
                    'project_entries' => collect(),
                    'general_entries' => collect(),
                ]);
            }
            $combined->get($key)['general_entries']->push($task);
        }

        $groupedReports = $combined->map(function($item) {
            $item['total_hours'] = $item['project_entries']->sum('hours_worked') + $item['general_entries']->sum('duration');
            
            // Set type tags
            $item['project_entries']->each(function($e) { $e->entry_type = 'project'; });
            $item['general_entries']->each(function($t) { $t->entry_type = 'general'; });
            
            // Combine all entries
            $item['all_entries'] = $item['project_entries']->concat($item['general_entries']);
            
            return $item;
        })->sortByDesc('work_date')->values();

        return view('reports.employee_daily_reports', compact(
            'updates', 'generalTasks', 'groupedUpdates', 'groupedTasks', 'groupedReports',
            'allEmployees', 'startDate', 'endDate',
            'totalAvailableHours', 'totalWorkingHours', 'remainingHours'
        ));
    }

    public function publicClientReport(Request $request, $token)
    {
        $project = Project::with(['modules', 'dailyUpdates' => function($q) {
            $q->where('status', 'approved')->orderBy('work_date', 'desc')->take(10);
        }, 'dailyUpdates.employee', 'delays', 'documents', 'screenshots', 'activities' => function($q) {
            $q->orderBy('created_at', 'desc')->take(15);
        }])->where('share_token', $token)->firstOrFail();

        if (!$project->share_link_enabled) {
            abort(404);
        }

        if ($project->share_password) {
            // Check if authenticated via session
            if ($request->session()->get('project_auth_' . $project->id) !== true) {
                return view('reports.project_client_password', compact('project', 'token'));
            }
        }

        return view('reports.project_client_report', compact('project'));
    }

    public function verifyPassword(Request $request, $token)
    {
        $project = Project::where('share_token', $token)->firstOrFail();
        
        if ($request->password === $project->share_password) {
            $request->session()->put('project_auth_' . $project->id, true);
            return redirect()->route('project.public.report', $token);
        }

        return redirect()->back()->with('error', 'Incorrect password.');
    }
}
