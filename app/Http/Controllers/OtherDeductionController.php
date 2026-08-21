<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OtherDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OtherDeductionController extends Controller
{
    public function index()
    {
        try {
            if (!\Auth::check()) {
                return redirect()->route('login');
            }

            if (\Auth::user()->type == 'company') {
                // Check if table exists, if not return empty collection
                try {
                    $deductions = OtherDeduction::with('employee')
                        ->where('created_by', \Auth::user()->creatorId())
                        ->orderBy('created_at', 'desc')
                        ->get();
                } catch (\Exception $e) {
                    // Table might not exist yet, return empty collection
                    Log::warning('Other deductions table might not exist: ' . $e->getMessage());
                    $deductions = collect([]);
                }
                
                // Get employees for dropdown
                $employees = Employee::where('created_by', \Auth::user()->creatorId())
                    ->get()
                    ->pluck('name', 'id');
                
                return view('other-deduction.index', compact('deductions', 'employees'));
            }
            return redirect()->route('dashboard')->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('OtherDeductionController index error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('dashboard')->with('error', __('Something went wrong. Please ensure migrations are run.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->type != 'company') {
            return response()->json(['error' => __('Permission denied.')], 403);
        }

        try {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required|exists:employees,id',
                    'month' => 'required|date_format:Y-m',
                    'amount' => 'required|numeric|min:0.01',
                    'remark' => 'nullable|string|max:1000',
                ],
                [
                    'employee_id.exists' => __('Selected employee does not exist'),
                    'month.date_format' => __('Invalid month format. Use YYYY-MM'),
                    'amount.min' => __('Amount must be greater than 0'),
                    'remark.max' => __('Remark must not exceed 1000 characters'),
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first()
                ], 422);
            }

            // Check if employee belongs to the company
            $employee = Employee::where('id', $request->employee_id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$employee) {
                return response()->json(['error' => __('Employee not found.')], 404);
            }

            // Parse month
            $month = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();

            $deduction = OtherDeduction::create([
                'employee_id' => $request->employee_id,
                'month' => $month,
                'amount' => $request->amount,
                'remark' => $request->remark,
                'created_by' => \Auth::user()->creatorId(),
            ]);

            return response()->json([
                'success' => __('Other deduction created successfully.'),
                'deduction' => $deduction->load('employee')
            ], 200);

        } catch (\Exception $e) {
            Log::error('OtherDeductionController store error: ' . $e->getMessage());
            return response()->json([
                'error' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            if (\Auth::user()->type != 'company') {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            $deduction = OtherDeduction::where('id', $id)
                ->where('created_by', \Auth::user()->creatorId())
                ->with('employee')
                ->first();

            if (!$deduction) {
                return response()->json(['error' => __('Other deduction not found.')], 404);
            }

            return response()->json([
                'deduction' => [
                    'id' => $deduction->id,
                    'employee_id' => $deduction->employee_id,
                    'month' => $deduction->month->format('Y-m'),
                    'amount' => $deduction->amount,
                    'remark' => $deduction->remark,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('OtherDeductionController edit error: ' . $e->getMessage());
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->type != 'company') {
            return response()->json(['error' => __('Permission denied.')], 403);
        }

        try {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required|exists:employees,id',
                    'month' => 'required|date_format:Y-m',
                    'amount' => 'required|numeric|min:0.01',
                    'remark' => 'nullable|string|max:1000',
                ],
                [
                    'employee_id.exists' => __('Selected employee does not exist'),
                    'month.date_format' => __('Invalid month format. Use YYYY-MM'),
                    'amount.min' => __('Amount must be greater than 0'),
                    'remark.max' => __('Remark must not exceed 1000 characters'),
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $deduction = OtherDeduction::where('id', $id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$deduction) {
                return response()->json(['error' => __('Other deduction not found.')], 404);
            }

            // Check if employee belongs to the company
            $employee = Employee::where('id', $request->employee_id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$employee) {
                return response()->json(['error' => __('Employee not found.')], 404);
            }

            // Parse month
            $month = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();

            $deduction->update([
                'employee_id' => $request->employee_id,
                'month' => $month,
                'amount' => $request->amount,
                'remark' => $request->remark,
            ]);

            return response()->json([
                'success' => __('Other deduction updated successfully.'),
                'deduction' => $deduction->load('employee')
            ], 200);

        } catch (\Exception $e) {
            Log::error('OtherDeductionController update error: ' . $e->getMessage());
            return response()->json([
                'error' => __('Something went wrong. Please try again.')
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (\Auth::user()->type != 'company') {
                return redirect()->route('dashboard')->with('error', __('Permission denied.'));
            }

            $deduction = OtherDeduction::where('id', $id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$deduction) {
                return redirect()->route('other-deduction.index')->with('error', __('Other deduction not found.'));
            }

            $deduction->delete();

            return redirect()->route('other-deduction.index')
                ->with('success', __('Other deduction deleted successfully.'));

        } catch (\Exception $e) {
            Log::error('OtherDeductionController destroy error: ' . $e->getMessage());
            return redirect()->route('other-deduction.index')->with('error', __('Something went wrong.'));
        }
    }

    public function getEmployees(Request $request)
    {
        try {
            if (\Auth::user()->type != 'company') {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            $search = $request->get('search', '');
            $search = trim($search);
            
            // Get all active employees (exclude terminated)
            $query = Employee::where('created_by', \Auth::user()->creatorId())
                ->whereNotIn('id', function($q) {
                    $q->select('employee_id')
                      ->from('terminations')
                      ->whereDate('termination_date', '<=', now());
                })
                ->whereNotIn('id', function($q) {
                    $q->select('employee_id')
                      ->from('resignations')
                      ->whereDate('resignation_date', '<=', now());
                });
            
            // Apply search filter if provided
            if (!empty($search)) {
                $searchLower = strtolower($search);
                $query->where(function($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%'])
                      ->orWhereRaw('LOWER(employee_id) LIKE ?', ['%' . $searchLower . '%'])
                      ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $searchLower . '%']);
                });
            }
            
            $employees = $query->select('id', 'name', 'employee_id', 'email')
                ->orderBy('name', 'asc')
                ->limit(100)
                ->get()
                ->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'text' => $employee->name . ' (' . ($employee->employee_id ?? 'N/A') . ')',
                        'name' => $employee->name,
                        'employee_id' => $employee->employee_id,
                    ];
                });

            return response()->json($employees);

        } catch (\Exception $e) {
            Log::error('OtherDeductionController getEmployees error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }
}






