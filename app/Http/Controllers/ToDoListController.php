<?php   
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToDoList;
use Illuminate\Support\Facades\Auth;

class ToDoListController extends Controller
{
    /**
     * Display a listing of the to-do list items.
     */
    public function index()
{
    // Get tasks ordered by whether they are today, completed status, and due date
    $tasks = ToDoList::where('user_id', Auth::user()->id)
        ->orderByRaw('
            (DATE(expires_at) = CURDATE()) DESC,  -- Show today’s tasks first
            is_completed ASC,                     -- Show pending tasks before completed tasks
            expires_at ASC                        -- Order by due date
        ')
        ->get();

    return view('todo.index', compact('tasks'));
}

    

  

    public function create()
    {
        // Allow all authenticated users to create a Todo
        return view('todo.create');
    }


    /**
     * Store a new to-do list item in the database.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'task'        => 'required|string|max:255',
            'priority'    => 'required|in:low,medium,high',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'description' => 'nullable|string',
            'is_completed'=> 'required|boolean',
        ]);

        // Create new ToDo List item and associate with the logged-in user
        ToDoList::create([
            'user_id'     => Auth::id(),  // Logged-in user's ID
            'task'        => $validatedData['task'],
            'priority'    => $validatedData['priority'],
            'start_date'  => $validatedData['start_date'],
            'end_date'    => $validatedData['end_date'],
            'description' => $validatedData['description'],
            'is_completed'=> $validatedData['is_completed'],
        ]);

        return redirect()->back()->with('success', __('To-Do item created successfully.'));
    }


    /**
     * Display the specified to-do list item.
     */
    public function show($id)
    {
        $todo = ToDoList::findOrFail($id);

        if ($todo->user_id === Auth::user()->id) {
            return view('todo.show', compact('todo'));
        } else {
            return response()->json(['error' => __('You do not have permission to view this task.')], 403);
        }
    }

    /**
     * Show the form for editing a to-do list item.
     */
    public function edit($id)
    {
        // Find the task
        $todo = ToDoList::findOrFail($id);

        // Ensure the logged-in user owns the task
        if ($todo->user_id === Auth::user()->id) {
            return view('todo.edit', compact('todo'));
        } else {
            return response()->json(['error' => __('You do not have permission to edit this task.')], 403);
        }
    }



    /**
     * Update the specified to-do list item in the database.
     */
    public function update(Request $request, $id)
    {
        $todo = ToDoList::findOrFail($id);
    
        // Ensure the logged-in user owns the task
        if ($todo->user_id === Auth::user()->id) {
            // Validation
            $request->validate([
                'task' => 'required|string|max:255',
                'priority' => 'nullable|in:high,medium,low',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'description' => 'nullable|string',
                'is_completed' => 'nullable|boolean',
            ]);
    
            // Update task
            $todo->update($request->only(['task', 'priority', 'start_date', 'end_date', 'description', 'is_completed']));
    
            return redirect()->back()->with('success', __('Task updated successfully.'));
        } else {
            return response()->json(['error' => __('You do not have permission to update this task.')], 403);
        }
    }
    

    /**
     * Remove the specified to-do list item from the database.
     */
    public function destroy(ToDoList $todo)
    {
        $todo->delete();
    
        return redirect()->back()->with('success', __('Task successfully deleted.'));
    }
    
}
