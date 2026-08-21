<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;

class CheckDocumentApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check for employee users
        if (auth()->user()->type !== 'employee') {
            return $next($request);
        }

        // Get the employee record
        $employee = Employee::where('user_id', auth()->user()->id)->first();
        
        if (!$employee) {
            return $next($request);
        }

        
        // Check if employee has approved status
        if ($employee->approval_status !== 'approved') {
            // Allow access to employee profile pages and document upload pages
            $allowedRoutes = [
                'employee.show',
                'employee.edit',
                'employee.update',
                'dashboard',
                'logout',
                'profile.edit',
                'profile.update',
                // Add document-related routes if they exist
                'document-upload.index',
                'document-upload.create',
                'document-upload.store',
                'document-upload.edit',
                'document-upload.update',
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                // Redirect to employee profile with a message
                return redirect()->route('employee.show', encrypt($employee->id))
                    ->with('warning', __('Your account is pending approval. Please wait for admin approval to access this feature.'));
            }
        }

        return $next($request);
    }
}
