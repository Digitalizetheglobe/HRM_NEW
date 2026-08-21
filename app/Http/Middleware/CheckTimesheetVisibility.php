<?php

namespace App\Http\Middleware;

use App\Models\TimeSheet;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTimesheetVisibility
{
    public function handle(Request $request, Closure $next)
    {
        $timeSheetId = $request->route('timesheet') ?? $request->route('timeSheet');
        
        if ($timeSheetId) {
            $timeSheet = $timeSheetId instanceof TimeSheet ? $timeSheetId : TimeSheet::find($timeSheetId);
            
            if ($timeSheet && !$timeSheet->isVisibleTo(Auth::id())) {
                if ($request->ajax()) {
                    return response()->json(['error' => __('Permission denied.')], 403);
                }
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        return $next($request);
    }
}
