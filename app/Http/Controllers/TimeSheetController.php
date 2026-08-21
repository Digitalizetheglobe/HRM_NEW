<?php

namespace App\Http\Controllers;
use App\Models\Unit;
use App\Models\Employee;
use App\Models\TimeSheet;
use App\Models\Project;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimeSheetExport;
use App\Mail\FollowUpReminder;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TimeSheetController extends Controller
{
    /**
     * Subquery to exclude terminated employees (employees existing in `terminations` table).
     */
    protected function terminatedEmployeeSubquery($query)
    {
        return $query->select('employee_id')->from('terminations');
    }
    
    public function index(Request $request)
    {
        $query = TimeSheet::with(['employee.employee']);

        if (Auth::user()->type == 'employee') {
            $userId = Auth::id();
            $query->where('employee_id', $userId);
        }   

        // Rest of your filter code remains the same...
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Get results ordered by latest first
        $timeSheets = $query->latest()->get();

        // Store filtered timesheets in session for export
        session(['export_timesheets' => $timeSheets]);

        $employeesList = Employee::pluck('name', 'id')->prepend(__('Select Employee'), '');
        
        if ($request->ajax()) {
            return view('timesheet.partials.table', compact('timeSheets'));
        }

        return view('timeSheet.index', compact('timeSheets', 'employeesList'));
    }

    public function create()
    {
        if (Auth::user()->can('Create TimeSheet')) {
            // Exclude terminated employees from dropdowns
            $employees = Employee::query()
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->pluck('name', 'user_id')
                ->prepend('Select Employee', '');

            return view('timeSheet.create', compact('employees'));
        }

        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function store(Request $request)
    {
        \Log::info('Store method called');
        \Log::info($request->all());

        if (!Auth::user()->can('Create TimeSheet')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => Auth::user()->type != 'employee' ? 'required|exists:users,id' : 'nullable',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0|max:24',
            'remark' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $timeSheetData = [
                'employee_id' => Auth::user()->type == 'employee' ? Auth::id() : $request->employee_id,
                'date' => $request->date,
                'hours' => $request->hours,
                'remark' => $request->remark,
                'created_by' => Auth::user()->creatorId(),
            ];

            $timeSheet = TimeSheet::create($timeSheetData);

            return redirect()->route('timesheet.index')
                ->with('success', __('Timesheet created successfully'));

        } catch (\Exception $e) {
            \Log::error('Timesheet creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating timesheet: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function edit(TimeSheet $timeSheet)
    {
        if (Auth::user()->can('Edit TimeSheet')) {
            // Exclude terminated employees from dropdowns
            $employees = Employee::query()
                ->whereNotIn('id', function ($q) {
                    $this->terminatedEmployeeSubquery($q);
                })
                ->pluck('name', 'user_id')
                ->toArray();
            
            return view('timeSheet.edit', compact(
                'timeSheet',
                'employees'
            ));
        }
        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function update(Request $request, $id)
    {
        \Log::info('Update request data:', $request->all());

        if (!Auth::user()->can('Edit TimeSheet')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $timeSheet = TimeSheet::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'employee_id' => Auth::user()->type != 'employee' ? 'required|exists:users,id' : 'nullable',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0|max:24',
            'remark' => 'required|string',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->all());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed. Please check your inputs: ' . implode(' ', $validator->errors()->all()));
        }

        try {
            $timeSheetData = [
                'date' => $request->date,
                'hours' => $request->hours,
                'remark' => $request->remark,
            ];

            if (Auth::user()->type != 'employee' && $request->filled('employee_id')) {
                $timeSheetData['employee_id'] = $request->employee_id;
            }

            $timeSheet->update($timeSheetData);
            
            return redirect()->route('timesheet.index')
                ->with('success', __('Timesheet updated successfully'));

        } catch (\Exception $e) {
            \Log::error('Timesheet update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating timesheet: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('Delete TimeSheet')) {
            $timeSheet = TimeSheet::findOrFail($id);
            $timeSheet->delete();
            return redirect()->route('timesheet.index')->with('success', 'Timesheet deleted successfully!');
        }

        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function show(TimeSheet $timeSheet)
    {
        try {
            // Load relationships with error handling
            $timeSheet->load([
                'employee' => function($query) {
                    $query->select('id', 'name');
                }
            ]);
            
            return view('timeSheet.show', compact('timeSheet'));
            
        } catch (\Exception $e) {
            \Log::error("Error showing timesheet: " . $e->getMessage());
            abort(500, 'Error loading timesheet details');
        }
    }

    public function export(Request $request)
    {
        // Check if we have filtered timesheets in session
        if (session()->has('export_timesheets')) {
            $timeSheets = session('export_timesheets');
            
            // Clear the session after use
            session()->forget('export_timesheets');
        } else {
            // Fallback - get all visible timesheets if no filters were applied
            $query = TimeSheet::with(['employee']);

            if (Auth::user()->type == 'employee') {
                $userId = Auth::id();
                $query->where('employee_id', $userId);
            }

            $timeSheets = $query->latest()->get();
        }

        // Generate file name with timestamp
        $fileName = 'enquiries_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new TimeSheetExport($timeSheets), $fileName);
    }
        


    /**
     * Send follow-up reminder email immediately
     * 
     * @param TimeSheet $timeSheet
     * @return bool
     */
    private function sendFollowUpEmail(TimeSheet $timeSheet)
    {
        try {
            Log::info("Attempting to send follow-up email", [
                'timesheet_id' => $timeSheet->id,
                'employee_id' => $timeSheet->employee_id
            ]);
            
            // Refresh to get latest data
            $timeSheet->refresh();
            
            // Load relationships
            $timeSheet->load(['employee']);
            
            // Ensure feedback_information is accessible as array
            if (!empty($timeSheet->feedback_information) && is_string($timeSheet->feedback_information)) {
                // If it's still a string, it means the cast didn't work, so decode it manually
                $decoded = json_decode($timeSheet->feedback_information, true);
                $timeSheet->setAttribute('feedback_information', $decoded);
            }
            
            // Determine which employee to send email to
            $employeeToNotify = null;
            $employeeEmail = null;
            $userForEmail = null;
            
            // Use the original creator
            if ($timeSheet->employee_id) {
                $originalUser = User::find($timeSheet->employee_id);
                Log::info("Checking original user", [
                    'employee_id' => $timeSheet->employee_id,
                    'user_found' => $originalUser ? 'yes' : 'no'
                ]);
                
                if ($originalUser) {
                    $employeeToNotify = Employee::where('user_id', $originalUser->id)->first();
                    $userForEmail = $originalUser;
                    Log::info("Original employee found", [
                        'employee_id' => $employeeToNotify ? $employeeToNotify->id : null,
                        'email' => $employeeToNotify ? ($employeeToNotify->email ?? 'null') : null
                    ]);
                }
            }
            
            // Get employee email - try employee email first, then user email as fallback
            if ($employeeToNotify) {
                $employeeEmail = $employeeToNotify->email;
                
                // If employee email is empty, try to get from user
                if (empty($employeeEmail) && $userForEmail && !empty($userForEmail->email)) {
                    $employeeEmail = $userForEmail->email;
                    Log::info("Using user email as fallback", ['email' => $employeeEmail]);
                }
            } elseif ($userForEmail && !empty($userForEmail->email)) {
                // If no employee record but we have a user, use user email
                $employeeEmail = $userForEmail->email;
                Log::info("Using user email directly (no employee record)", ['email' => $employeeEmail]);
            }
            
            if (empty($employeeEmail)) {
                Log::warning("Cannot send follow-up email: No valid employee email found", [
                    'timesheet_id' => $timeSheet->id,
                    'employee_id' => $timeSheet->employee_id,
                    'employee_found' => $employeeToNotify ? 'yes' : 'no',
                    'employee_email' => $employeeToNotify ? ($employeeToNotify->email ?? 'empty') : 'no employee'
                ]);
                return false;
            }
            
            // Get the last remark
            $lastRemark = $this->getLastRemark($timeSheet);
            
            // Configure mail settings from database (IMPORTANT!)
            // Get the company/user ID - use current logged in user's creator ID or default to 1
            $companyId = Auth::check() ? Auth::user()->creatorId() : 1;
            $mailSettings = Utility::getSMTPDetails($companyId);
            
            // Normalize mail driver to lowercase (Laravel expects 'smtp' not 'SMTP')
            if (!empty($mailSettings['mail_driver'])) {
                $mailDriver = strtolower(trim($mailSettings['mail_driver']));
                // Ensure it's a valid mailer name
                if ($mailDriver === 'smtp' || $mailDriver === 'mail' || $mailDriver === 'sendmail') {
                    config(['mail.default' => $mailDriver]);
                } else {
                    // Default to smtp if invalid value
                    config(['mail.default' => 'smtp']);
                }
            } else {
                // Default to smtp if not set
                config(['mail.default' => 'smtp']);
            }
            
            Log::info("Sending email now", [
                'to' => $employeeEmail,
                'company_id' => $companyId,
                'mail_driver' => config('mail.default'),
                'follow_up_date' => $timeSheet->follow_up_date,
                'last_remark' => substr($lastRemark, 0, 50) . '...'
            ]);
            
            // Send email
            Mail::to($employeeEmail)->send(new FollowUpReminder($timeSheet, $lastRemark));
            
            Log::info("Follow-up reminder sent immediately - SUCCESS", [
                'timesheet_id' => $timeSheet->id,
                'employee_email' => $employeeEmail,
                'follow_up_date' => $timeSheet->follow_up_date
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error sending immediate follow-up reminder", [
                'timesheet_id' => $timeSheet->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception - we don't want to break the save/update process
            return false;
        }
    }
    
    /**
     * Get the last remark for a timesheet
     * Priority: Last feedback > Executive remark
     * 
     * @param TimeSheet $timeSheet
     * @return string
     */
    private function getLastRemark(TimeSheet $timeSheet)
    {
        // Check if there are feedbacks
        if (!empty($timeSheet->feedback_information)) {
            // feedback_information is already cast as array in the model, but check to be safe
            $feedbacks = [];
            if (is_array($timeSheet->feedback_information)) {
                $feedbacks = $timeSheet->feedback_information;
            } elseif (is_string($timeSheet->feedback_information)) {
                $feedbacks = json_decode($timeSheet->feedback_information, true);
            }
            
            if (is_array($feedbacks) && !empty($feedbacks)) {
                // Get the last feedback (most recent)
                $lastFeedback = end($feedbacks);
                if (isset($lastFeedback['description']) && !empty(trim($lastFeedback['description']))) {
                    return trim($lastFeedback['description']);
                }
            }
        }
        
        // Fallback to executive remark
        if (!empty($timeSheet->executive_remark)) {
            return trim($timeSheet->executive_remark);
        }
        
        return 'No remarks available.';
    }

}




