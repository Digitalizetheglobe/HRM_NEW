<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        $companyId = Auth::id();
        $branchesCount = Branch::where('company_id', $companyId)->count();
        $departmentsCount = Department::where('company_id', $companyId)->count();
        $designationsCount = Designation::where('company_id', $companyId)->count();

        return view('settings.index', compact('branchesCount', 'departmentsCount', 'designationsCount'));
    }
}
