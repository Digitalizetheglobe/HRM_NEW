<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::where('company_id', Auth::id())->latest()->get();
        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        Branch::create([
            'company_id' => Auth::id(),
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        // Safety check
        if ($branch->company_id !== Auth::id()) {
            abort(403);
        }

        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        // Safety check
        if ($branch->company_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $branch->update([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        // Safety check
        if ($branch->company_id !== Auth::id()) {
            abort(403);
        }

        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully!');
    }
}
