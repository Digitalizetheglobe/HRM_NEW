<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $designations = Designation::with('department.branch')
            ->where('company_id', Auth::id())
            ->latest()
            ->get();
        return view('designations.index', compact('designations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = \App\Models\Branch::where('company_id', Auth::id())
            ->orderBy('name')
            ->get();
        $departments = Department::where('company_id', Auth::id())
            ->orderBy('name')
            ->get();
        return view('designations.create', compact('branches', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
        ]);

        // Double check department belongs to this company
        $department = Department::where('id', $request->department_id)
            ->where('company_id', Auth::id())
            ->firstOrFail();

        Designation::create([
            'company_id' => Auth::id(),
            'department_id' => $department->id,
            'name' => $request->name,
        ]);

        return redirect()->route('designations.index')->with('success', 'Designation created successfully!');
    }

    public function edit(Designation $designation)
    {
        // Safety check
        if ($designation->company_id !== Auth::id()) {
            abort(403);
        }

        $branches = \App\Models\Branch::where('company_id', Auth::id())
            ->orderBy('name')
            ->get();
        $departments = Department::where('company_id', Auth::id())
            ->orderBy('name')
            ->get();
        return view('designations.edit', compact('designation', 'branches', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Designation $designation)
    {
        // Safety check
        if ($designation->company_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
        ]);

        // Double check department belongs to this company
        $department = Department::where('id', $request->department_id)
            ->where('company_id', Auth::id())
            ->firstOrFail();

        $designation->update([
            'department_id' => $department->id,
            'name' => $request->name,
        ]);

        return redirect()->route('designations.index')->with('success', 'Designation updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Designation $designation)
    {
        // Safety check
        if ($designation->company_id !== Auth::id()) {
            abort(403);
        }

        $designation->delete();

        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully!');
    }
}
