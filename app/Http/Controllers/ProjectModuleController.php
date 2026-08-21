<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectModuleController extends Controller
{
    public function store(Request $request, Project $project)
    {
        if (!Auth::user()->can('Create Employee') && Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        $request->validate([
            'module_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        ProjectModule::create([
            'project_id' => $project->id,
            'module_name' => $request->module_name,
            'description' => $request->description,
            'employee_ids' => $request->employee_ids,
            'progress' => 0,
            'status' => 'pending',
        ]);

        ProjectActivity::create([
            'project_id' => $project->id,
            'activity' => 'New module "' . $request->module_name . '" created by ' . Auth::user()->name,
            'activity_type' => 'Module Created',
            'created_by' => Auth::id(),
        ]);

        $project->recalculateProgress();

        return redirect()->back()->with('success', 'Project Module added successfully.');
    }

    public function edit(ProjectModule $module)
    {
        if (!Auth::user()->can('Create Employee') && Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }
        
        $project = $module->project;
        return view('projects.module_edit', compact('module', 'project'));
    }

    public function update(Request $request, ProjectModule $module)
    {
        if (!Auth::user()->can('Create Employee') && Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        if ($request->has('progress')) {
            $request->validate([
                'progress' => 'required|integer|min:0|max:100',
            ]);

            $module->update([
                'progress' => $request->progress,
                'status' => $request->progress == 100 ? 'completed' : 'in-progress'
            ]);

            ProjectActivity::create([
                'project_id' => $module->project_id,
                'activity' => 'Module "' . $module->module_name . '" progress updated to ' . $request->progress . '%',
                'activity_type' => 'Module Updated',
                'created_by' => Auth::id(),
            ]);
            
            $module->project->recalculateProgress();
            return redirect()->back()->with('success', 'Module progress updated successfully.');
        } else {
            $request->validate([
                'module_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'employee_ids' => 'nullable|array',
                'employee_ids.*' => 'exists:employees,id',
            ]);

            $module->update([
                'module_name' => $request->module_name,
                'description' => $request->description,
                'employee_ids' => $request->employee_ids,
            ]);

            ProjectActivity::create([
                'project_id' => $module->project_id,
                'activity' => 'Module "' . $module->module_name . '" was updated by ' . Auth::user()->name,
                'activity_type' => 'Module Updated',
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Module updated successfully.');
        }
    }

    public function destroy(ProjectModule $module)
    {
        if (!Auth::user()->can('Create Employee') && Auth::user()->type != 'company' && !Auth::user()->isTester()) {
            abort(403);
        }

        $projectName = $module->project->project_name;
        $moduleName = $module->module_name;
        $projectId = $module->project_id;
        
        $module->delete();

        ProjectActivity::create([
            'project_id' => $projectId,
            'activity' => 'Module "' . $moduleName . '" was deleted by ' . Auth::user()->name,
            'activity_type' => 'Module Deleted',
            'created_by' => Auth::id(),
        ]);

        $project = Project::find($projectId);
        if($project) {
            $project->recalculateProgress();
        }

        return redirect()->back()->with('success', 'Module deleted successfully.');
    }
}
