<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Termination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ProjectController extends Controller
{
    /**
     * Subquery to exclude terminated employees (employees existing in `terminations` table).
     */
    protected function terminatedEmployeeSubquery($query)
    {
        return $query->select('employee_id')->from('terminations');
    }
    
    public function index()
    {
        $projects = Project::all();
        
        // Preload all needed departments, employees, and site heads
        $departmentIds = [];
        $employeeIds = [];
        $siteHeadIds = [];
        
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
            
            // Get site head IDs - ensure this is properly formatted
            if ($project->site_heads && is_array($project->site_heads)) {
                $siteHeadIds = array_merge($siteHeadIds, $project->site_heads);
            }
        }
        
        // Get unique IDs
        $departmentIds = array_unique($departmentIds);
        $employeeIds = array_unique($employeeIds);
        $siteHeadIds = array_unique($siteHeadIds);
        
        // Preload data
        $departments = Department::whereIn('id', $departmentIds)->get()->keyBy('id');
        $employees = Employee::with('user')->whereIn('id', $employeeIds)->get()->keyBy('id');
        $siteHeads = Employee::with('user')->whereIn('id', $siteHeadIds)->get()->keyBy('id');
        
        return view('projects.index', compact('projects', 'departments', 'employees', 'siteHeads'));
    }

    public function show(Project $project)
    {
        if (\Auth::user()->type == 'employee') {
            // Ensure employee is assigned
            $isAssigned = false;
            $employeeId = \Auth::user()->employee->id ?? 0;
            
            foreach ($project->assigned_data ?? [] as $data) {
                if (isset($data['employee_ids']) && in_array($employeeId, $data['employee_ids'])) {
                    $isAssigned = true; break;
                }
                if (isset($data['employees'])) {
                    foreach ($data['employees'] as $emp) {
                        if ($emp['id'] == $employeeId) {
                            $isAssigned = true; break 2;
                        }
                    }
                }
            }
            if (!$isAssigned && !\Auth::user()->can('Create Employee') && !\Auth::user()->isTester()) {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        }
        
        $project->load(['modules', 'dailyUpdates.employee', 'dailyUpdates.module', 'delays.creator', 'documents.uploader', 'screenshots.uploader', 'activities.creator']);
        return view('projects.show', compact('project'));
    }

    public function updateShareSettings(Request $request, Project $project)
    {
        if (\Auth::user()->type != 'company' && !\Auth::user()->isTester()) {
            abort(403);
        }

        $project->share_link_enabled = $request->has('share_link_enabled');
        $project->share_password = $request->share_password;

        if ($project->share_link_enabled && empty($project->share_token)) {
            $project->share_token = \Illuminate\Support\Str::random(32);
        }

        $project->save();

        return redirect()->back()->with('success', 'Project sharing settings updated successfully.');
    }

    public function myProjects()
    {
        $employeeId = \Auth::user()->employee->id ?? 0;
        
        $projects = Project::where(function($q) use ($employeeId) {
            $q->whereJsonContains('assigned_data', [['employee_ids' => [(string)$employeeId]]])
              ->orWhereJsonContains('assigned_data', [['employee_ids' => [$employeeId]]])
              ->orWhereJsonContains('assigned_data', ['employee_ids' => (string)$employeeId])
              ->orWhereJsonContains('assigned_data', ['employee_ids' => $employeeId]);
        })->get();
        
        return view('projects.my_projects', compact('projects'));
    }

    public function projectLinks()
    {
        if (\Auth::user()->type != 'company' && !\Auth::user()->isTester()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $projects = Project::select('project_name', 'website_url', 'dashboard_url')->get();
        return view('projects.links', compact('projects'));
    }

    public function myUpdates(Request $request)
    {
        $employeeId = \Auth::user()->employee->id ?? 0;
        
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->format('Y-m-d'));
        $endDate = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));

        $myProjects = \App\Models\Project::where(function($q) use ($employeeId) {
            $q->whereJsonContains('assigned_data', [['employee_ids' => [(string)$employeeId]]])
              ->orWhereJsonContains('assigned_data', [['employee_ids' => [$employeeId]]])
              ->orWhereJsonContains('assigned_data', ['employee_ids' => (string)$employeeId])
              ->orWhereJsonContains('assigned_data', ['employee_ids' => $employeeId]);
        })->with('modules')->get();

        $updatesQuery = \App\Models\ProjectDailyUpdate::where('employee_id', $employeeId)->with('project', 'module')->orderBy('work_date', 'desc')->orderBy('created_at', 'desc');
        $generalTasksQuery = \App\Models\GeneralDailyTask::where('employee_id', $employeeId)->orderBy('work_date', 'desc')->orderBy('created_at', 'desc');

        if ($startDate && $endDate) {
            $updatesQuery->whereBetween('work_date', [$startDate, $endDate]);
            $generalTasksQuery->whereBetween('work_date', [$startDate, $endDate]);
        }

        $updates = $updatesQuery->get();
        $generalTasks = $generalTasksQuery->get();

        $totalWorkingHours = $updates->sum('hours_worked') + $generalTasks->sum('duration');

        $totalAvailableHours = 0;
        if ($startDate && $endDate) {
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                if ($date->isWeekday()) {
                    $totalAvailableHours += 8;
                } elseif ($date->isSaturday()) {
                    $totalAvailableHours += 4;
                }
            }
        }

        $remainingHours = $totalAvailableHours - $totalWorkingHours;

        return view('projects.my_updates', compact(
            'updates', 'generalTasks', 'startDate', 'endDate',
            'totalAvailableHours', 'totalWorkingHours', 'remainingHours', 'myProjects'
        ));
    }

    public function create()
    {
        if (Auth::user()->can('Create Employee') || Auth::user()->type == 'company' || Auth::user()->isTester()) {
            $branches = Branch::all(); // Add this line
            $departments = Department::all();
            
            $uiUxDept = Department::where('name', 'UI-UX Designer')->first();
            $uiUxDeptId = $uiUxDept ? $uiUxDept->id : '';
            
            // Exclude terminated employees from selectable lists
            $employees = Employee::with('user')
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->get();

            $teamLeaders = Employee::with('user')
                ->where('is_team_leader', 1)
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->get();
                
            return view('projects.create', compact('branches', 'departments', 'employees', 'uiUxDeptId', 'teamLeaders')); // Add branches
        }
        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function store(Request $request)
    {
        \Log::debug('Request data:', $request->all()); // Add this line
        try {
            // Validate with proper error messages
            $validated = $request->validate([
                'project_name' => 'required|string|max:255',
                'client_name' => 'required|string|max:255',
                'project_description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'project_type' => 'required|array',
                'project_type.*' => 'string',
                'project_startdate' => 'nullable|date',
                'project_days' => 'nullable|integer|min:1',
                'project_enddate' => 'nullable|date|after_or_equal:project_startdate',
                'estimated_hours' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:on_boarding,on_hold,assigned,in_progress,completed',
                'current_status' => 'nullable|string|max:255',
                'technology' => 'nullable|array',
                'technology.*' => 'string',
                'custom_technology' => 'nullable|string',
                'delay_reason' => 'nullable|string',
                'assigned_data' => 'required|json',
                'site_heads' => 'nullable|array',
                'site_heads.*' => 'exists:employees,id',
                'ui_ux_required' => 'nullable|boolean',
                'website_url' => 'nullable|url|max:255',
                'dashboard_url' => 'nullable|url|max:255',
                'project_lead' => 'nullable|exists:employees,id',
            ], [
                'project_name.required' => 'The project name field is required.',
                'assigned_data.required' => 'You must assign at least one employee.',
                'assigned_data.json' => 'Invalid assignment data format.',
                'project_enddate.after_or_equal' => 'The end date must be after or equal to the start date.',
            ]);

            \Log::debug('Validated data:', $validated); // Add this line

            // Ensure required fields exist before using them
            if (!isset($validated['project_name'])) {
                throw new \Exception('Project name is missing');
            }

            DB::beginTransaction();

            $assignedData = json_decode($validated['assigned_data'], true);

            $technologies = $validated['technology'] ?? [];
            if (in_array('Other', $technologies) && !empty($validated['custom_technology'])) {
                $technologies[] = $validated['custom_technology'];
                $technologies = array_diff($technologies, ['Other']);
            }
            $technologies = array_values($technologies); // Reindex array
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON in assigned_data');
            }

            $projectData = [
                'project_name' => $validated['project_name'],
                'client_name' => $validated['client_name'],
                'project_description' => $validated['project_description'] ?? null,
                'location' => $validated['location'] ?? null,
                'project_type' => implode(', ', $validated['project_type']),
                'status' => $validated['status'] ?? 'on_boarding',
                'current_status' => $validated['current_status'] ?? null,
                'technology' => $technologies,
                'delay_reason' => $validated['delay_reason'] ?? null,
                'assigned_data' => $assignedData,
                'created_by' => auth()->id(),
                'site_heads' => $validated['site_heads'] ?? null,
                'estimated_hours' => $validated['estimated_hours'] ?? 0,
                'ui_ux_required' => $request->has('ui_ux_required') ? 1 : 0,
                'has_urls' => $request->has('has_urls') ? 1 : 0,
                'website_url' => $validated['website_url'] ?? null,
                'dashboard_url' => $validated['dashboard_url'] ?? null,
                'project_lead' => $validated['project_lead'] ?? null,
            ];

            // Only add dates if they are provided
            if (!empty($validated['project_startdate'])) {
                $projectData['project_startdate'] = $validated['project_startdate'];
            }
            if (isset($validated['project_days'])) {
                $projectData['project_days'] = $validated['project_days'];
            }

            if (!empty($validated['project_enddate'])) {
                $projectData['project_enddate'] = $validated['project_enddate'];
            }

            $project = Project::create($projectData);

            // Notify assigned employees
            $employeeIds = collect($assignedData)->pluck('employee_ids')->flatten()->unique()->toArray();
            if (!empty($employeeIds)) {
                $usersToNotify = \App\Models\User::whereHas('employee', function($q) use ($employeeIds) {
                    $q->whereIn('id', $employeeIds);
                })->get();

                foreach ($usersToNotify as $userToNotify) {
                    $userToNotify->notify(new \App\Notifications\ProjectAssignmentNotification($project));
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('projects.index'),
                    'message' => 'Project created successfully!'
                ]);
            }
            
            return redirect()->route('projects.index')->with('success', 'Project created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Project creation failed: '.$e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        if (Auth::user()->can('Edit Employee') || Auth::user()->type == 'company' || Auth::user()->isTester()) {
            $project = Project::findOrFail($id);
            $branches = Branch::all(); // Add this line
            $departments = Department::all();
            
            $uiUxDept = Department::where('name', 'UI-UX Designer')->first();
            $uiUxDeptId = $uiUxDept ? $uiUxDept->id : '';
            
            // Exclude terminated employees from selectable lists
            $employees = Employee::with('user')
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->get();
                
            $teamLeaders = Employee::with('user')
                ->where('is_team_leader', 1)
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->get();
            
            if (is_string($project->assigned_data)) {
                $decoded = json_decode($project->assigned_data, true);
                $project->assigned_data = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
            } elseif (!is_array($project->assigned_data)) {
                $project->assigned_data = [];
            }
            
            return view('projects.edit', compact('project', 'branches', 'departments', 'employees', 'uiUxDeptId', 'teamLeaders')); // Add branches
        }
        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'project_name' => 'required|string|max:255',
                'client_name' => 'required|string|max:255',
                'project_description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'project_type' => 'required|array',
                'project_type.*' => 'string',
                'project_startdate' => 'nullable|date',
                'project_days' => 'nullable|integer|min:1',
                'project_enddate' => 'nullable|date|after_or_equal:project_startdate',
                'estimated_hours' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:on_boarding,on_hold,assigned,in_progress,completed',
                'current_status' => 'nullable|string|max:255',
                'technology' => 'nullable|array',
                'technology.*' => 'string',
                'custom_technology' => 'nullable|string',
                'delay_reason' => 'nullable|string',
                'assigned_data' => 'required|json',
                'site_heads' => 'nullable|array',
                'site_heads.*' => 'exists:employees,id',
                'ui_ux_required' => 'nullable|boolean',
                'website_url' => 'nullable|url|max:255',
                'dashboard_url' => 'nullable|url|max:255',
                'project_lead' => 'nullable|exists:employees,id',
            ]);

            DB::beginTransaction();

            // Decode assigned data array
            $assignedData = json_decode($validated['assigned_data'], true);

            $technologies = $validated['technology'] ?? [];
            if (in_array('Other', $technologies) && !empty($validated['custom_technology'])) {
                $technologies[] = $validated['custom_technology'];
                $technologies = array_diff($technologies, ['Other']);
            }
            $technologies = array_values($technologies); // Reindex array

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON in assigned_data');
            }

            // Prepare update data
            $project = Project::findOrFail($id);
            
            $updateData = [
                'project_name' => $validated['project_name'],
                'client_name' => $validated['client_name'],
                'project_description' => $validated['project_description'] ?? null,
                'location' => $validated['location'] ?? null,
                'project_type' => implode(', ', $validated['project_type']),
                'status' => $validated['status'] ?? 'on_boarding',
                'current_status' => $validated['current_status'] ?? null,
                'technology' => $technologies,
                'delay_reason' => $validated['delay_reason'] ?? null,
                'assigned_data' => $assignedData,
                'site_heads' => $validated['site_heads'] ?? null,
                'estimated_hours' => $validated['estimated_hours'] ?? 0,
                'ui_ux_required' => $request->has('ui_ux_required') ? 1 : 0,
                'has_urls' => $request->has('has_urls') ? 1 : 0,
                'website_url' => $validated['website_url'] ?? null,
                'dashboard_url' => $validated['dashboard_url'] ?? null,
                'project_lead' => $validated['project_lead'] ?? null,
            ];

            // Only update dates if they are provided
            if (!empty($validated['project_startdate'])) {
                $updateData['project_startdate'] = $validated['project_startdate'];
            } else {
                $updateData['project_startdate'] = null;
            }
            if (isset($validated['project_days'])) {
                $updateData['project_days'] = $validated['project_days'];
            } else {
                $updateData['project_days'] = null;
            }

            if (!empty($validated['project_enddate'])) {
                $updateData['project_enddate'] = $validated['project_enddate'];
            } else {
                $updateData['project_enddate'] = null;
            }

            // Get old assignees before update
            $oldAssignees = [];
            if ($project->assigned_data) {
                $oldAssignees = collect(is_string($project->assigned_data) ? json_decode($project->assigned_data, true) : $project->assigned_data)
                                    ->pluck('employee_ids')->flatten()->unique()->toArray();
            }

            $project->update($updateData);

            // Get new assignees after update
            $newAssignees = collect($assignedData)->pluck('employee_ids')->flatten()->unique()->toArray();
            
            // Notify newly assigned employees
            $newlyAssignedIds = array_diff($newAssignees, $oldAssignees);
            if (!empty($newlyAssignedIds)) {
                $usersToNotify = \App\Models\User::whereHas('employee', function($q) use ($newlyAssignedIds) {
                    $q->whereIn('id', $newlyAssignedIds);
                })->get();

                foreach ($usersToNotify as $userToNotify) {
                    $userToNotify->notify(new \App\Notifications\ProjectAssignedNotification([
                        'project_id' => $project->id,
                        'message' => 'You have been assigned to a new project: ' . $project->project_name,
                        'project_name' => $project->project_name,
                        'url' => route('dashboard')
                    ]));
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('projects.index'),
                    'message' => 'Project updated successfully!'
                ]);
            }
            
            return redirect()->route('projects.index')->with('success', 'Project updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', collect($e->errors())->flatten()->toArray())
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Project update failed: '.$e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function getEmployeesByDepartment($id, Request $request)
    {
        try {
            $employees = Employee::where('department_id', $id)
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->whereHas('user', function($query) {
                    $query->where('type', 'employee');
                })
                ->orderBy('name')
                ->get(['id', 'name', 'department_id']);
                
            return response()->json($employees);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load employees',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return employees grouped by department for multiple departments.
     * Expected response shape:
     * [
     *   { department_id: 1, employees: [{id: 10, name: "A"}, ...] },
     *   ...
     * ]
     */
    public function getEmployeesByDepartments(Request $request)
    {
        try {
            $departmentIds = $request->input('department_ids', []);
            if (!is_array($departmentIds) || empty($departmentIds)) {
                return response()->json([]);
            }

            $employees = Employee::query()
                ->whereIn('department_id', $departmentIds)
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->whereHas('user', function ($query) {
                    $query->where('type', 'employee');
                })
                ->orderBy('name')
                ->get(['id', 'name', 'last_name', 'department_id'])
                ->map(function ($employee) {
                    $employee->name = $employee->full_name;
                    return $employee;
                })
                ->groupBy('department_id');

            $response = [];
            foreach ($departmentIds as $deptId) {
                $deptId = (int) $deptId;
                $response[] = [
                    'department_id' => $deptId,
                    'employees' => ($employees->get($deptId) ?? collect([]))->values(),
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load employees',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDepartmentsByBranch($branchId)
    {
        try {
            $departments = Department::where('branch_id', $branchId)
                ->orderBy('name')
                ->get(['id', 'name']);
                
            return response()->json($departments);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load departments',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

 
    public function destroy(Project $project)
    {
        if (!Auth::user()->can('Delete Employee') && Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403, 'Permission Denied');
        }

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }

    public function getDepartmentsById(Request $request)
{
    try {
        $departmentIds = $request->input('department_ids', []);
        
        if (empty($departmentIds)) {
            return response()->json([]);
        }

        $departments = Department::whereIn('id', $departmentIds)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($departments);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to load departments',
            'message' => $e->getMessage()
        ], 500);
    }
}
}