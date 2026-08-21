<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectDocument;
use App\Models\Project;
use App\Models\ProjectActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentController extends Controller
{
    public function store(Request $request, $projectId)
    {
        $request->validate([
            'document' => 'required|file|max:20480', // 20MB limit
            'file_name' => 'nullable|string|max:255',
        ]);

        $project = Project::findOrFail($projectId);
        $file = $request->file('document');

        // Create filename
        $fileName = $request->file_name ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $storedFileName = time() . '_' . \Str::slug($fileName) . '.' . $extension;

        $path = $file->storeAs('project_documents', $storedFileName, 'public');

        $document = ProjectDocument::create([
            'project_id' => $projectId,
            'file_name' => $fileName,
            'file_path' => $path,
            'uploaded_by' => Auth::id() ?? null,
        ]);

        // Log activity if project activity model exists
        if(class_exists('\App\Models\ProjectActivity')) {
            ProjectActivity::create([
                'project_id' => $projectId,
                'user_id' => Auth::id() ?? null,
                'activity_type' => 'Document Uploaded',
                'description' => 'Document "' . $fileName . '" was uploaded.',
            ]);
        }

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy($id)
    {
        $document = ProjectDocument::findOrFail($id);
        
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        if(class_exists('\App\Models\ProjectActivity')) {
            ProjectActivity::create([
                'project_id' => $document->project_id,
                'user_id' => Auth::id() ?? null,
                'activity_type' => 'Document Deleted',
                'description' => 'Document "' . $document->file_name . '" was deleted.',
            ]);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    public function download($id)
    {
        $document = ProjectDocument::findOrFail($id);
        
        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        return back()->with('error', 'File not found.');
    }
}
