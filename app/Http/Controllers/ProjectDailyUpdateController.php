<?php

namespace App\Http\Controllers;

use App\Models\ProjectDailyUpdate;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectDailyUpdateController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'module_id' => 'required|exists:project_modules,id',
            'work_date' => 'required|date',
            'work_done' => 'required|string',
            'hours_worked' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
        ]);

        $employeeId = Auth::user()->employee->id ?? Auth::id(); // Handle depending on architecture

        $update = ProjectDailyUpdate::create([
            'project_id' => $project->id,
            'employee_id' => $employeeId,
            'module_id' => $request->module_id,
            'work_date' => $request->work_date,
            'work_done' => $request->work_done,
            'hours_worked' => $request->hours_worked,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'activity' => 'New daily update submitted by ' . Auth::user()->name,
            'activity_type' => 'Update Submitted',
            'created_by' => Auth::id(),
        ]);

        $testerDept = \App\Models\Department::where('name', 'Tester')->first();
        if ($testerDept) {
            $testers = \App\Models\Employee::where('department_id', $testerDept->id)->with('user')->get();
            $usersToNotify = $testers->pluck('user')->filter();
            if ($usersToNotify->count() > 0) {
                \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\ProjectReportSubmittedNotification($project));
            }
        }

        return redirect()->back()->with('success', 'Daily update submitted successfully and is pending approval.');
    }

    public function approve(ProjectDailyUpdate $update)
    {
        if (!Auth::user()->can('Create Employee') && Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        $update->update(['status' => 'approved']);

        // Update Project Actual Hours

        $project = $update->project;
        $project->actual_hours += $update->hours_worked;
        $project->save();
        $project->recalculateProgress();

        ProjectActivity::create([
            'project_id' => $project->id,
            'activity' => 'Daily update for ' . $update->work_date . ' approved by ' . Auth::user()->name,
            'activity_type' => 'Update Approved',
            'created_by' => Auth::id(),
        ]);

        if ($update->employee && $update->employee->user) {
            $update->employee->user->notify(new \App\Notifications\ProjectReportApprovalNotification($project));
        }

        return redirect()->back()->with('success', 'Update approved and project progress updated.');
    }

    public function reject(ProjectDailyUpdate $update)
    {
        if (!Auth::user()->can('Create Employee') && Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        $update->update(['status' => 'rejected']);

        ProjectActivity::create([
            'project_id' => $update->project_id,
            'activity' => 'Daily update for ' . $update->work_date . ' was rejected.',
            'activity_type' => 'Update Rejected',
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Update rejected.');
    }
    public function destroy(ProjectDailyUpdate $update)
    {
        if (Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        $update->delete();

        return redirect()->back()->with('success', 'Daily update deleted successfully.');
    }
}
