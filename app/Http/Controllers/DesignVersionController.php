<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DesignVersion;
use App\Models\DesignVersionLink;
use App\Models\DesignActivityLog;
use Illuminate\Support\Facades\Auth;

class DesignVersionController extends Controller
{
    public function store(Request $request, $designId)
    {
        $request->validate([
            'version' => 'required|string|max:50',
            'links' => 'nullable|array',
            'links.*.title' => 'required_with:links|string|max:255',
            'links.*.url' => 'required_with:links|url',
            'client_visible' => 'boolean',
        ]);

        $version = DesignVersion::create([
            'design_id' => $designId,
            'version' => $request->version,
            'client_visible' => $request->client_visible ?? false,
            'created_by' => Auth::id(),
            'status' => 'Draft',
        ]);

        if ($request->has('links')) {
            foreach ($request->links as $link) {
                if(!empty($link['title']) && !empty($link['url'])) {
                    DesignVersionLink::create([
                        'design_version_id' => $version->id,
                        'title' => $link['title'],
                        'url' => $link['url'],
                    ]);
                }
            }
        }

        DesignActivityLog::create([
            'project_id' => $version->design->project_id,
            'design_id' => $designId,
            'design_version_id' => $version->id,
            'user_id' => Auth::id(),
            'activity_type' => 'Version Created',
            'description' => 'Version ' . $version->version . ' was uploaded.',
        ]);

        return back()->with('success', 'Design version added successfully.');
    }

    public function update(Request $request, $id)
    {
        $version = DesignVersion::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
        ]);

        $version->update([
            'status' => $request->status,
        ]);

        DesignActivityLog::create([
            'project_id' => $version->design->project_id,
            'design_id' => $version->design_id,
            'design_version_id' => $version->id,
            'user_id' => Auth::id(),
            'activity_type' => 'Status Changed',
            'description' => 'Status changed to ' . $request->status,
        ]);

        return back()->with('success', 'Version status updated.');
    }

    public function toggleVisibility($id)
    {
        $version = DesignVersion::findOrFail($id);
        $version->client_visible = !$version->client_visible;
        $version->save();

        return back()->with('success', 'Client visibility updated.');
    }

    public function approve(Request $request, $id)
    {
        $version = DesignVersion::findOrFail($id);
        $version->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        DesignActivityLog::create([
            'project_id' => $version->design->project_id,
            'design_id' => $version->design_id,
            'design_version_id' => $version->id,
            'user_id' => Auth::id(),
            'activity_type' => 'Version Approved',
            'description' => 'Version ' . $version->version . ' was approved.',
        ]);

        return back()->with('success', 'Design version approved.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejected_reason' => 'required|string',
        ]);

        $version = DesignVersion::findOrFail($id);
        $version->update([
            'status' => 'Rejected',
            'rejected_reason' => $request->rejected_reason,
        ]);

        DesignActivityLog::create([
            'project_id' => $version->design->project_id,
            'design_id' => $version->design_id,
            'design_version_id' => $version->id,
            'user_id' => Auth::id(),
            'activity_type' => 'Version Rejected',
            'description' => 'Version ' . $version->version . ' was rejected. Reason: ' . $request->rejected_reason,
        ]);

        return back()->with('success', 'Design version rejected.');
    }
    public function destroy($id)
    {
        $version = DesignVersion::findOrFail($id);
        
        DesignActivityLog::create([
            'project_id' => $version->design->project_id,
            'design_id' => $version->design_id,
            'user_id' => Auth::id(),
            'activity_type' => 'Version Deleted',
            'description' => 'Version ' . $version->version . ' was deleted.',
        ]);

        $version->delete();

        return back()->with('success', 'Design version deleted successfully.');
    }
}
