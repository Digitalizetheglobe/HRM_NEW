<?php

namespace App\Exports;

use App\Models\Branch;
use App\Models\Site;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $employees = Employee::where('created_by', \Auth::user()->creatorId())->get();
        $data = [];
        
        foreach($employees as $employee)
        {
            // Format the employee ID as RIS001, RIS002, etc.
            $formattedId = 'RS' . str_pad($employee->id, 3, '0', STR_PAD_LEFT);
            
            $row = [
                'employee_id' => $formattedId, // Use the formatted ID
                'name' => $employee->full_name,
                'dob' => $employee->dob,
                'blood_group' => $employee->blood_group,
                'gender' => $employee->gender,
                'phone' => $employee->phone,
                'office_phone_one' => $employee->office_phone_one,
                'office_phone_two' => $employee->office_phone_two,
                'emergency_number' => $employee->emergency_number,
                'address' => $employee->address,
                'email' => $employee->email,
                'branch_id' => !empty($employee->branch_id) ? $employee->branch->name : '-',
                'department_id' => !empty($employee->department_id) ? $employee->department->name : '-',
                'designation_id' => !empty($employee->designation_id) ? $employee->designation->name : '-',
                'education_details' => !empty($employee->education_details) ? json_encode($employee->education_details) : '-',
                'experience_details' => !empty($employee->experience_details) ? json_encode($employee->experience_details) : '-',
                'company_doj' => $employee->company_doj,
                'salary' => Employee::employee_salary($employee->salary),
                'week_off_day' => 'Sunday',
                'education_images' => !empty($employee->education_images) ? json_encode($employee->education_images) : '-',
            ];

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            "Employee ID", // Add Employee ID as the first column
            "Name",
            "Date of Birth",
            "Blood Group",
            "Gender",
            "Phone Number",
            "Office Phone One",
            "Office Phone Two",
            "Emergency Number",
            "Address",
            "Email ID",
            "Branch",
            "Department",
            "Designation",
            "Education Details",
            "Experience Details",
            "Date of Join",
            "Salary",
            "Week Off Day",
            "Education Images",
        ];
    }
}