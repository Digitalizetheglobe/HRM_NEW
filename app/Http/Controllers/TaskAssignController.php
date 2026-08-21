<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskAssignController extends Controller
{
    // Display a listing of the assigned tasks
    public function index()
    {
        $tasks = Task::all(); // Fetch all assigned tasks
        return view('taskassign.index', compact('tasks'));
    }

    // Show form to create a new task assignment
    public function create()
    {
        return view('taskassign.create');
    }

    // Store a newly created task assignment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'assigned_to' => 'required|string|max:255', // Name or User ID of assignee
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'required|string|max:50',
        ]);

        Task::create($validated);
        return redirect()->route('taskassign.index');
    }

    // Show form to edit a task assignment
    public function edit(Task $task)
    {
        return view('taskassign.edit', compact('task'));
    }

    // Update the specified task assignment
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'assigned_to' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'required|string|max:50',
        ]);

        $task->update($validated);
        return redirect()->route('taskassign.index');
    }

    // Remove the specified task assignment
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('taskassign.index');
    }
}