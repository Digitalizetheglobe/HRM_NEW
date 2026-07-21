<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'employee_uid',
        'phone',
        'dob',
        'gender',
        'address',
        'branch_id',
        'department_id',
        'designation_id',
        'salary_type',
        'basic_salary',
        'joining_date',
        'termination_date',
        'account_holder_name',
        'account_number',
        'bank_name',
        'bank_branch',
        'ifsc_code',
        'pan_number',
        'doc_aadhar_card',
        'doc_pan_card',
        'doc_marksheet_10th',
        'doc_marksheet_12th',
        'doc_degree_certificate',
        'doc_experience_letter',
        'doc_offer_letter',
        'doc_passport_photo',
    ];

    protected $casts = [
        'dob'              => 'date',
        'joining_date'     => 'date',
        'termination_date' => 'date',
        'basic_salary'     => 'decimal:2',
    ];

    /** The user account for this employee (login credentials). */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** The administrator/company that owns this employee. */
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    /** Branch assignment. */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /** Department assignment. */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /** Designation assignment. */
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Generate the next employee UID for a given company.
     * Format: #DTG001, #DTG002, ...
     */
    public static function generateUid(int $companyId): string
    {
        $last = self::where('company_id', $companyId)
            ->whereNotNull('employee_uid')
            ->orderByDesc('id')
            ->value('employee_uid');

        if ($last) {
            // Extract numeric part and increment
            $num = (int) preg_replace('/[^0-9]/', '', $last);
        } else {
            $num = 0;
        }

        return '#DTG' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Returns true if the employee is considered active.
     */
    public function isActive(): bool
    {
        return $this->user && $this->user->is_active;
    }
}
