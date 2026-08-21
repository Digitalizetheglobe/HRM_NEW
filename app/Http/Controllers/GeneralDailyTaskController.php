<?php

namespace App\Http\Controllers;

use App\Models\GeneralDailyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralDailyTaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'nullable|string|max:255',
            'work_date' => 'required|date',
            'duration' => 'required|numeric|min:0',
            'task_title' => 'required|string|max:255',
            'task_description' => 'required|string',
        ]);

        $employeeId = Auth::user()->employee->id ?? Auth::id();

        GeneralDailyTask::create([
            'employee_id' => $employeeId,
            'project_name' => $request->project_name,
            'work_date' => $request->work_date,
            'duration' => $request->duration,
            'task_title' => $request->task_title,
            'task_description' => $request->task_description,
        ]);

        return redirect()->back()->with('success', 'General daily task saved successfully.');
    }

    public function update(Request $request, GeneralDailyTask $task)
    {
        $employeeId = Auth::user()->employee->id ?? 0;
        if (Auth::user()->type !== 'company' && !Auth::user()->isTester() && $task->employee_id != $employeeId) {
            abort(403);
        }

        $request->validate([
            'project_name' => 'nullable|string|max:255',
            'work_date' => 'required|date',
            'duration' => 'required|numeric|min:0',
            'task_title' => 'required|string|max:255',
            'task_description' => 'required|string',
        ]);

        $task->update([
            'project_name' => $request->project_name,
            'work_date' => $request->work_date,
            'duration' => $request->duration,
            'task_title' => $request->task_title,
            'task_description' => $request->task_description,
        ]);

        return redirect()->back()->with('success', 'General daily task updated successfully.');
    }

    public function destroy(GeneralDailyTask $task)
    {
        $employeeId = Auth::user()->employee->id ?? 0;
        if (Auth::user()->type !== 'company' && !Auth::user()->isTester() && $task->employee_id != $employeeId) {
            abort(403);
        }

        $task->delete();

        return redirect()->back()->with('success', 'General daily task deleted successfully.');
    }
}
