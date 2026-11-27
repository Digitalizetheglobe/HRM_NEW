<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            
            if (isset($notification->data['url'])) {
                return redirect($notification->data['url']);
            }
        }
        return redirect()->back();
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($user->type === 'company') {
            Leave::where('status', 'pending')
                ->where('seen_by_manager', 0)
                ->update(['seen_by_manager' => 1]);
        } elseif (in_array(strtolower($user->type), ['director', 'hr'], true)) {
            Leave::where('status', 'pending')
                ->where('seen_by_director', 0)
                ->whereHas('leaveType', function ($query) {
                    $query->where('title', 'Casual Leave');
                })
                ->update(['seen_by_director' => 1]);
        }

        if (\Schema::hasTable('ch_messages')) {
            \DB::table('ch_messages')
                ->where('to_id', $user->id)
                ->where('seen', 0)
                ->update(['seen' => 1]);
        }

        if (\Schema::hasTable('chat_group_members')) {
            \DB::table('chat_group_members')
                ->where('user_id', $user->id)
                ->update(['last_read_at' => now()]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
