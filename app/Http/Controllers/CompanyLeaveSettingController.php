<?php
// app/Http/Controllers/CompanyLeaveSettingController.php
namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\CompanyLeaveSetting;
use Illuminate\Http\Request;

class CompanyLeaveSettingController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Leave Settings')) {
            $leaveTypes = LeaveType::where('created_by', \Auth::user()->creatorId())->get();
            $settings = CompanyLeaveSetting::where('created_by', \Auth::user()->creatorId())
                ->with('leaveType')
                ->get();
                
            return view('leave.settings', compact('leaveTypes', 'settings'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    public function store(Request $request)
    {
        if (\Auth::user()->can('Manage Leave Settings')) {
            $request->validate([
                'leave_type_id' => 'required|exists:leave_types,id',
                'monthly_credit' => 'required|numeric|min:0'
            ]);
            
            CompanyLeaveSetting::updateOrCreate(
                [
                    'leave_type_id' => $request->leave_type_id,
                    'created_by' => \Auth::user()->creatorId()
                ],
                ['monthly_credit' => $request->monthly_credit]
            );
            
            return redirect()->back()->with('success', __('Settings saved successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}