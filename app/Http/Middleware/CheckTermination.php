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
        if (Auth::check()) {
            $user = Auth::user();
            $isTerminated = false;

            if ($user->is_active == 0 && !in_array(strtolower($user->type), ['super admin', 'company'], true)) {
                $isTerminated = true;
            } elseif ($user->type === 'employee') {
                $employee = Employee::where('user_id', $user->id)->first();
                if ($employee && Termination::where('employee_id', $employee->id)->exists()) {
                    $isTerminated = true;
                }
            }

            if ($isTerminated) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success'  => false,
                        'message'  => __('Your account has been terminated. Please contact administrator.'),
                        'redirect' => route('login'),
                    ], 401);
                }

                return redirect()->route('login')->with('error', __('Your account has been terminated. Please contact administrator.'));
            }
        }

        return $next($request);
    }
}