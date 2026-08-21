<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Resignation;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class ResignationController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('Manage Resignation')) {
            if(Auth::user()->type == 'employee') {
                $emp = Employee::where('user_id', \Auth::user()->id)->first();
                $resignations = Resignation::where('created_by', \Auth::user()->creatorId())
                    ->where('employee_id', $emp->id)
                    ->get();
            } else {
                $resignations = Resignation::where('created_by', \Auth::user()->creatorId())
                    ->with(['employee', 'approvedBy'])
                    ->get();
            }

            return view('resignation.index', compact('resignations'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create()
    {
        if(\Auth::user()->can('Create Resignation'))
        {
            // Get employee IDs that already have resignations
            $resignedEmployeeIds = Resignation::where('created_by', \Auth::user()->creatorId())
                ->pluck('employee_id')
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            // Get employees excluding those who already have resignations
            $employeesList = Employee::where('created_by', \Auth::user()->creatorId())
                ->whereNotIn('id', $resignedEmployeeIds)
                ->get();

            $employees = $employeesList->mapWithKeys(function ($employee) {
                return [$employee->id => $employee->full_name];
            });

            $employeeDojMap = $employeesList->pluck('company_doj', 'id');

            $employeeDoj = null;
            if (\Auth::user()->type == 'employee') {
                $employee = Employee::where('user_id', \Auth::user()->id)->first();
                $employeeDoj = $employee ? $employee->company_doj : null;
            }

            return view('resignation.create', compact('employees', 'employeeDojMap', 'employeeDoj'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('Create Resignation'))
        {

            $validator = \Validator::make(
                $request->all(), [

                                   'notice_date' => 'required',
                                   'resignation_date' => 'required|after_or_equal:notice_date',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $resignation = new Resignation();
            $user        = \Auth::user();
            if($user->type == 'employee')
            {
                $employee                 = Employee::where('user_id', $user->id)->first();
                $resignation->employee_id = $employee->id;
            }
            else
            {
                $resignation->employee_id = $request->employee_id;
            }

            // Check if employee already has a resignation
            $existingResignation = Resignation::where('created_by', \Auth::user()->creatorId())
                ->where('employee_id', $resignation->employee_id)
                ->first();

            if($existingResignation)
            {
                return redirect()->back()->with('error', __('This employee already has a resignation submitted.'));
            }
            $resignation->notice_date      = $request->notice_date;
            
            // Server-side calculation of resignation_date (Last Working Day) for employees
            if ($user->type == 'employee') {
                $employee = Employee::where('user_id', $user->id)->first();
                if ($employee && !empty($employee->company_doj)) {
                    $joiningDate = new \DateTime($employee->company_doj);
                    $resignDate = new \DateTime($request->notice_date);
                    
                    $oneYearLater = clone $joiningDate;
                    $oneYearLater->modify('+1 year');
                    
                    $daysToAdd = 30;
                    if ($resignDate >= $oneYearLater) {
                        $daysToAdd = 45;
                    }
                    
                    $lastWorkingDate = clone $resignDate;
                    $lastWorkingDate->modify('+' . $daysToAdd . ' days');
                    
                    $resignation->resignation_date = $lastWorkingDate->format('Y-m-d');
                } else {
                    $resignDate = new \DateTime($request->notice_date);
                    $resignDate->modify('+30 days');
                    $resignation->resignation_date = $resignDate->format('Y-m-d');
                }
            } else {
                $resignation->resignation_date = $request->resignation_date;
            }

            $resignation->description      = $request->description;
            $resignation->created_by       = \Auth::user()->creatorId();

            $resignation->save();

            $settings = Utility::settings();
            if (isset($settings['employee_resignation']) && $settings['employee_resignation'] == 1) {
                $employee = Employee::find($resignation->employee_id);
                if ($employee) {
                    $companyUser = User::find($employee->created_by);
                    if ($companyUser) {
                        $company_email = !empty($settings['company_email']) ? $settings['company_email'] : $companyUser->email;
                        
                        $uArr = [
                            'assign_user' => $employee->full_name,
                            'notice_date' => $resignation->notice_date,
                            'resignation_date' => $resignation->resignation_date,
                        ];
                        
                        Utility::sendEmailTemplate('employee_resignation', [$company_email], $uArr);
                    }
                }
            }

            return redirect()->route('resignation.index')->with('success', __('Resignation successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Resignation $resignation)
    {
        return redirect()->route('resignation.index');
    }

    public function edit(Resignation $resignation)
    {
        if(\Auth::user()->can('Edit Resignation'))
        {
            if($resignation->created_by == \Auth::user()->creatorId())
            {
                // Get employee IDs that already have resignations (excluding current resignation)
                $resignedEmployeeIds = Resignation::where('created_by', \Auth::user()->creatorId())
                    ->where('id', '!=', $resignation->id)
                    ->pluck('employee_id')
                    ->unique()
                    ->filter()
                    ->values()
                    ->toArray();

                // Get employees excluding those who already have resignations, but include current employee
                $employeesList = Employee::where('created_by', \Auth::user()->creatorId())
                    ->where(function($query) use ($resignedEmployeeIds, $resignation) {
                        $query->whereNotIn('id', $resignedEmployeeIds)
                              ->orWhere('id', $resignation->employee_id);
                    })
                    ->get();

                $employees = $employeesList->mapWithKeys(function ($employee) {
                    return [$employee->id => $employee->full_name];
                });

                $employeeDojMap = $employeesList->pluck('company_doj', 'id');

                $employeeDoj = null;
                if (\Auth::user()->type == 'employee') {
                    $employee = Employee::where('user_id', \Auth::user()->id)->first();
                    $employeeDoj = $employee ? $employee->company_doj : null;
                }

                return view('resignation.edit', compact('resignation', 'employees', 'employeeDojMap', 'employeeDoj'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Resignation $resignation)
    {
        if(\Auth::user()->can('Edit Resignation'))
        {
            if($resignation->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [

                                       'notice_date' => 'required',
                                       'resignation_date' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                if(\Auth::user()->type != 'employee')
                {
                    $newEmployeeId = $request->employee_id;
                    
                    // Check if the new employee already has a resignation (excluding current one)
                    $existingResignation = Resignation::where('created_by', \Auth::user()->creatorId())
                        ->where('employee_id', $newEmployeeId)
                        ->where('id', '!=', $resignation->id)
                        ->first();

                    if($existingResignation)
                    {
                        return redirect()->back()->with('error', __('This employee already has a resignation submitted.'));
                    }
                    
                    $resignation->employee_id = $newEmployeeId;
                }


                 $resignation->notice_date      = $request->notice_date;
                 
                 // Enforce server-side calculation for employee edit
                 if (\Auth::user()->type == 'employee') {
                     $employee = Employee::where('user_id', \Auth::user()->id)->first();
                     if ($employee && !empty($employee->company_doj)) {
                         $joiningDate = new \DateTime($employee->company_doj);
                         $resignDate = new \DateTime($request->notice_date);
                         
                         $oneYearLater = clone $joiningDate;
                         $oneYearLater->modify('+1 year');
                         
                         $daysToAdd = 30;
                         if ($resignDate >= $oneYearLater) {
                             $daysToAdd = 45;
                         }
                         
                         $lastWorkingDate = clone $resignDate;
                         $lastWorkingDate->modify('+' . $daysToAdd . ' days');
                         
                         $resignation->resignation_date = $lastWorkingDate->format('Y-m-d');
                     } else {
                         $resignDate = new \DateTime($request->notice_date);
                         $resignDate->modify('+30 days');
                         $resignation->resignation_date = $resignDate->format('Y-m-d');
                     }
                 } else {
                     $resignation->resignation_date = $request->resignation_date;
                 }
                 
                 $resignation->description      = $request->description;

                $resignation->save();

                return redirect()->route('resignation.index')->with('success', __('Resignation successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Resignation $resignation)
    {
        if(\Auth::user()->can('Delete Resignation'))
        {
            if($resignation->created_by == \Auth::user()->creatorId())
            {
                $resignation->delete();

                return redirect()->route('resignation.index')->with('success', __('Resignation successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function review($id)
    {
        if(\Auth::user()->can('Manage Resignation')) {
            $resignation = Resignation::with(['employee'])->findOrFail($id);
            return view('resignation.review', compact('resignation'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function approve(Request $request, $id)
    {
        if(\Auth::user()->can('Manage Resignation')) {
            $resignation = Resignation::findOrFail($id);
            
            $validator = \Validator::make($request->all(), [
                'notice_date' => 'required',
                'resignation_date' => 'required|after_or_equal:notice_date',
            ]);

            if($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update dates if changed
            $resignation->update([
                'notice_date' => $request->notice_date,
                'resignation_date' => $request->resignation_date,
                'status' => 'approved',
                'approved_by' => \Auth::id(),
                'approved_at' => now(),
            ]);

            // Send approval email to employee
            $settings = Utility::settings();
            if (isset($settings['employee_resignation']) && $settings['employee_resignation'] == 1) {
                $employee = Employee::find($resignation->employee_id);
                if ($employee && !empty($employee->email)) {
                    $uArr = [
                        'assign_user' => $employee->full_name,
                        'notice_date' => $resignation->notice_date,
                        'resignation_date' => $resignation->resignation_date,
                    ];
                    
                    Utility::sendEmailTemplate('employee_resignation', [$employee->email], $uArr);
                }
            }

            return redirect()->route('resignation.index')
                ->with('success', __('Resignation approved successfully.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }
}
