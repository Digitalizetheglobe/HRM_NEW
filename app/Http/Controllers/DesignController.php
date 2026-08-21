<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Design;
use App\Models\Project;
use App\Models\DesignActivityLog;
use Illuminate\Support\Facades\Auth;

class DesignController extends Controller
{
    public function index(Request $request)
    {
        $project_id = $request->get('project_id');
        if($project_id) {
            $project = Project::findOrFail($project_id);
            $designs = Design::where('project_id', $project_id)->with('latestVersion')->get();
            return view('designs.index', compact('project', 'designs'));
        }
        
        $designs = Design::with(['project', 'latestVersion'])->get();
        return view('designs.index', compact('designs'));
    }

    public function create(Request $request)
    {
        $project_id = $request->get('project_id');
        $user = Auth::user();

        if ($user->type === 'employee') {
            // Only show projects assigned to this employee
            $employeeId = $user->employee->id ?? null;
            $projects = $employeeId
                ? Project::all()->filter(function ($project) use ($employeeId) {
                    foreach ($project->assigned_data ?? [] as $assignment) {
                        if (isset($assignment['employee_ids']) && in_array($employeeId, (array)$assignment['employee_ids'])) {
                            return true;
                        }
                    }
                    return false;
                })->values()
                : collect();
        } else {
            $projects = Project::all();
        }

        return view('designs.create', compact('project_id', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $design = Design::create([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'design_type' => 'General',
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        DesignActivityLog::create([
            'project_id' => $design->project_id,
            'design_id' => $design->id,
            'user_id' => Auth::id(),
            'activity_type' => 'Design Created',
            'description' => 'Design "' . $design->title . '" was created.',
        ]);

        return redirect()->route('designs.show', $design->id)->with('success', 'Design created successfully.');
    }

    public function show($id)
    {
        $design = Design::with(['versions.links', 'versions.feedbacks', 'project', 'creator'])->findOrFail($id);
        return view('designs.show', compact('design'));
    }

    public function edit($id)
    {
        $design = Design::findOrFail($id);
        $user = Auth::user();

        if ($user->type === 'employee') {
            $employeeId = $user->employee->id ?? null;
            $projects = $employeeId
                ? Project::all()->filter(function ($project) use ($employeeId) {
                    foreach ($project->assigned_data ?? [] as $assignment) {
                        if (isset($assignment['employee_ids']) && in_array($employeeId, (array)$assignment['employee_ids'])) {
                            return true;
                        }
                    }
                    return false;
                })->values()
                : collect();
        } else {
            $projects = Project::all();
        }

        return view('designs.edit', compact('design', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $design = Design::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $design->update([
            'title' => $request->title,
            'design_type' => 'General',
            'description' => $request->description,
        ]);

        return redirect()->route('designs.show', $design->id)->with('success', 'Design updated successfully.');
    }

    public function destroy($id)
    {
        $design = Design::findOrFail($id);
        $project_id = $design->project_id;
        $design->delete();

        return redirect()->route('projects.show', $project_id)->with('success', 'Design deleted successfully.');
    }
}
