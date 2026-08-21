<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PetrolAllowance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PetrolAllowanceController extends Controller
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
                    $petrolAllowances = PetrolAllowance::with('employee')
                        ->where('created_by', \Auth::user()->creatorId())
                        ->orderBy('created_at', 'desc')
                        ->get();
                } catch (\Exception $e) {
                    // Table might not exist yet, return empty collection
                    Log::warning('Petrol allowances table might not exist: ' . $e->getMessage());
                    $petrolAllowances = collect([]);
                }
                
                // Get employees for dropdown (like assets)
                $employees = Employee::where('created_by', \Auth::user()->creatorId())
                    ->get()
                    ->pluck('name', 'id');
                
                return view('petrol-allowance.index', compact('petrolAllowances', 'employees'));
            }
            return redirect()->route('dashboard')->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('PetrolAllowanceController index error: ' . $e->getMessage());
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
                    'months' => 'required|array|min:1',
                    'months.*' => 'required|date_format:Y-m',
                    'vehicle_type' => 'required|in:two-wheeler,four-wheeler',
                    'amount' => 'required|numeric|min:0.01',
                ],
                [
                    'employee_id.exists' => __('Selected employee does not exist'),
                    'months.required' => __('Please select at least one month'),
                    'months.array' => __('Invalid months format'),
                    'months.*.date_format' => __('Invalid month format. Use YYYY-MM'),
                    'vehicle_type.in' => __('Invalid vehicle type'),
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

            $createdRecords = [];
            
            // Create a record for each selected month
            foreach ($request->months as $month) {
                $monthDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                
                // Check if record already exists for this employee, month, and vehicle type
                $existing = PetrolAllowance::where('employee_id', $request->employee_id)
                    ->whereYear('month', $monthDate->year)
                    ->whereMonth('month', $monthDate->month)
                    ->where('vehicle_type', $request->vehicle_type)
                    ->where('created_by', \Auth::user()->creatorId())
                    ->first();

                if ($existing) {
                    // Update existing record
                    $existing->amount = $request->amount;
                    $existing->save();
                    $createdRecords[] = $existing->load('employee');
                } else {
                    // Create new record
                    $petrolAllowance = PetrolAllowance::create([
                        'employee_id' => $request->employee_id,
                        'month' => $monthDate,
                        'vehicle_type' => $request->vehicle_type,
                        'amount' => $request->amount,
                        'created_by' => \Auth::user()->creatorId(),
                    ]);
                    $createdRecords[] = $petrolAllowance->load('employee');
                }
            }

            return response()->json([
                'success' => __('Petrol allowance created successfully.'),
                'petrol_allowances' => $createdRecords
            ], 200);

        } catch (\Exception $e) {
            Log::error('PetrolAllowanceController store error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
            // Check if it's a table doesn't exist error
            if (strpos($e->getMessage(), "doesn't exist") !== false || strpos($e->getMessage(), 'Base table or view not found') !== false) {
                return response()->json([
                    'error' => __('Database table not found. Please run migrations: php artisan migrate')
                ], 500);
            }
            
            return response()->json([
                'error' => __('Something went wrong. Please try again.') . ' (' . $e->getMessage() . ')'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (\Auth::user()->type != 'company') {
                return redirect()->route('dashboard')->with('error', __('Permission denied.'));
            }

            $petrolAllowance = PetrolAllowance::where('id', $id)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();

            if (!$petrolAllowance) {
                return redirect()->route('petrol-allowance.index')->with('error', __('Petrol allowance not found.'));
            }

            $petrolAllowance->delete();

            return redirect()->route('petrol-allowance.index')
                ->with('success', __('Petrol allowance deleted successfully.'));

        } catch (\Exception $e) {
            Log::error('PetrolAllowanceController destroy error: ' . $e->getMessage());
            return redirect()->route('petrol-allowance.index')->with('error', __('Something went wrong.'));
        }
    }

    public function getEmployees(Request $request)
    {
        try {
            if (\Auth::user()->type != 'company') {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            $search = $request->get('search', '');
            
            $employees = Employee::where('created_by', \Auth::user()->creatorId())
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('employee_id', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->select('id', 'name', 'employee_id', 'email')
                ->limit(20)
                ->get()
                ->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'text' => $employee->name . ' (' . $employee->employee_id . ')',
                        'name' => $employee->name,
                        'employee_id' => $employee->employee_id,
                    ];
                });

            return response()->json($employees);

        } catch (\Exception $e) {
            Log::error('PetrolAllowanceController getEmployees error: ' . $e->getMessage());
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }
}
