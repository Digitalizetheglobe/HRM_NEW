<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    public function index()
    {
        if (\Auth::user()->type == 'employee') {
            $notices = Notice::whereDate('notice_startdate', '<=', now())
                             ->whereDate('notice_enddate', '>=', now())
                             ->latest()
                             ->get();
        } else {
            $notices = Notice::latest()->get();
        }
        
        return view('notice.index', compact('notices'));
    }

    // public function create()
    // {
    //     return view('notice.create');
    // }

    public function create()
    {
        if (\Auth::user()->type == 'company') {
            return view('notice.create');
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'notice_startdate' => 'required|date|after_or_equal:today',
            'notice_enddate' => 'required|date|after_or_equal:notice_startdate',
        ]);

        Notice::create([
            'title' => $request->title,
            'description' => $request->description,
            'notice_startdate' => $request->notice_startdate,
            'notice_enddate' => $request->notice_enddate,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('notices.index')->with('success', 'Notice created successfully.');
    }

    public function show(Notice $notice)
    {
        return view('notice.show', compact('notice'));
    }

    public function edit(Notice $notice)
    {
        return view('notice.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
{
    // Validate the request
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'notice_startdate' => 'required|date',
        'notice_enddate' => 'required|date|after_or_equal:notice_startdate',
    ]);

    // Check if 'status' field exists before updating
    $data = [
        'title' => $request->title,
        'description' => $request->description,
        'notice_startdate' => \Carbon\Carbon::parse($request->notice_startdate)->format('Y-m-d'),
        'notice_enddate' => \Carbon\Carbon::parse($request->notice_enddate)->format('Y-m-d'),
    ];

   

    // Update the notice
    $notice->update($data);

    return redirect()->route('notices.index')->with('success', 'Notice updated successfully.');
}


public function destroy(Notice $notice)
    {
        $notice->delete();

        return redirect()->route('notices.index')->with('success', __('Notice deleted successfully.'));
    }

}
