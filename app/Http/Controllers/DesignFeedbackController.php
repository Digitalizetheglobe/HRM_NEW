<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DesignFeedback;
use App\Models\DesignFeedbackAttachment;
use App\Models\DesignActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DesignFeedbackController extends Controller
{
    public function store(Request $request, $versionId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'feedback_type' => 'required|string',
            'comment' => 'required|string',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'attachments.*' => 'nullable|file|mimes:png,jpg,jpeg,webp,pdf,docx,zip|max:20480', // 20MB limit
        ]);

        $feedback = DesignFeedback::create([
            'design_version_id' => $versionId,
            'title' => $request->title,
            'feedback_type' => $request->feedback_type,
            'comment' => $request->comment,
            'priority' => 'Normal',
            'due_date' => $request->due_date,
            'assigned_to' => $request->assigned_to,
            'submitted_by' => Auth::id(),
            'status' => $request->assigned_to ? 'Assigned' : 'Pending',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('design_attachments', $filename, 'public');

                // Also copy to public/storage so it's accessible even without a symlink
                $publicDir = public_path('storage/design_attachments');
                if (!file_exists($publicDir)) {
                    mkdir($publicDir, 0775, true);
                }
                copy(storage_path('app/public/' . $path), $publicDir . '/' . $filename);

                DesignFeedbackAttachment::create([
                    'feedback_id' => $feedback->id,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        DesignActivityLog::create([
            'project_id' => $feedback->designVersion->design->project_id,
            'design_id' => $feedback->designVersion->design_id,
            'design_version_id' => $versionId,
            'user_id' => Auth::id(),
            'activity_type' => 'Feedback Submitted',
            'description' => 'Feedback "' . $feedback->title . '" was submitted.',
        ]);

        $uiUxDept = \App\Models\Department::where('name', 'UI-UX Designer')->first();
        if ($uiUxDept) {
            $designers = \App\Models\Employee::where('department_id', $uiUxDept->id)->with('user')->get();
            $usersToNotify = $designers->pluck('user')->filter();
            if ($usersToNotify->count() > 0) {
                \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\ClientFeedbackNotification($feedback->designVersion->design->project));
            }
        }

        return back()->with('success', 'Feedback submitted successfully.');
    }

    public function update(Request $request, $id)
    {
        $feedback = DesignFeedback::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $feedback->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to ?? $feedback->assigned_to,
        ]);

        DesignActivityLog::create([
            'project_id' => $feedback->designVersion->design->project_id,
            'design_id' => $feedback->designVersion->design_id,
            'design_version_id' => $feedback->design_version_id,
            'user_id' => Auth::id(),
            'activity_type' => 'Feedback Updated',
            'description' => 'Feedback "' . $feedback->title . '" status changed to ' . $request->status . '.',
        ]);

        return back()->with('success', 'Feedback updated successfully.');
    }

    public function destroy($id)
    {
        $feedback = DesignFeedback::findOrFail($id);
        
        DesignActivityLog::create([
            'project_id' => $feedback->designVersion->design->project_id,
            'design_id' => $feedback->designVersion->design_id,
            'design_version_id' => $feedback->design_version_id,
            'user_id' => Auth::id(),
            'activity_type' => 'Feedback Deleted',
            'description' => 'Feedback "' . $feedback->title . '" was deleted.',
        ]);

        $feedback->delete();

        return back()->with('success', 'Feedback deleted successfully.');
    }
}
