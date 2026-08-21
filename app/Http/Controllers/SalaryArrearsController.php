<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryArrears;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SalaryArrearsController extends Controller
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
                    $arrears = SalaryArrears::with('employee')
                        ->where('created_by', \Auth::user()->creatorId())
                        ->orderBy('created_at', 'desc')
                        ->get();
                } catch (\Exception $e) {
                    // Table might not exist yet, return empty collection
                    Log::warning('Salary arrears table might not exist: ' . $e->getMessage());
                    $arrears = collect([]);
                }
                
                // Get employees for dropdown (like assets)
                $employees = Employee::where('created_by', \Auth::user()->creatorId())
                    ->get()
                    ->pluck('name', 'id');
                
                return view('salary-arrears.index', compact('arrears', 'employees'));
            }
            return redirect()->route('dashboard')->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('SalaryArrearsController index error: ' . $e->getMessage());
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
                    'arrears_month' => 'required|date_format:Y-m',
                    'payment_month' => 'required|date_format:Y-m',
                    'amount' => 'required|numeric|min:0.01',
                ],
                [
                    'employee_id.exists' => __('Selected employee does not exist'),
                    'arrears_month.date_format' => __('Invalid arrears month format. Use YYYY-MM'),
                    'payment_month.date_format' => __('Invalid payment month format. Use YYYY-MM'),
                    'amount.min' => __('Amount must be greater than 0'),
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

            // Parse months
            $arrearsMonth = Carbon::createFromFormat('Y-m', $request->arrears_month)->startOfMonth();
            $paymentMonth = Carbon::createFromFormat('Y-m', $request->payment_month)->startOfMonth();

            // Validate that payment month is not before arrears month
            if ($paymentMonth->lt($arrearsMonth)) {
                return response()->json([
                    'error' => __('Payment month cannot be before arrears month.')
                ], 422);
            }

            $arrears = SalaryArrears::create([
                'employee_id' => $request->employee_id,
                'arrears_month' => $arrearsMonth,
                'payment_month' => $paymentMonth,
                'amount' => $request->amount,
                'created_by' => \Auth::user()->creatorId(),
            ]);

            return response()->json([
                'success' => __('Salary arrears created successfully.'),
                'arrears' => $arrears->load('employee')
            ], 200);

        } catch (\Exception $e) {
            Log::error('SalaryArrearsController store error: ' . $e->getMessage());
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

            $arrears = SalaryArrears::where('id', $id)
                ->where('created_by', \Auth::user()->creatorId())
                ->with('employee')
                ->first();

            if (!$arrears) {
                return response()->json(['error' => __('Salary arrears not found.')], 404);
            }

            return response()->json([
                'arrears' => [
                    'id' => $arrears->id,
                    'employee_id' => $arrears->employee_id,
                    'arrears_month' => $arrears->arrears_month->format('Y-m'),
                    'payment_month' => $arrears->payment_month->format('Y-m'),
                    'amount' => $arrears->amount,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('SalaryArrearsController edit error: ' . $e->getMessage());
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
                    'arrears_month' => 'required|date_format:Y-m',
                    'payment_month' => 'required|date_format:Y-m',
                    'amount' => 'required|numeric|min:0.01',
                ],
                [
                    'employee_id.exists' => __('Selected employee does not exist'),
                    'arrears_month.date_format' => __('Invalid arrears month format. Use YYYY-MM'),
                    'payment_month.date_format' => __('Invalid payment month format. Use YYYY-MM'),
                    'amount.min' => __('Amount must be greater than 0'),
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $arrears = SalaryArrears::where('id', $id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$arrears) {
                return response()->json(['error' => __('Salary arrears not found.')], 404);
            }

            // Check if employee belongs to the company
            $employee = Employee::where('id', $request->employee_id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$employee) {
                return response()->json(['error' => __('Employee not found.')], 404);
            }

            // Parse months
            $arrearsMonth = Carbon::createFromFormat('Y-m', $request->arrears_month)->startOfMonth();
            $paymentMonth = Carbon::createFromFormat('Y-m', $request->payment_month)->startOfMonth();

            // Validate that payment month is not before arrears month
            if ($paymentMonth->lt($arrearsMonth)) {
                return response()->json([
                    'error' => __('Payment month cannot be before arrears month.')
                ], 422);
            }

            $arrears->update([
                'employee_id' => $request->employee_id,
                'arrears_month' => $arrearsMonth,
                'payment_month' => $paymentMonth,
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => __('Salary arrears updated successfully.'),
                'arrears' => $arrears->load('employee')
            ], 200);

        } catch (\Exception $e) {
            Log::error('SalaryArrearsController update error: ' . $e->getMessage());
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

            $arrears = SalaryArrears::where('id', $id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$arrears) {
                return redirect()->route('salary-arrears.index')->with('error', __('Salary arrears not found.'));
            }

            $arrears->delete();

            return redirect()->route('salary-arrears.index')
                ->with('success', __('Salary arrears deleted successfully.'));

        } catch (\Exception $e) {
            Log::error('SalaryArrearsController destroy error: ' . $e->getMessage());
            return redirect()->route('salary-arrears.index')->with('error', __('Something went wrong.'));
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
                ->limit(100) // Increased limit to show more employees
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
            Log::error('SalaryArrearsController getEmployees error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }
}
