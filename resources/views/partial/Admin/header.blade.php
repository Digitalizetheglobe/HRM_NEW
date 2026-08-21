@php
    use App\Models\Utility;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    $users = Auth::user();
    $currantLang = $users->currentLanguage();
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
        ->where('seen', 0)
        ->count();
    $unseen_count = DB::select('SELECT from_id, COUNT(*) AS totalmasseges FROM ch_messages WHERE seen = 0 GROUP BY from_id');

    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::id())
        ->where('seen', 0)
        ->count();

    // Get leave notifications (for company type and forwarded Casual Leaves to HR/Director)
    $leaveNotifications = collect([]);
    $unseenLeaveCount = 0;
    if (\Auth::user()->type == 'company') {
        // Company sees all pending leaves that haven't been cleared
        $leaveNotifications = \App\Models\Leave::where('status', 'pending')
            ->where('seen_by_manager', 0)
            ->with(['employees.user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->get();
        $unseenLeaveCount = $leaveNotifications->count();
    } elseif (in_array(strtolower(\Auth::user()->type), ['director', 'hr'])) {
        // Directors and HR see only pending Casual Leave requests
        $leaveNotifications = \App\Models\Leave::where('status', 'pending')
            ->where('seen_by_director', 0)
            ->whereHas('leaveType', function($query) {
                $query->where('title', 'Casual Leave');
            })
            ->with(['employees.user', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->get();
        $unseenLeaveCount = $leaveNotifications->count();
    }



    // Get attendance regularization notifications (for company, hr, director types)
    $regularisationNotifications = collect([]);
    $unseenRegularisationCount = 0;
    if (in_array(strtolower(\Auth::user()->type), ['company', 'hr', 'director'])) {
        try {
            if (\Schema::hasTable('notifications')) {
                // Get regularisation notifications - use OR condition to catch both exact and partial matches
                $regularisationNotifications = Auth::user()->unreadNotifications()
                    ->where(function($query) {
                        $query->where('type', 'App\Notifications\AttendanceRegularisationNotification')
                              ->orWhere('type', 'LIKE', '%AttendanceRegularisation%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
                
                // Count unread notifications
                $unseenRegularisationCount = $regularisationNotifications->count();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or query failed, use empty collection
            \Log::error('Error fetching regularisation notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $regularisationNotifications = collect([]);
            $unseenRegularisationCount = 0;
        }
    }

    // Get General Notifications
    $generalNotifications = collect([]);
    $unseenGeneralCount = 0;
    try {
        if (\Schema::hasTable('notifications')) {
            $generalNotifications = Auth::user()->unreadNotifications()
                ->whereIn('type', [
                    'App\Notifications\ProjectAssignmentNotification',
                    'App\Notifications\ProjectReportSubmittedNotification',
                    'App\Notifications\ProjectReportApprovalNotification',
                    'App\Notifications\ClientFeedbackNotification',
                    'App\Notifications\ProjectAssignedNotification'
                ])
                ->orderBy('created_at', 'desc')
                ->take(15)
                ->get();
            $unseenGeneralCount = $generalNotifications->count();
        }
    } catch (\Exception $e) {
        \Log::error('Error fetching general notifications', ['error' => $e->getMessage()]);
    }

    // Calculate total unseen notifications
    $totalUnseenCount = $unseenLeaveCount + $unseenRegularisationCount + $unseenGeneralCount;
@endphp

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <header class="dash-header transprent-bg" style="background: linear-gradient(to right, #fff, #fff); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
@else
    <header class="dash-header" style="background: linear-gradient(to right, #0a3772, #008ecc);">
@endif

<div class="header-wrapper" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
    <div class="me-auto dash-mob-drp">
        <ul class="list-unstyled" style="display: flex; align-items: center;">
            <li class="dash-h-item mob-hamburger">
                <a href="#!" class="dash-head-link" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
            </li>
            <li class="dropdown dash-h-item drp-company">
                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false" style="background-color: white;">
                    <span class="theme-avtar" style="background-color: white;">
                        <img alt="User Avatar"
                             src="{{ !empty(Auth::user()->avatar) ? $profile . Auth::user()->avatar : $profile . 'avatar.png' }}"
                             class="header-avtar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background-color: white;">
                    </span>
                    <span class="hide-mob ms-2" style="background-color: white;">{{ 'Hi, ' . Auth::user()->name . '!' }}
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob" style="background-color: white;"></i>
                    </span>
                </a>
                <div class="dropdown-menu dash-h-dropdown" style="background-color: white;">
                    <a href="{{ route('profile') }}" class="dropdown-item" style="background-color: white;">
                        <i class="ti ti-user"></i>
                        <span>{{ __('My Profile') }}</span>
                    </a>
                    <a href="{{ route('logout') }}" class="dropdown-item"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                       style="background-color: white;">
                        <i class="ti ti-power"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
    
    <!-- Marquee Section for Daily Quote -->
    <div class="quote-container" style="display: flex; justify-content: center; align-items: center; flex-grow: 1; overflow: hidden;">
        <marquee behavior="scroll" direction="left" scrollamount="6" style="color: #0a3c77; font-size: 18px; font-weight: bold; width: 100%; margin-left: 11px;">
            " {{ $quote->quote ?? 'No quote for today!!' }} "
        </marquee>
    </div>

    <div class="ms-auto" style="display: flex; justify-content: flex-end; align-items: center;">
        <ul class="list-unstyled" style="display: flex; align-items: center;">
            @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'employee' || in_array(strtolower(\Auth::user()->type), ['hr', 'director']))
                <style>
/* ============================================================
   Modern Notification Panel
============================================================ */
.notif-trigger {
    position: relative;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.25);
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
    text-decoration: none !important;
}
.notif-trigger:hover {
    background: rgba(255,255,255,0.28);
    box-shadow: 0 0 0 4px rgba(255,255,255,0.12);
}
.notif-trigger i {
    font-size: 18px;
    color: #fff;
    line-height: 1;
}
.notif-badge {
    position: absolute;
    top: -3px;
    right: -4px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 50px;
    background: linear-gradient(135deg, #ff416c, #ff4b2b);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(255,65,108,.5);
    animation: pulse-badge 2s infinite;
}
@keyframes pulse-badge {
    0%,100% { box-shadow: 0 2px 6px rgba(255,65,108,.5); }
    50%      { box-shadow: 0 2px 14px rgba(255,65,108,.8); }
}

/* Panel */
.notif-panel {
    width: 390px;
    max-width: 95vw;
    border: none !important;
    border-radius: 18px !important;
    box-shadow: 0 20px 60px rgba(0,0,0,.18), 0 4px 16px rgba(0,0,0,.1) !important;
    overflow: hidden;
    padding: 0 !important;
    margin-top: 10px !important;
    animation: slideDown .18s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Header */
.notif-panel-header {
    background: linear-gradient(135deg, #0a3772 0%, #1565C0 60%, #008ecc 100%);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.notif-panel-header h6 {
    margin: 0;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: .3px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.notif-panel-header h6 span.n-count {
    background: rgba(255,255,255,.22);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 1px 7px;
    border-radius: 20px;
}
.notif-clear-btn {
    background: rgba(255,255,255,.18);
    color: #fff !important;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 20px;
    padding: 3px 12px;
    cursor: pointer;
    transition: background .2s;
    text-decoration: none !important;
    line-height: 1.6;
}
.notif-clear-btn:hover {
    background: rgba(255,255,255,.32);
    color: #fff !important;
}

/* Category label */
.notif-category-label {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px 6px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: #8a94a6;
    background: #f8fafc;
    border-bottom: 1px solid #eef1f6;
}
.notif-category-label i { font-size: 13px; }

/* Notification Item */
.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 18px;
    border-bottom: 1px solid #f0f3f7;
    transition: background .15s;
    cursor: default;
    position: relative;
}
.notif-item.unread {
    background: #f5f8ff;
}
.notif-item.unread::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #0a3772, #008ecc);
    border-radius: 0 3px 3px 0;
}
.notif-item:hover {
    background: #eef3fc;
}

/* Avatar icon */
.notif-avatar {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}
.notif-avatar.blue   { background: #e8f0fe; color: #1a73e8; }
.notif-avatar.green  { background: #e6f4ea; color: #34a853; }
.notif-avatar.amber  { background: #fef7e0; color: #f9ab00; }
.notif-avatar.purple { background: #f3e8fd; color: #9c27b0; }
.notif-avatar.red    { background: #fce8e6; color: #d93025; }
.notif-avatar.teal   { background: #e0f5f1; color: #00897b; }

/* Text */
.notif-body-text { flex: 1; min-width: 0; }
.notif-body-text .n-title {
    font-size: 13.5px;
    font-weight: 600;
    color: #1a202c;
    margin: 0 0 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notif-body-text .n-msg {
    font-size: 12px;
    color: #64748b;
    margin: 0 0 5px;
    line-height: 1.45;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.notif-body-text .n-time {
    font-size: 11px;
    color: #94a3b8;
}

/* Action button */
.notif-action-btn {
    flex-shrink: 0;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    transition: transform .15s, opacity .15s;
    font-size: 14px;
}
.notif-action-btn:hover { transform: scale(1.1); opacity: .85; }
.notif-action-btn.blue-btn   { background: linear-gradient(135deg,#1565C0,#008ecc); color:#fff; }
.notif-action-btn.green-btn  { background: linear-gradient(135deg,#2e7d32,#43a047); color:#fff; }
.notif-action-btn.amber-btn  { background: linear-gradient(135deg,#e65100,#ff8f00); color:#fff; }
.notif-action-btn.purple-btn { background: linear-gradient(135deg,#6a1b9a,#ab47bc); color:#fff; }
.notif-action-btn.teal-btn   { background: linear-gradient(135deg,#00695c,#26a69a); color:#fff; }

/* Body scroll */
.notif-scroll {
    max-height: 380px;
    overflow-y: auto;
    overscroll-behavior: contain;
}
.notif-scroll::-webkit-scrollbar { width: 4px; }
.notif-scroll::-webkit-scrollbar-track { background: #f8fafc; }
.notif-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Empty state */
.notif-empty {
    padding: 40px 20px;
    text-align: center;
}
.notif-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg,#f0f4ff,#e8ecf9);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 14px;
    font-size: 28px;
    color: #b0bcda;
}
.notif-empty p {
    color: #94a3b8;
    font-size: 13px;
    margin: 0;
}

/* Footer */
.notif-footer-links {
    padding: 10px 18px;
    background: #f8fafc;
    border-top: 1px solid #eef1f6;
    display: flex;
    gap: 8px;
    flex-direction: column;
}
.notif-footer-links a {
    font-size: 12.5px;
    font-weight: 600;
    color: #1565C0;
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 8px;
    transition: background .15s;
    display: flex;
    align-items: center;
    gap: 6px;
}
.notif-footer-links a:hover { background: #e8f0fe; }
</style>

<li class="dropdown dash-h-item drp-notification">
    {{-- Bell Button --}}
    <a class="dash-head-link dropdown-toggle arrow-none me-0 notif-trigger"
        data-bs-toggle="dropdown" href="#"
        role="button" aria-haspopup="false" aria-expanded="false"
        id="unified-notification-btn">
        <i class="ti ti-bell"></i>
        @if($totalUnseenCount > 0)
            <span class="notif-badge">{{ $totalUnseenCount > 99 ? '99+' : $totalUnseenCount }}</span>
        @endif
    </a>

    {{-- Dropdown Panel --}}
    <div class="dropdown-menu notif-panel dropdown-menu-end">

        {{-- Panel Header --}}
        <div class="notif-panel-header">
            <h6>
                <i class="ti ti-bell-ringing"></i>
                {{ __('Notifications') }}
                @if($totalUnseenCount > 0)
                    <span class="n-count">{{ $totalUnseenCount }} new</span>
                @endif
            </h6>
            @if($leaveNotifications->count() > 0 || $regularisationNotifications->count() > 0 || (isset($generalNotifications) && $generalNotifications->count() > 0))
                <form action="{{ route('notifications.readAll') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="notif-clear-btn">{{ __('Clear All') }}</button>
                </form>
            @endif
        </div>

        {{-- Scrollable Body --}}
        <div class="notif-scroll">

            {{-- ── System Notifications ── --}}
            @if(isset($generalNotifications) && $generalNotifications->count() > 0)
                <div class="notif-category-label">
                    <i class="ti ti-device-desktop-analytics"></i> System Notifications
                </div>
                @foreach($generalNotifications as $notification)
                    @php
                        $data    = $notification->data;
                        $title   = $data['title'] ?? 'System Notification';
                        if (isset($data['project_name']) && !isset($data['title'])) {
                            $title = 'New Project Assignment';
                        }
                        $message = $data['message'] ?? '';
                        $url     = route('notifications.read', $notification->id);
                        $isUnread = !$notification->read_at;

                        // Choose icon + colour by notification type
                        $nType = $notification->type ?? '';
                        if (str_contains($nType, 'ProjectAssignment'))          { $avatarClass='blue';   $avatarIcon='ti-briefcase'; $btnClass='blue-btn'; }
                        elseif (str_contains($nType, 'ReportSubmitted'))        { $avatarClass='amber';  $avatarIcon='ti-file-text'; $btnClass='amber-btn'; }
                        elseif (str_contains($nType, 'ReportApproval'))         { $avatarClass='green';  $avatarIcon='ti-circle-check'; $btnClass='green-btn'; }
                        elseif (str_contains($nType, 'ClientFeedback'))         { $avatarClass='purple'; $avatarIcon='ti-message-2'; $btnClass='purple-btn'; }
                        else                                                     { $avatarClass='blue';   $avatarIcon='ti-bell'; $btnClass='blue-btn'; }
                    @endphp
                    <div class="notif-item {{ $isUnread ? 'unread' : '' }}">
                        <div class="notif-avatar {{ $avatarClass }}">
                            <i class="ti {{ $avatarIcon }}"></i>
                        </div>
                        <div class="notif-body-text">
                            <p class="n-title">{{ $title }}</p>
                            <p class="n-msg">{{ $message }}</p>
                            <span class="n-time"><i class="ti ti-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ $url }}" class="notif-action-btn {{ $btnClass }}" title="{{ __('View') }}">
                            <i class="ti ti-chevron-right"></i>
                        </a>
                    </div>
                @endforeach
            @endif

            {{-- ── Leave Requests ── --}}
            @if((\Auth::user()->type == 'company' || in_array(strtolower(\Auth::user()->type), ['director', 'hr'])) && $leaveNotifications->count() > 0)
                <div class="notif-category-label">
                    <i class="ti ti-calendar-time"></i>
                    @if(\Auth::user()->type == 'company') Leave Requests @else Forwarded Leave Requests @endif
                </div>
                @foreach($leaveNotifications as $leave)
                    @php
                        $isUnreadLeave = !($leave->seen_by_manager || $leave->seen_by_director);
                        $employeeName = $leave->employees?->user?->name ?? $leave->employee_name ?? 'Unknown Employee';
                        $leaveTypeName = $leave->leaveType?->title ?? 'N/A';
                        $leaveDateStr = ($leave->forwarded_at
                            ? \Carbon\Carbon::parse($leave->forwarded_at)
                            : $leave->created_at)->diffForHumans();
                    @endphp
                    <div class="notif-item {{ $isUnreadLeave ? 'unread' : '' }}" data-leave-id="{{ $leave->id }}">
                        <div class="notif-avatar red">
                            <i class="ti ti-user-pause"></i>
                        </div>
                        <div class="notif-body-text">
                            <p class="n-title">{{ $employeeName }} <span style="font-weight:400;color:#94a3b8;"> · {{ $leaveTypeName }}</span></p>
                            <p class="n-msg">{{ $leave->start_date }} to {{ $leave->end_date }} &mdash; {{ Str::limit($leave->leave_reason, 40) }}</p>
                            @if(strtolower(\Auth::user()->type) == 'director' && $leave->forwardedByCompany)
                                <p class="n-msg" style="color:#0288d1;"><i class="ti ti-corner-down-right"></i> Forwarded by: {{ $leave->forwardedByCompany->name }}</p>
                            @endif
                            <span class="n-time"><i class="ti ti-clock me-1"></i>{{ $leaveDateStr }}</span>
                        </div>
                        <a href="#"
                            data-size="lg"
                            data-url="{{ URL::to('leave/' . $leave->id . '/action') }}"
                            data-ajax-popup="true"
                            data-size="md"
                            data-title="{{ __('Leave Action') }}"
                            class="notif-action-btn green-btn" title="{{ __('Manage Leave') }}">
                            <i class="ti ti-chevron-right"></i>
                        </a>
                    </div>
                @endforeach
            @endif

            {{-- ── Attendance Regularisation ── --}}
            @if(in_array(strtolower(\Auth::user()->type), ['company', 'hr', 'director']) && $regularisationNotifications->count() > 0)
                <div class="notif-category-label">
                    <i class="ti ti-clock-hour-4"></i> Attendance Regularisation
                </div>
                @foreach($regularisationNotifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = !$notification->read_at;
                    @endphp
                    <div class="notif-item {{ $isUnread ? 'unread' : '' }}" data-notification-id="{{ $notification->id }}">
                        <div class="notif-avatar teal">
                            <i class="ti ti-clock-edit"></i>
                        </div>
                        <div class="notif-body-text">
                            <p class="n-title">{{ $data['employee_name'] ?? 'Employee' }}
                                <span style="font-weight:400;color:#94a3b8;"> · Regularisation</span>
                            </p>
                            <p class="n-msg">Date: {{ $data['date'] ?? 'N/A' }} — {{ Str::limit($data['message'] ?? '', 45) }}</p>
                            <span class="n-time"><i class="ti ti-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ $data['url'] ?? route('attendance-regularisation.index') }}"
                            class="notif-action-btn teal-btn mark-notification-read"
                            data-notification-id="{{ $notification->id }}"
                            title="{{ __('View Request') }}">
                            <i class="ti ti-chevron-right"></i>
                        </a>
                    </div>
                @endforeach
            @endif

            {{-- ── Empty State ── --}}
            @if(
                $leaveNotifications->count() == 0 &&
                $regularisationNotifications->count() == 0 &&
                (!isset($generalNotifications) || $generalNotifications->count() == 0)
            )
                <div class="notif-empty">
                    <div class="notif-empty-icon">
                        <i class="ti ti-bell-off"></i>
                    </div>
                    <p>{{ __('You\'re all caught up!') }}<br><span style="font-size:11px;">No new notifications</span></p>
                </div>
            @endif

        </div>{{-- /.notif-scroll --}}

        {{-- Footer quick links --}}
        @php
            $hasLeave = (\Auth::user()->type == 'company' || in_array(strtolower(\Auth::user()->type), ['director', 'hr'])) && $leaveNotifications->count() > 0;
            $hasReg   = in_array(strtolower(\Auth::user()->type), ['company', 'hr', 'director']) && $regularisationNotifications->count() > 0;
        @endphp
        @if($hasLeave || $hasReg)
            <div class="notif-footer-links">
                @if($hasLeave)
                    <a href="{{ route('leave.index') }}"><i class="ti ti-calendar-event"></i> View All Leave Requests</a>
                @endif
                @if($hasReg)
                    <a href="{{ route('attendance-regularisation.index') }}"><i class="ti ti-clock-hour-4"></i> View All Regularisation Requests</a>
                @endif
            </div>
        @endif

    </div>{{-- /.notif-panel --}}
</li>
            @endif
        </ul>
    </div>

</div>
</header>

@push('scripts')
    <script>
        $('#msg-btn').click(function() {
            let contactsPage = 1;
            let contactsLoading = false;
            let noMoreContacts = false;
            $.ajax({
                url: url + "/getContacts",
                method: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    page: contactsPage,
                    type: 'custom',
                },
                dataType: "JSON",
                success: (data) => {
                    if (contactsPage < 2) {
                        $(".count-listOfContacts").html(data.contacts);
                    } else {
                        $(".count-listOfContacts").append(data.contacts);
                    }
                    $('.count-listOfContacts').find('.messenger-list-item').each(function(e) {
                        $('.noti-body .activeStatus').remove()
                        $('.noti-body .avatar').remove()
                        $(this).find('span').remove()
                        $(this).find('p').addClass("d-inline")
                        $(this).find('b').css({
                            "position": "absolute",
                            "right": "50px"
                        });
                        $(this).find('tr').remove('td')
                    })
                },
                error: (error) => {
                    setContactsLoading(false);
                    console.error(error);
                },
            });
        })
        
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.show-employee-info').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const leaveId = this.getAttribute('data-leave-id');
                    const card = document.getElementById(`employee-card-${leaveId}`);
                    
                    document.querySelectorAll('.employee-info-card').forEach(el => {
                        el.style.display = 'none';
                    });
                    
                    card.style.display = 'block';
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            });

            document.querySelectorAll('.cancel-action').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.employee-info-card').style.display = 'none';
                });
            });

            document.querySelectorAll('.confirm-action').forEach(button => {
                button.addEventListener('click', function(e) {
                    if(!confirm(`Are you sure you want to ${this.getAttribute('data-status')} this leave request?`)) {
                        e.preventDefault();
                    }
                });
            });

            // Update unified badge when leave notification is interacted with
            document.querySelectorAll('.leave-notification-item').forEach(item => {
                const actionLink = item.querySelector('a[data-url*="leave"]');
                if (actionLink) {
                    actionLink.addEventListener('click', function() {
                        // Mark as seen visually
                        item.style.backgroundColor = '#fff';
                        // Update unified badge count
                        const badge = document.querySelector('.unified-counter');
                        if (badge) {
                            const currentCount = parseInt(badge.textContent) || 0;
                            if (currentCount > 1) {
                                badge.textContent = currentCount - 1;
                            } else {
                                badge.remove();
                            }
                        }
                    });
                }
            });

            // Mark booking and regularisation notifications as read when view link is clicked
            document.querySelectorAll('.mark-notification-read').forEach(link => {
                link.addEventListener('click', function(e) {
                    const notificationId = this.getAttribute('data-notification-id');
                    if (notificationId) {
                        // Mark as read via AJAX using Laravel's notification system
                        fetch('{{ url("/notification/mark-read") }}/' + notificationId, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(response => {
                            if (response.ok) {
                                // Handle regularisation notifications and project notifications
                                const notificationItem = this.closest('.regularisation-notification-item') || this.closest('.project-notification-item');
                                if (notificationItem) {
                                    notificationItem.style.backgroundColor = '#fff';
                                    // Update unified badge count
                                    const badge = document.querySelector('.unified-counter');
                                    if (badge) {
                                        const currentCount = parseInt(badge.textContent) || 0;
                                        if (currentCount > 1) {
                                            badge.textContent = currentCount - 1;
                                        } else {
                                            badge.remove();
                                        }
                                    }
                                }
                            }
                        }).catch(error => {
                            console.error('Error marking notification as read:', error);
                        });
                    }
                });
            });

            // Mark all notifications as read
            $(document).on('click', '#mark-all-read', function(e) {
                e.preventDefault();
                fetch('{{ route("notification.markAllRead") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            console.error("Failed to mark notifications as read", response);
                        }
                    }).catch(error => {
                        console.error('Error marking all notifications as read:', error);
                    });
            });

            // Capture and Register Mobile Device Token for Push Notifications
            const urlParams = new URLSearchParams(window.location.search);
            const deviceToken = urlParams.get('device_token');
            
            if (deviceToken) {
                localStorage.setItem('mobile_device_token', deviceToken);
                // Clean up the URL to not show the token
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path: newUrl}, '', newUrl);
            }
            
            const storedToken = localStorage.getItem('mobile_device_token');
            
            @auth
            if (storedToken && localStorage.getItem('registered_device_token') !== storedToken) {
                fetch("{{ route('register.device.token') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ device_token: storedToken })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('registered_device_token', storedToken);
                        console.log('Device token registered successfully.');
                    }
                })
                .catch(error => console.error('Error registering device token:', error));
            }
            @endauth
        });
    </script>
@endpush