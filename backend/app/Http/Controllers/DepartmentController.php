<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with('branch')
            ->where('company_id', Auth::id())
            ->latest()
            ->get();
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::where('company_id', Auth::id())->orderBy('name')->get();
        return view('departments.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
        ]);

        // Double check branch belongs to this company
        $branch = Branch::where('id', $request->branch_id)
            ->where('company_id', Auth::id())
            ->firstOrFail();

        Department::create([
            'company_id' => Auth::id(),
            'branch_id' => $branch->id,
            'name' => $request->name,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        // Safety check
        if ($department->company_id !== Auth::id()) {
            abort(403);
        }

        $branches = Branch::where('company_id', Auth::id())->orderBy('name')->get();
        return view('departments.edit', compact('department', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        // Safety check
        if ($department->company_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
        ]);

        // Double check branch belongs to this company
        $branch = Branch::where('id', $request->branch_id)
            ->where('company_id', Auth::id())
            ->firstOrFail();

        $department->update([
            'branch_id' => $branch->id,
            'name' => $request->name,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        // Safety check
        if ($department->company_id !== Auth::id()) {
            abort(403);
        }

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully!');
    }
}
