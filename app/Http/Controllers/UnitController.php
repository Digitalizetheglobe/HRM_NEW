<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UnitsImport;
use App\Models\Project;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::all(); // Get all projects for the dropdown
        
        // Filter units based on project if selected
        $unitsQuery = Unit::with('project');
        
        if ($request->has('project_id') && $request->project_id != '') {
            $unitsQuery->where('project_id', $request->project_id);
        }
        
        $units = $unitsQuery->get();
        
        return view('units.index', compact('units', 'projects'));
    }

    
    public function create()
{
    // Add explicit HR check
    if (Auth::user()->can('Create Employee') && !Auth::user()->hasRole('HR')) {
        $projects = Project::all(); 
        return view('units.create', compact('projects'));
    }
    return redirect()->back()->with('error', 'Permission denied.');
}


    public function import(Request $request)
{
    try {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,csv,xls,txt',
            'project_id' => 'required|integer|exists:projects,id'
        ]);

        \Log::info('Starting import for project: ' . $request->project_id);
        
        Excel::import(new UnitsImport($request->project_id), $request->file('import_file'));

        \Log::info('Import completed successfully');
        
        return back()->with('success', 'Units imported successfully!');
    } catch (\Exception $e) {
        \Log::error('Import failed: ' . $e->getMessage());
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}

    
    public function getUnitsByProject($projectId)
{
    $units = Unit::where('project_id', $projectId)->get();
    return response()->json(['units' => $units]);
}


public function destroy(Unit $unit)
{
    if (!Auth::user()->can('Delete Employee')) {
        abort(403, 'Permission Denied');
    }

    $unit->delete();
    return redirect()->route('units.index')->with('success', 'Unit deleted successfully!');
}

public function bulkDelete(Request $request)
    {
        try {
            if (!Auth::user()->can('Delete Employee')) {
                abort(403, 'Permission Denied');
            }
            
            $request->validate([
                'unit_ids' => 'required|string' // We'll receive a JSON string
            ]);
            
            // Decode the JSON string to array
            $unitIds = json_decode($request->unit_ids, true);
            
            if (!is_array($unitIds) || empty($unitIds)) {
                return redirect()->route('units.index')
                    ->with('error', 'No units selected for deletion.')
                    ->with('debug', 'Unit IDs: ' . $request->unit_ids);
            }
            
            Log::info('Attempting to delete units: ' . json_encode($unitIds));
            
            $deleteCount = Unit::whereIn('id', $unitIds)->delete();
            
            Log::info("Successfully deleted $deleteCount units.");
            
            return redirect()->route('units.index')
                ->with('success', "Successfully deleted $deleteCount units.");
                
        } catch (\Exception $e) {
            Log::error('Bulk delete failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('units.index')
                ->with('error', 'Delete failed: ' . $e->getMessage())
                ->with('debug', 'Unit IDs: ' . $request->unit_ids);
        }
    }

}