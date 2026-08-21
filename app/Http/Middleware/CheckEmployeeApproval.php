<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Auth::check() && \Auth::user()->type === 'employee') {
            $employee = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
            
            if ($employee && $employee->approval_status !== 'approved') {
                // Allow access only to specific routes
                $allowedRoutes = [
                    'employee.show',
                    'employee.edit',
                    'employee.update',
                    'logout',
                    'offline',
                ];
                
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    // Redirect unapproved employees to their profile page
                    return redirect()->route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id))
                                     ->with('error', __('Your profile is pending approval by the admin. You cannot access other modules.'));
                }
            }
        }
        
        return $next($request);
    }
}
