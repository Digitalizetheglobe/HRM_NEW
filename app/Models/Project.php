<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_name', 'client_name', 'project_description', 'location', 'project_type',
        'project_startdate', 'project_days', 'project_enddate', 'status', 'current_status', 'technology', 'delay_reason',
        'assigned_data', 'created_by', 'site_heads',
        'project_priority', 'estimated_hours', 'actual_hours', 'project_progress',
        'share_token', 'share_link_enabled', 'share_password', 'ui_ux_required',
        'has_urls', 'website_url', 'dashboard_url', 'project_lead'
    ];

    protected $casts = [
        'assigned_data' => 'array', // This will automatically handle JSON decode/encode
        'site_heads' => 'array',
        'technology' => 'array',
        // Remove the date casts for project_startdate and project_enddate
    ];




    // Rest of your model code remains the same...
    public function getDepartmentNames()
    {
        $departmentIds = collect($this->assigned_data)->pluck('department_id')->toArray();
        return Department::whereIn('id', $departmentIds)->pluck('name', 'id');
    }

    public function getEmployeeNames()
    {
        $employeeIds = collect($this->assigned_data)
            ->pluck('employee_ids')
            ->flatten()
            ->unique()
            ->toArray();
            
        return Employee::with('user')->whereIn('id', $employeeIds)->get()->mapWithKeys(function($emp) {
            $fullName = $emp->user ? $emp->user->name : trim("{$emp->name} {$emp->middle_name} {$emp->last_name}");
            return [$emp->id => $fullName ?: $emp->name];
        })->toArray();
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'project_department')
                    ->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'project_employee')
                    ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeAssignedToEmployee($query, $employeeId)
    {
        return $query->where(function($q) use ($employeeId) {
            $q->whereJsonContains('assigned_data', [['employee_ids' => [(string)$employeeId]]])
              ->orWhereJsonContains('assigned_data', [['employee_ids' => [$employeeId]]])
              ->orWhereJsonContains('assigned_data', ['employee_ids' => (string)$employeeId])
              ->orWhereJsonContains('assigned_data', ['employee_ids' => $employeeId]);
        });
    }

    public function siteHeads()
    {
        return $this->belongsToMany(Employee::class, 'project_site_head', 'project_id', 'employee_id');
    }

    public function scopeWhereSiteHead($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->whereJsonContains('site_heads', (string)$userId)
            ->orWhereJsonContains('site_heads', $userId);
        });
    }

    public function modules()
    {
        return $this->hasMany(ProjectModule::class);
    }

    public function dailyUpdates()
    {
        return $this->hasMany(ProjectDailyUpdate::class);
    }

    public function delays()
    {
        return $this->hasMany(ProjectDelay::class);
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function screenshots()
    {
        return $this->hasMany(ProjectScreenshot::class);
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class);
    }
    
    public function recalculateProgress()
    {
        $modules = $this->modules;
        if ($modules->count() > 0) {
            $this->project_progress = round($modules->avg('progress'));
        } else {
            $this->project_progress = 0;
        }
        $this->save();
    }
    
    public function getProjectHealthAttribute()
    {
        if ($this->status == 'completed') {
            return 'Completed';
        }
        
        // Handle invalid '0000-00-00' or similar dates
        $isValidDate = !empty($this->project_enddate) && !str_starts_with($this->project_enddate, '0000') && !str_starts_with($this->project_enddate, '-0001');
        
        if (!$isValidDate) {
            return 'Ongoing';
        }
        
        $endDate = \Carbon\Carbon::parse($this->project_enddate)->endOfDay();
        $now = \Carbon\Carbon::now();
        
        if ($now->greaterThan($endDate) && $this->project_progress < 100) {
            return 'Delayed';
        }
        
        $daysRemaining = $now->startOfDay()->diffInDays($endDate->startOfDay(), false);
        $progressRemaining = 100 - $this->project_progress;
        
        if ($daysRemaining < 7 && $progressRemaining > 20) {
            return 'Critical';
        } elseif ($daysRemaining < 14 && $progressRemaining > 40) {
            return 'At Risk';
        }
        
        return 'On Track';
    }
}