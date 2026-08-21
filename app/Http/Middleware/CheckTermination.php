<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use App\Models\Termination;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTermination
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->type == 'employee') {
            $employee = Employee::where('user_id', Auth::id())->first();
            
            if ($employee) {
                $termination = Termination::where('employee_id', $employee->id)->first();
                
                if ($termination) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->with('error', __('Your account has been terminated. Please contact administrator.'));
                }
            }
        }

        return $next($request);
    }
}