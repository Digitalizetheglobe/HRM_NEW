<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $companyId = Auth::id();

        $employees = User::where('company_id', $companyId)
            ->whereHas('role', fn($q) => $q->where('slug', 'employee'))
            ->with(['employee.branch', 'employee.department', 'employee.designation'])
            ->orderBy('id', 'desc')
            ->get();

        $activeCount   = $employees->where('is_active', 1)->count();
        $inactiveCount = $employees->where('is_active', 0)->count();

        return response()->json([
            'employees' => $employees->values(),
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount
        ]);
    }

    public function createData()
    {
        $companyId    = Auth::id();
        $branches     = Branch::where('company_id', $companyId)->get();
        $departments  = Department::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();
        $nextUid      = Employee::generateUid($companyId);

        return response()->json([
            'branches' => $branches,
            'departments' => $departments,
            'designations' => $designations,
            'nextUid' => $nextUid
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'password'            => 'required|string|min:8',
            'phone'               => 'nullable|string|max:20',
            'dob'                 => 'nullable|date',
            'gender'              => 'nullable|in:male,female,other',
            'address'             => 'nullable|string',
            'branch_id'           => 'required|exists:branches,id',
            'department_id'       => 'required|exists:departments,id',
            'designation_id'      => 'required|exists:designations,id',
            'salary_type'         => 'nullable|string',
            'basic_salary'        => 'nullable|numeric|min:0',
            'joining_date'        => 'nullable|date',
            'is_active'           => 'nullable|boolean',
            'account_holder_name' => 'nullable|string',
            'account_number'      => 'nullable|string',
            'bank_name'           => 'nullable|string',
            'bank_branch'         => 'nullable|string',
            'ifsc_code'           => 'nullable|string',
            'pan_number'          => 'nullable|string',
            'doc_aadhar_card'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_pan_card'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_marksheet_10th'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_marksheet_12th'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_degree_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_experience_letter'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_offer_letter'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_passport_photo'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $companyId = Auth::id();
        $user = null;

        DB::transaction(function () use ($request, $companyId, &$user) {
            $employeeRole = Role::where('slug', 'employee')->first();
            $avatarPath = null;
            if ($request->hasFile('doc_passport_photo')) {
                $avatarPath = $request->file('doc_passport_photo')->store('avatars', 'public');
            }

            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role_id'    => $employeeRole ? $employeeRole->id : null,
                'company_id' => $companyId,
                'avatar'     => $avatarPath,
                'is_active'  => $request->boolean('is_active', true),
            ]);

            $docFields = [
                'doc_aadhar_card', 'doc_pan_card', 'doc_marksheet_10th',
                'doc_marksheet_12th', 'doc_degree_certificate', 'doc_experience_letter',
                'doc_offer_letter', 'doc_passport_photo',
            ];
            $docs = [];
            foreach ($docFields as $field) {
                if ($request->hasFile($field)) {
                    $docs[$field] = $request->file($field)->store('employee_docs', 'public');
                }
            }

            $uid = Employee::generateUid($companyId);

            Employee::create(array_merge([
                'user_id'              => $user->id,
                'company_id'           => $companyId,
                'employee_uid'         => $uid,
                'phone'                => $request->phone,
                'dob'                  => $request->dob,
                'gender'               => $request->gender,
                'address'              => $request->address,
                'branch_id'            => $request->branch_id,
                'department_id'        => $request->department_id,
                'designation_id'       => $request->designation_id,
                'salary_type'          => $request->salary_type ?? 'Monthly',
                'basic_salary'         => $request->basic_salary ?? 0,
                'joining_date'         => $request->joining_date,
                'account_holder_name'  => $request->account_holder_name,
                'account_number'       => $request->account_number,
                'bank_name'            => $request->bank_name,
                'bank_branch'          => $request->bank_branch,
                'ifsc_code'            => $request->ifsc_code,
                'pan_number'           => $request->pan_number,
            ], $docs));
        });

        return response()->json([
            'message' => 'Employee created successfully!',
            'user' => $user
        ], 201);
    }

    public function show(User $employee)
    {
        if ($employee->company_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $employee->load(['employee.branch', 'employee.department', 'employee.designation']);

        return response()->json([
            'employee' => $employee
        ]);
    }

    public function edit(User $employee)
    {
        if ($employee->company_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $companyId    = Auth::id();
        $branches     = Branch::where('company_id', $companyId)->get();
        $departments  = Department::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();

        $employee->load('employee.branch', 'employee.department', 'employee.designation');

        return response()->json([
            'employee' => $employee,
            'branches' => $branches,
            'departments' => $departments,
            'designations' => $designations
        ]);
    }

    public function update(Request $request, User $employee)
    {
        if ($employee->company_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email,' . $employee->id,
            'password'            => 'nullable|string|min:8',
            'phone'               => 'nullable|string|max:20',
            'dob'                 => 'nullable|date',
            'gender'              => 'nullable|in:male,female,other',
            'address'             => 'nullable|string',
            'branch_id'           => 'required|exists:branches,id',
            'department_id'       => 'required|exists:departments,id',
            'designation_id'      => 'required|exists:designations,id',
            'salary_type'         => 'nullable|string',
            'basic_salary'        => 'nullable|numeric|min:0',
            'joining_date'        => 'nullable|date',
            'is_active'           => 'nullable|boolean',
            'account_holder_name' => 'nullable|string',
            'account_number'      => 'nullable|string',
            'bank_name'           => 'nullable|string',
            'bank_branch'         => 'nullable|string',
            'ifsc_code'           => 'nullable|string',
            'pan_number'          => 'nullable|string',
            'doc_aadhar_card'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_pan_card'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_marksheet_10th'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_marksheet_12th'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_degree_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_experience_letter'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_offer_letter'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'doc_passport_photo'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $userData = [
                'name'      => $request->name,
                'email'     => $request->email,
                'is_active' => $request->boolean('is_active', true),
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('doc_passport_photo')) {
                $userData['avatar'] = $request->file('doc_passport_photo')->store('avatars', 'public');
            }
            $employee->update($userData);

            $docFields = [
                'doc_aadhar_card', 'doc_pan_card', 'doc_marksheet_10th',
                'doc_marksheet_12th', 'doc_degree_certificate', 'doc_experience_letter',
                'doc_offer_letter', 'doc_passport_photo',
            ];
            $docs = [];
            foreach ($docFields as $field) {
                if ($request->hasFile($field)) {
                    $docs[$field] = $request->file($field)->store('employee_docs', 'public');
                }
            }

            $profileData = array_merge([
                'phone'                => $request->phone,
                'dob'                  => $request->dob,
                'gender'               => $request->gender,
                'address'              => $request->address,
                'branch_id'            => $request->branch_id,
                'department_id'        => $request->department_id,
                'designation_id'       => $request->designation_id,
                'salary_type'          => $request->salary_type ?? 'Monthly',
                'basic_salary'         => $request->basic_salary ?? 0,
                'joining_date'         => $request->joining_date,
                'account_holder_name'  => $request->account_holder_name,
                'account_number'       => $request->account_number,
                'bank_name'            => $request->bank_name,
                'bank_branch'          => $request->bank_branch,
                'ifsc_code'            => $request->ifsc_code,
                'pan_number'           => $request->pan_number,
            ], $docs);

            if ($employee->employee) {
                $employee->employee->update($profileData);
            } else {
                $profileData['user_id']    = $employee->id;
                $profileData['company_id'] = $employee->company_id;
                Employee::create($profileData);
            }
        });

        return response()->json(['message' => 'Employee updated successfully!']);
    }

    public function destroy(User $employee)
    {
        if ($employee->company_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Employee::where('user_id', $employee->id)->delete();
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully!']);
    }
}
