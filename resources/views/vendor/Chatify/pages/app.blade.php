@extends('layouts.admin')
@push('css-page')
    @include('Chatify::layouts.headLinks')

    <style>
        /* WhatsApp Web Style Messenger UI */
        .messenger-wrapper-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px !important;
            box-shadow: 0 6px 24px -4px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            margin-top: 8px;
            margin-bottom: 16px;
        }
        .messenger {
            border-radius: 18px !important;
            border: none !important;
            background: #ffffff !important;
            height: calc(100vh - 175px) !important;
            min-height: 580px !important;
            display: flex !important;
        }
        .messenger-listView {
            border-right: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            width: 340px !important;
            min-width: 300px !important;
            max-width: 360px !important;
            display: flex !important;
            flex-direction: column !important;
        }
        .messenger-listView .m-header {
            padding: 14px 16px 10px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background: #ffffff !important;
            position: static !important;
        }
        .messenger-listView .m-body {
            flex: 1 !important;
            overflow-y: auto !important;
            height: auto !important;
            position: relative !important;
        }
        .messenger-tab {
            display: none;
            height: 100% !important;
        }
        .messenger-tab.show {
            display: block !important;
        }
        .wa-sidebar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .wa-sidebar-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .wa-new-group-btn {
            background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(1, 138, 200, 0.25);
            transition: transform 0.15s ease;
        }
        .wa-new-group-btn:hover {
            transform: translateY(-1px);
        }
        .messenger-search {
            background: #f1f5f9 !important;
            border: 1px solid transparent !important;
            border-radius: 10px !important;
            padding: 9px 14px !important;
            font-size: 13px !important;
            color: #0f172a !important;
            width: 100% !important;
            margin: 0 0 10px !important;
            transition: border-color 0.2s, background 0.2s !important;
        }
        .messenger-search:focus {
            border-color: #018ac8 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(1, 138, 200, 0.1) !important;
        }
        .messenger-listView-tabs {
            display: flex !important;
            gap: 8px !important;
            border: none !important;
            background: #f1f5f9 !important;
            padding: 4px !important;
            border-radius: 10px !important;
        }
        .messenger-listView-tabs a {
            flex: 1 !important;
            text-align: center !important;
            font-weight: 600 !important;
            font-size: 12.5px !important;
            color: #64748b !important;
            padding: 7px 10px !important;
            border-radius: 8px !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
        }
        .messenger-listView-tabs a.active-tab {
            background: #ffffff !important;
            color: #018ac8 !important;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08) !important;
            border-bottom: none !important;
        }
        .messenger-title {
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: 0.6px !important;
            color: #94a3b8 !important;
            padding: 10px 16px 4px !important;
        }
        .messenger-list-item {
            width: calc(100% - 16px) !important;
            border-radius: 12px !important;
            margin: 3px 8px !important;
            padding: 10px 12px !important;
            transition: background 0.15s ease !important;
        }
        .messenger-list-item:hover {
            background: #f8fafc !important;
        }
        .m-list-active, .m-list-active:hover, .m-list-active:focus {
            background: #e6f4fa !important;
            box-shadow: none !important;
        }
        .m-list-active p, .m-list-active span, .m-list-active small {
            color: #0f172a !important;
        }
        .avatar {
            background-color: #e2e8f0 !important;
            background-size: cover !important;
            background-position: center !important;
        }
        .messenger-messagingView {
            flex: 1 !important;
            display: flex !important;
            flex-direction: column !important;
            background: #efeae2 !important;
            background-image: radial-gradient(rgba(15, 23, 42, 0.04) 1px, transparent 1px) !important;
            background-size: 18px 18px !important;
        }
        .m-header-messaging {
            background: #f0f2f5 !important;
            padding: 12px 20px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            position: static !important;
        }
        .m-header-messaging .user-name,
        .m-header-messaging a {
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 15px !important;
        }
        .m-header-messaging .m-header-right a i {
            color: #475569 !important;
            font-size: 18px !important;
        }
        .m-header-messaging .m-header-right a:hover i {
            color: #018ac8 !important;
        }
        .messenger-messagingView .m-body {
            flex: 1 !important;
            background: transparent !important;
            overflow-y: auto !important;
            padding-bottom: 14px !important;
        }
        .message-hint {
            margin-top: 22% !important;
        }
        .message-hint span {
            background: rgba(255, 255, 255, 0.92) !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            padding: 12px 24px !important;
            border-radius: 30px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06) !important;
        }
        .mc-sender p {
            background: #d9fdd3 !important;
            color: #111b21 !important;
            border-radius: 12px 2px 12px 12px !important;
            padding: 9px 13px !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1) !important;
        }
        .mc-sender p sub, .mc-sender p a {
            color: #54656f !important;
        }
        .message-card:not(.mc-sender) p {
            background: #ffffff !important;
            color: #111b21 !important;
            border: none !important;
            border-radius: 2px 12px 12px 12px !important;
            padding: 9px 13px !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1) !important;
        }
        .messenger-sendCard {
            background: #f0f2f5 !important;
            border-top: 1px solid #e2e8f0 !important;
            padding: 10px 16px !important;
            position: static !important;
        }
        .messenger-sendCard form {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 24px !important;
            padding: 6px 12px !important;
        }
        .messenger-sendCard textarea.m-send {
            background: transparent !important;
            border: none !important;
            font-size: 13.5px !important;
            color: #0f172a !important;
            padding: 6px 6px !important;
        }
        .messenger-sendCard button {
            background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%) !important;
            color: #ffffff !important;
            width: 36px !important;
            height: 36px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(1, 138, 200, 0.3) !important;
        }
        .messenger-sendCard label {
            color: #64748b !important;
            margin: 0 !important;
            cursor: pointer !important;
            padding-left: 4px !important;
        }
        .messenger-sendCard label:hover {
            color: #018ac8 !important;
        }
        /* Hide info panel by default until user clicks info icon */
        .messenger-infoView {
            display: none;
            border-left: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
        }
        @if (empty(config('chatify.pusher.key')))
        .internet-connection {
            display: none !important;
        }
        @endif
        .dropdown-menu {
            padding: 15px 0;
            box-shadow: 0 4px 24px 0 rgb(62 57 107 / 18%);
            border: none;
            z-index: 1000;
            min-width: 12rem;
            margin: 0;
            font-size: 0.875rem;
            color: #293240;
            text-align: left;
            background-clip: padding-box;
            border-radius: 10px;
        }
    </style>
@endpush
@php
    // $profile=\App\Models\Utility::get_file('/'.config('chatify.user_avatar.folder'));
    $profile = \App\Models\Utility::get_file('uploads/avatar/');
    $setting = App\Models\Utility::colorset();
    $color = !empty($setting['theme_color']) ? $setting['theme_color'] : 'theme-3';
    $setting = \App\Models\Utility::colorset();
    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $color = 'custom-color';
    } else {
        $color = $color;
    }
    
@endphp
@section('page-title')
    {{ __('Messenger') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Messenger') }}</li>
@endsection

@if ($setting['cust_darklayout'] == 'on')
    <style>
        .cards {
            background-color: #272727;
            padding: 25px 25px;
        }
    </style>
@else
    <style>
        .cards {
            background-color: #fff;
            padding: 25px 25px;
        }
    </style>
@endif

@section('content')
    <div class="messenger-wrapper-card">
        <div class="card-body p-0" style="box-shadow: none;">
            <div class="messenger rounded min-h-750 overflow-hidden">
                {{-- ----------------------Users/Groups lists side---------------------- --}}
                <div class="messenger-listView">
                    {{-- Header and search bar --}}
                    <div class="m-header">
                        <div class="wa-sidebar-top">
                            <h5 class="wa-sidebar-title">{{ __('Chats') }}</h5>
                            <button type="button" class="wa-new-group-btn" id="quickNewGroupBtn">
                                <i class="ti ti-users-plus"></i> {{ __('New Group') }}
                            </button>
                            <a href="#" class="listView-x d-lg-none"><i class="fas fa-times"></i></a>
                        </div>
                        {{-- Search input --}}
                        <input type="text" class="messenger-search" placeholder="{{ __('Search or start new chat') }}" />
                        {{-- Tabs --}}
                        <div class="messenger-listView-tabs">
                            <a href="#" @if ($route == 'user') class="active-tab" @endif data-view="users">
                                <i class="ti ti-message-circle"></i> <span>{{ __('Chats') }}</span>
                            </a>
                            <a href="#" @if ($route == 'group') class="active-tab" @endif data-view="groups">
                                <i class="ti ti-users"></i> <span>{{ __('Groups') }}</span>
                            </a>
                        </div>
                    </div>
                    {{-- tabs and lists --}}
                    <div class="m-body">
                        {{-- Lists [Users/Group] --}}
                        {{-- ---------------- [ User Tab ] ---------------- --}}
                        <div class="@if ($route == 'user') show @endif messenger-tab app-scroll"
                            data-view="users">

                            {{-- Favorites --}}
                            <p class="messenger-title">Favorites</p>
                            <div class="messenger-favorites app-scroll-thin"></div>

                            {{-- Saved Messages --}}
                            {!! view('Chatify::layouts.listItem', ['get' => 'saved', 'id' => $id])->render() !!}

                            {{-- Contact --}}
                            <div class="listOfContacts" style="width: 100%;height: calc(100% - 200px);position: relative;">
                            </div>
                        </div>

                        {{-- ---------------- [ Group Tab ] ---------------- --}}
                        <div class="all_members @if ($route == 'group') show @endif messenger-tab app-scroll"
                            data-view="groups">
                            <div class="p-3 pb-2">
                                <button type="button" class="btn w-100 text-white fw-semibold d-flex align-items-center justify-content-center gap-2"
                                    id="openCreateGroupModalBtn"
                                    style="background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%); border-radius: 12px; padding: 9px 14px; font-size: 13px; border: none; box-shadow: 0 3px 10px rgba(1, 138, 200, 0.25);">
                                    <i class="ti ti-users-plus"></i> {{ __('New Group') }}
                                </button>
                            </div>
                            <div id="chatGroupsList" class="px-1 pb-3">
                                <p class="text-center text-muted small py-4 mb-0">{{ __('Loading groups...') }}</p>
                            </div>
                        </div>

                        {{-- ---------------- [ Search Tab ] ---------------- --}}
                        <div class="messenger-tab app-scroll" data-view="search">
                            {{-- items --}}
                            <p class="messenger-title">{{ __('Search') }}</p>
                            <div class="search-records">
                                <p class="message-hint center-el"><span>{{ __('Type to search..') }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ----------------------Messaging side---------------------- --}}
                <div class="messenger-messagingView">
                    {{-- header title [conversation name] amd buttons --}}
                    <div class="m-header m-header-messaging">
                        <nav class="d-flex a;align-items-center justify-content-between">
                            {{-- header back button, avatar and user name --}}
                            <div style="display: flex; align-items: center;">
                                <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i> </a>
                                @if (!empty($user->avatar))
                                    <div class="avatar av-s header-avatar"
                                        style="margin: 0px 10px; background-image: url('{{ $profile . '/' . $user->avatar }}');">
                                    </div>
                                @else
                                    <div class="avatar av-s header-avatar d-flex align-items-center justify-content-center"
                                        style="margin: 0px 10px; background: rgba(56, 189, 248, 0.16) !important; color: #38bdf8;">
                                        <i class="ti ti-message-circle" style="font-size: 18px;"></i>
                                    </div>
                                @endif
                                <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                            </div>
                            {{-- header buttons --}}
                            <nav class="m-header-right">
                                <a href="#" class="add-to-favorite my-lg-1 my-xl-1 mx-lg-1 mx-xl-1"><i
                                        class="fas fa-star"></i></a>
                                <a href="#" class="show-infoSide my-lg-1 my-xl-1 mx-lg-1 mx-xl-2"><i
                                        class="fas fa-info-circle"></i></a>
                            </nav>
                        </nav>
                    </div>
                    {{-- Internet connection --}}
                    <div class="internet-connection">
                        <span class="ic-connected">{{ __('Connected') }}</span>
                        <span class="ic-connecting">{{ __('Connecting...') }}</span>
                        <span class="ic-noInternet">{{ __('Please add pusher settings for using messenger.') }}</span>
                    </div>
                    {{-- Messaging area --}}
                    <div class="m-body app-scroll">
                        <div class="messages">
                            <p class="message-hint" style="margin-top: calc(30% - 126.2px);">
                                <span>{{ __('Please select a chat to start messaging') }}</span>
                            </p>
                        </div>
                        {{-- Typing indicator --}}
                        <div class="typing-indicator">
                            <div class="message-card typing">
                                <p>
                                    <span class="typing-dots">
                                        <span class="dot dot-1"></span>
                                        <span class="dot dot-2"></span>
                                        <span class="dot dot-3"></span>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- Send Message Form (pinned at bottom) --}}
                    @include('Chatify::layouts.sendForm')
                </div>
                {{-- ---------------------- Info side ---------------------- --}}
                <div class="messenger-infoView app-scroll text-center">
                    {{-- nav actions --}}
                    <nav class="text-center">
                        <a href="#"><i class="fas fa-times"></i></a>
                    </nav>
                    {!! view('Chatify::layouts.info')->render() !!}
                </div>
            </div>
        </div>
    </div>

    {{-- Create Group Modal --}}
    <div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none; padding: 16px 22px;">
                    <h5 class="modal-title text-white d-flex align-items-center gap-2">
                        <i class="ti ti-users" style="color: #38bdf8;"></i> {{ __('Create New Group') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">{{ __('Group Name') }} <span class="text-danger">*</span></label>
                        <input type="text" id="newGroupName" class="form-control" placeholder="{{ __('e.g. Development Team, Office Updates...') }}" style="border-radius: 10px;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-dark small">{{ __('Select Members') }} <span class="text-danger">*</span></label>
                        <input type="text" id="groupMemberSearch" class="form-control form-control-sm mb-2" placeholder="{{ __('Search employees...') }}" style="border-radius: 8px;">
                        <div id="createGroupMembersList" style="max-height: 240px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 12px; padding: 6px;"></div>
                    </div>
                    <div id="createGroupError" class="text-danger small mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('Cancel') }}</button>
                    <button type="button" class="btn text-white fw-semibold" id="submitCreateGroupBtn" style="background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%); border-radius: 10px;">
                        <i class="ti ti-check me-1"></i> {{ __('Create Group') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Group Info / Members Modal --}}
    <div class="modal fade" id="groupInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none; padding: 16px 22px;">
                    <h5 class="modal-title text-white d-flex align-items-center gap-2">
                        <i class="ti ti-info-circle" style="color: #38bdf8;"></i> <span id="groupInfoModalTitle">{{ __('Group Info') }}</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="groupAdminControls" style="display: none;" class="mb-3 pb-3 border-bottom">
                        <label class="form-label fw-semibold text-dark small">{{ __('Group Name') }}</label>
                        <div class="input-group mb-3">
                            <input type="text" id="editGroupNameInput" class="form-control" style="border-radius: 10px 0 0 10px;">
                            <button type="button" class="btn text-white" id="saveGroupNameBtn" style="background: #018ac8; border-radius: 0 10px 10px 0;">{{ __('Save') }}</button>
                        </div>
                        <label class="form-label fw-semibold text-dark small">{{ __('Add Member') }}</label>
                        <div class="input-group">
                            <select id="addMemberSelect" class="form-select" style="border-radius: 10px 0 0 10px;"></select>
                            <button type="button" class="btn text-white" id="addMemberToGroupBtn" style="background: #018ac8; border-radius: 0 10px 10px 0;">{{ __('Add') }}</button>
                        </div>
                    </div>
                    <h6 class="fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px;">{{ __('Members') }}</h6>
                    <div id="groupInfoMembersList" style="max-height: 240px; overflow-y: auto;"></div>
                </div>
                <div class="modal-footer border-top px-4 py-3 justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="leaveGroupBtn" style="width:auto; height:auto; padding:6px 14px; border-radius: 8px;">
                        <i class="ti ti-logout me-1"></i> {{ __('Leave Group') }}
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('Chatify::layouts.modals')
    <script>
        (function () {
            const csrfToken = '{{ csrf_token() }}';
            const routes = {
                groupsIndex: '{{ route("chat.groups.index") }}',
                groupsStore: '{{ route("chat.groups.store") }}',
                groupMessages: (id) => `{{ url('/chats/groups') }}/${id}/messages`,
                groupSend: (id) => `{{ url('/chats/groups') }}/${id}/messages`,
                groupUpdate: (id) => `{{ url('/chats/groups') }}/${id}/update`,
                groupLeave: (id) => `{{ url('/chats/groups') }}/${id}/leave`,
            };

            let activeGroupId = null;
            let cachedGroups = [];
            let cachedUsers = [];
            let activeGroupData = null;
            let lastGroupMsgCount = 0;

            const senderColors = ['#018ac8', '#7c3aed', '#059669', '#d97706', '#db2777', '#2563eb'];
            function getSenderColor(senderId) {
                return senderColors[Number(senderId || 0) % senderColors.length];
            }

            function escapeHtml(str) {
                return String(str || '').replace(/[&<>"']/g, function (m) {
                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
                });
            }

            function loadGroups() {
                fetch(routes.groupsIndex, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(res => {
                        if (!res.success) return;
                        cachedGroups = res.groups || [];
                        cachedUsers = res.users || [];
                        renderGroupsList();
                    })
                    .catch(() => {});
            }

            function renderGroupsList() {
                const container = document.getElementById('chatGroupsList');
                if (!container) return;

                if (!cachedGroups.length) {
                    container.innerHTML = `
                        <div class="text-center py-5 px-3 text-muted">
                            <i class="ti ti-users d-block mb-2" style="font-size: 32px; opacity: 0.4;"></i>
                            <p class="small mb-0">No groups yet. Click <strong>New Group</strong> to start!</p>
                        </div>`;
                    return;
                }

                container.innerHTML = cachedGroups.map(g => {
                    const isActive = activeGroupId === g.id;
                    const activeClass = isActive ? 'm-list-active text-white' : '';
                    const subColor = isActive ? 'rgba(255,255,255,0.85)' : '#64748b';
                    const preview = g.last_sender ? `<strong>${escapeHtml(g.last_sender)}:</strong> ${escapeHtml(g.last_message)}` : escapeHtml(g.last_message);
                    const unreadBadge = g.unread_count > 0
                        ? `<span class="badge rounded-pill" style="background: #10b981; color: #fff; font-size: 10px;">${g.unread_count}</span>`
                        : '';

                    return `
                        <div class="messenger-list-item group-list-item d-flex align-items-center gap-2 ${activeClass}" data-group-id="${g.id}" style="cursor: pointer;">
                            <div class="avatar av-m d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="background: ${isActive ? 'rgba(255,255,255,0.22)' : 'linear-gradient(135deg,#e0f2fe,#bae6fd)'} !important; color: ${isActive ? '#fff' : '#018ac8'};">
                                <i class="ti ti-users" style="font-size: 20px;"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-truncate" style="font-size: 13.5px; color: ${isActive ? '#fff' : '#0f172a'};">${escapeHtml(g.name)}</span>
                                    <small style="font-size: 10.5px; color: ${subColor}; flex-shrink: 0;">${escapeHtml(g.last_time)}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="text-truncate" style="font-size: 12px; color: ${subColor}; max-width: 170px;">${preview}</span>
                                    ${unreadBadge}
                                </div>
                            </div>
                        </div>`;
                }).join('');
            }

            function openGroupChat(groupId, isSilentPoll = false) {
                activeGroupId = groupId;
                if (typeof setMessengerId === 'function') setMessengerId(0);
                renderGroupsList();

                // Enable send form
                const form = document.getElementById('message-form');
                if (form) {
                    const ta = form.querySelector('textarea[name="message"]');
                    const btn = form.querySelector('button');
                    const fileInp = form.querySelector('input[type="file"]');
                    if (ta) ta.removeAttribute('readonly');
                    if (btn) btn.removeAttribute('disabled');
                    if (fileInp) fileInp.removeAttribute('disabled');
                }

                fetch(routes.groupMessages(groupId), { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(res => {
                        if (!res.success) return;
                        activeGroupData = res;

                        // Update chat header
                        const headerUser = document.querySelector('.m-header-messaging .user-name');
                        const headerAvatar = document.querySelector('.m-header-messaging .header-avatar');
                        if (headerUser) {
                            headerUser.innerHTML = `${escapeHtml(res.group.name)} <small class="d-block" style="font-size:11px;font-weight:500;color:rgba(255,255,255,0.7);">${res.members.length} members</small>`;
                        }
                        if (headerAvatar) {
                            headerAvatar.style.backgroundImage = 'none';
                            headerAvatar.innerHTML = '<i class="ti ti-users" style="font-size: 18px; color: #38bdf8;"></i>';
                            headerAvatar.classList.add('d-flex', 'align-items-center', 'justify-content-center');
                            headerAvatar.style.background = 'rgba(56, 189, 248, 0.16)';
                        }

                        // Hide 1-to-1 favorite star for groups
                        const favBtn = document.querySelector('.add-to-favorite');
                        if (favBtn) favBtn.style.display = 'none';

                        const messagesBox = document.querySelector('.messenger-messagingView .messages');
                        if (!messagesBox) return;

                        const msgs = res.messages || [];
                        if (isSilentPoll && msgs.length === lastGroupMsgCount) {
                            return;
                        }
                        lastGroupMsgCount = msgs.length;

                        if (!msgs.length) {
                            messagesBox.innerHTML = '<p class="message-hint"><span>No messages in this group yet. Say hello!</span></p>';
                            return;
                        }

                        let lastDate = '';
                        let html = '';
                        msgs.forEach(m => {
                            if (m.date_label !== lastDate) {
                                lastDate = m.date_label;
                                html += `<div class="text-center my-3"><span class="px-3 py-1 rounded-pill" style="background:#e2e8f0;color:#475569;font-size:11px;font-weight:600;">${escapeHtml(lastDate)}</span></div>`;
                            }

                            let attachmentHtml = '';
                            if (m.attachment_url) {
                                if (m.is_image) {
                                    attachmentHtml = `<div class="mb-1"><a href="${m.attachment_url}" target="_blank"><img src="${m.attachment_url}" style="max-width:220px;border-radius:10px;display:block;"></a></div>`;
                                } else {
                                    attachmentHtml = `<div class="mb-1"><a href="${m.attachment_url}" target="_blank" style="color:${m.is_mine ? '#fff' : '#018ac8'};text-decoration:underline;font-size:12px;"><i class="ti ti-file me-1"></i>${escapeHtml(m.attachment_name || 'Download')}</a></div>`;
                                }
                            }

                            if (m.is_mine) {
                                html += `
                                    <div class="message-card mc-sender" data-id="g-${m.id}">
                                        <p>${attachmentHtml}${m.body ? escapeHtml(m.body).replace(/\n/g, '<br>') : ''}
                                            <sub style="display:block;text-align:right;font-size:10px;opacity:0.8;margin-top:4px;">${escapeHtml(m.time)}</sub>
                                        </p>
                                    </div>`;
                            } else {
                                html += `
                                    <div class="message-card" data-id="g-${m.id}">
                                        <p>
                                            <span style="display:block;font-size:11px;font-weight:700;color:${getSenderColor(m.sender_id)};margin-bottom:3px;">${escapeHtml(m.sender_name)}</span>
                                            ${attachmentHtml}${m.body ? escapeHtml(m.body).replace(/\n/g, '<br>') : ''}
                                            <sub style="display:block;text-align:right;font-size:10px;color:#94a3b8;margin-top:4px;">${escapeHtml(m.time)}</sub>
                                        </p>
                                    </div>`;
                            }
                        });

                        messagesBox.innerHTML = html;
                        const scrollBody = document.querySelector('.messenger-messagingView .m-body');
                        if (scrollBody) scrollBody.scrollTop = scrollBody.scrollHeight;
                    });
            }

            function showBsModal(el) {
                if (!el) return;
                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(el).show();
                } else if (window.jQuery) {
                    window.jQuery(el).modal('show');
                }
            }

            function hideBsModal(el) {
                if (!el) return;
                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(el).hide();
                } else if (window.jQuery) {
                    window.jQuery(el).modal('hide');
                }
            }

            function initMessengerGroups() {
                loadGroups();

                // Explicit WhatsApp-style tab switcher (Chats <-> Groups)
                document.querySelectorAll('.messenger-listView-tabs a').forEach(tabLink => {
                    tabLink.addEventListener('click', function (e) {
                        e.preventDefault();
                        const viewName = this.getAttribute('data-view');
                        document.querySelectorAll('.messenger-listView-tabs a').forEach(a => a.classList.remove('active-tab'));
                        this.classList.add('active-tab');
                        document.querySelectorAll('.messenger-listView .messenger-tab').forEach(tab => {
                            tab.style.display = '';
                            if (tab.getAttribute('data-view') === viewName) {
                                tab.classList.add('show');
                            } else {
                                tab.classList.remove('show');
                            }
                        });
                        if (viewName === 'groups') {
                            loadGroups();
                        }
                    });
                });

                // Also wire top "+ New Group" button in sidebar header
                document.getElementById('quickNewGroupBtn')?.addEventListener('click', function () {
                    document.querySelector('.messenger-listView-tabs a[data-view="groups"]')?.click();
                    document.getElementById('openCreateGroupModalBtn')?.click();
                });

                // Click handler for Groups list
                document.addEventListener('click', function (e) {
                    const groupItem = e.target.closest('.group-list-item');
                    if (groupItem) {
                        const gId = Number(groupItem.getAttribute('data-group-id'));
                        lastGroupMsgCount = 0;
                        openGroupChat(gId, false);
                        return;
                    }

                    // If user clicks a 1-to-1 contact, exit group mode
                    const userItem = e.target.closest('.messenger-list-item:not(.group-list-item)');
                    if (userItem) {
                        activeGroupId = null;
                        activeGroupData = null;
                        const favBtn = document.querySelector('.add-to-favorite');
                        if (favBtn) favBtn.style.display = '';
                        renderGroupsList();
                    }
                });

                // Intercept message form submit for Group Chat
                const messageForm = document.getElementById('message-form');
                if (messageForm) {
                    messageForm.addEventListener('submit', function (e) {
                        if (!activeGroupId) return; // Let Chatify handle 1-to-1
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        const ta = messageForm.querySelector('textarea[name="message"]');
                        const fileInp = messageForm.querySelector('input[type="file"]');
                        const msgText = ta ? ta.value.trim() : '';
                        const hasFile = fileInp && fileInp.files && fileInp.files.length > 0;
                        if (!msgText && !hasFile) return;

                        const fd = new FormData();
                        fd.append('_token', csrfToken);
                        fd.append('message', msgText);
                        if (hasFile) fd.append('file', fileInp.files[0]);

                        if (ta) ta.value = '';
                        if (fileInp) fileInp.value = '';

                        fetch(routes.groupSend(activeGroupId), {
                            method: 'POST',
                            body: fd,
                            headers: { 'Accept': 'application/json' }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                openGroupChat(activeGroupId, false);
                                loadGroups();
                            }
                        });
                    }, true);
                }

                // Open Create Group Modal
                const createGroupModalEl = document.getElementById('createGroupModal');
                document.getElementById('openCreateGroupModalBtn')?.addEventListener('click', function () {
                    document.getElementById('newGroupName').value = '';
                    document.getElementById('groupMemberSearch').value = '';
                    document.getElementById('createGroupError').style.display = 'none';

                    const listEl = document.getElementById('createGroupMembersList');
                    listEl.innerHTML = cachedUsers.map(u => `
                        <label class="d-flex align-items-center gap-2 p-2 rounded group-member-row" style="cursor:pointer;" data-name="${escapeHtml(u.name.toLowerCase())}">
                            <input type="checkbox" class="form-check-input m-0 group-member-cb" value="${u.id}">
                            <img src="${escapeHtml(u.avatar)}" class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                            <span class="small fw-semibold text-dark">${escapeHtml(u.name)}</span>
                        </label>
                    `).join('') || '<p class="text-muted small text-center py-3 mb-0">No team members found.</p>';

                    showBsModal(createGroupModalEl);
                });

                document.getElementById('groupMemberSearch')?.addEventListener('input', function () {
                    const q = this.value.toLowerCase().trim();
                    document.querySelectorAll('#createGroupMembersList .group-member-row').forEach(row => {
                        row.style.display = row.getAttribute('data-name').includes(q) ? 'flex' : 'none';
                    });
                });

                document.getElementById('submitCreateGroupBtn')?.addEventListener('click', function () {
                    const name = document.getElementById('newGroupName').value.trim();
                    const memberIds = Array.from(document.querySelectorAll('.group-member-cb:checked')).map(cb => Number(cb.value));
                    const errEl = document.getElementById('createGroupError');

                    if (!name) {
                        errEl.textContent = 'Please enter a group name.';
                        errEl.style.display = 'block';
                        return;
                    }
                    if (!memberIds.length) {
                        errEl.textContent = 'Please select at least 1 member.';
                        errEl.style.display = 'block';
                        return;
                    }

                    fetch(routes.groupsStore, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ name, member_ids: memberIds })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (!res.success) {
                            errEl.textContent = res.message || 'Could not create group.';
                            errEl.style.display = 'block';
                            return;
                        }
                        hideBsModal(createGroupModalEl);
                        loadGroups();
                        openGroupChat(res.group.id, false);
                    });
                });

                // Open Group Info Modal when clicking (i) during active group chat
                const groupInfoModalEl = document.getElementById('groupInfoModal');
                document.querySelector('.show-infoSide')?.addEventListener('click', function (e) {
                    if (!activeGroupId || !activeGroupData) return;
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    document.getElementById('groupInfoModalTitle').textContent = activeGroupData.group.name;
                    document.getElementById('editGroupNameInput').value = activeGroupData.group.name;

                    const isAdmin = activeGroupData.group.my_role === 'admin' || ['company', 'hr'].includes('{{ Auth::user()->type }}');
                    document.getElementById('groupAdminControls').style.display = isAdmin ? 'block' : 'none';

                    const existingIds = activeGroupData.members.map(m => m.id);
                    const nonMembers = cachedUsers.filter(u => !existingIds.includes(u.id));
                    document.getElementById('addMemberSelect').innerHTML = nonMembers.length
                        ? nonMembers.map(u => `<option value="${u.id}">${escapeHtml(u.name)}</option>`).join('')
                        : '<option value="">All users already in group</option>';

                    document.getElementById('groupInfoMembersList').innerHTML = activeGroupData.members.map(m => `
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <img src="${escapeHtml(m.avatar)}" class="rounded-circle" width="34" height="34" style="object-fit:cover;">
                                <span class="small fw-semibold text-dark">${escapeHtml(m.name)}</span>
                            </div>
                            <span class="badge ${m.role === 'admin' ? 'bg-primary' : 'bg-light text-dark'}">${escapeHtml(m.role)}</span>
                        </div>
                    `).join('');

                    showBsModal(groupInfoModalEl);
                }, true);

                document.getElementById('saveGroupNameBtn')?.addEventListener('click', function () {
                    if (!activeGroupId) return;
                    const newName = document.getElementById('editGroupNameInput').value.trim();
                    if (!newName) return;
                    fetch(routes.groupUpdate(activeGroupId), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ name: newName })
                    }).then(() => {
                        hideBsModal(groupInfoModalEl);
                        loadGroups();
                        openGroupChat(activeGroupId, false);
                    });
                });

                document.getElementById('addMemberToGroupBtn')?.addEventListener('click', function () {
                    if (!activeGroupId) return;
                    const userId = document.getElementById('addMemberSelect').value;
                    if (!userId) return;
                    fetch(routes.groupUpdate(activeGroupId), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ add_member_ids: [Number(userId)] })
                    }).then(() => {
                        hideBsModal(groupInfoModalEl);
                        loadGroups();
                        openGroupChat(activeGroupId, false);
                    });
                });

                document.getElementById('leaveGroupBtn')?.addEventListener('click', function () {
                    if (!activeGroupId) return;
                    fetch(routes.groupLeave(activeGroupId), {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    }).then(() => {
                        hideBsModal(groupInfoModalEl);
                        activeGroupId = null;
                        activeGroupData = null;
                        document.querySelector('.messenger-messagingView .messages').innerHTML = '<p class="message-hint"><span>Please select a chat to start messaging</span></p>';
                        loadGroups();
                    });
                });

                // 3-second real-time polling for both Groups & 1-to-1 chats
                setInterval(function () {
                    loadGroups();
                    if (activeGroupId) {
                        openGroupChat(activeGroupId, true);
                    } else if (typeof getMessengerId === 'function' && getMessengerId() && getMessengerId() != 0) {
                        @if (empty(config('chatify.pusher.key')))
                        if (typeof fetchMessages === 'function') {
                            fetchMessages(getMessengerId(), getMessengerType(), false);
                        }
                        if (typeof getContacts === 'function') {
                            getContacts();
                        }
                        @endif
                    }
                }, 3000);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMessengerGroups);
            } else {
                initMessengerGroups();
            }
        })();
    </script>
@endpush

@if ($setting['SITE_RTL'] == 'on')
    <style type="text/css">
        body {

            text-align: right !important;
        }
    </style>
@endif

@if ($color == 'theme-1')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, #0CAF60 3.46%, #0CAF60 99.86%), #0CAF60 !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, #0CAF60 3.46%, #0CAF60 99.86%), #0CAF60 !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #0CAF60 !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, #0CAF60 3.46%, #0CAF60 99.86%), #0CAF60 !important;
        }

        .m-header svg {
            color: #0CAF60 !important;
        }

        .active-tab {
            border-bottom: 2px solid #0CAF60 !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, #0CAF60 3.46%, #0CAF60 99.86%), #0CAF60 !important;
        }

        .lastMessageIndicator {
            color: #0CAF60 !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #0CAF60 !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #0CAF60 !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-2')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, #584ED2 3.46%, #584ED2 99.86%), #584ED2 !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, #584ED2 3.46%, #584ED2 99.86%), #584ED2 !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #584ED2 !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, #584ED2 3.46%, #584ED2 99.86%), #584ED2 !important;
        }

        .m-header svg {
            color: #584ED2 !important;
        }

        .active-tab {
            border-bottom: 2px solid #584ED2 !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, #584ED2 3.46%, #584ED2 99.86%), #584ED2 !important;
        }

        .lastMessageIndicator {
            color: #584ED2 !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #584ED2 !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #584ED2 !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-3')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, #6fd943 3.46%, #6fd943 99.86%), #6fd943 !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, #6fd943 3.46%, #6fd943 99.86%), #6fd943 !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #6fd943 !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, #6fd943 3.46%, #6fd943 99.86%), #6fd943 !important;
        }

        .m-header svg {
            color: #6fd943 !important;
        }

        .active-tab {
            border-bottom: 2px solid #6fd943 !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, #6fd943 3.46%, #6fd943 99.86%), #6fd943 !important;
        }

        .lastMessageIndicator {
            color: #6fd943 !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #6fd943 !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #6fd943 !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-4')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, #145388 3.46%, #145388 99.86%), #145388 !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, #145388 3.46%, #145388 99.86%), #145388 !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #145388 !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, #145388 3.46%, #145388 99.86%), #145388 !important;
        }

        .m-header svg {
            color: #145388 !important;
        }

        .active-tab {
            border-bottom: 2px solid #145388 !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, #145388 3.46%, #145388 99.86%), #145388 !important;
        }

        .lastMessageIndicator {
            color: #145388 !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #145388 !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #145388 !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-5')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #B9406B 99.86%), #B9406B !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #B9406B 99.86%), #B9406B !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #B9406B !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #B9406B 99.86%), #B9406B !important;
        }

        .m-header svg {
            color: #B9406B !important;
        }

        .active-tab {
            border-bottom: 2px solid #B9406B !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #B9406B 99.86%), #B9406B !important;
        }

        .lastMessageIndicator {
            color: #B9406B !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #B9406B !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #B9406B !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-6')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #008ECC 99.86%), #008ECC !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #008ECC 99.86%), #008ECC !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #008ECC !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #008ECC 99.86%), #008ECC !important;
        }

        .m-header svg {
            color: #008ECC !important;
        }

        .active-tab {
            border-bottom: 2px solid #008ECC !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #008ECC 99.86%), #008ECC !important;
        }

        .lastMessageIndicator {
            color: #008ECC !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #008ECC !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #008ECC !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-7')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #922C88 99.86%), #922C88 !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #922C88 99.86%), #922C88 !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #922C88 !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #922C88 99.86%), #922C88 !important;
        }

        .m-header svg {
            color: #922C88 !important;
        }

        .active-tab {
            border-bottom: 2px solid #922C88 !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #922C88 99.86%), #922C88 !important;
        }

        .lastMessageIndicator {
            color: #922C88 !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #922C88 !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #922C88 !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-8')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #C0A145 99.86%), #C0A145 !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #C0A145 99.86%), #C0A145 !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #C0A145 !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #C0A145 99.86%), #C0A145 !important;
        }

        .m-header svg {
            color: #C0A145 !important;
        }

        .active-tab {
            border-bottom: 2px solid #C0A145 !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #C0A145 99.86%), #C0A145 !important;
        }

        .lastMessageIndicator {
            color: #C0A145 !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #C0A145 !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #C0A145 !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-9')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #48494B 99.86%), #48494B !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #48494B 99.86%), #48494B !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #48494B !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #48494B 99.86%), #48494B !important;
        }

        .m-header svg {
            color: #48494B !important;
        }

        .active-tab {
            border-bottom: 2px solid #48494B !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #48494B 99.86%), #48494B !important;
        }

        .lastMessageIndicator {
            color: #48494B !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #48494B !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #48494B !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'theme-10')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #0C7785 99.86%), #0C7785 !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #0C7785 99.86%), #0C7785 !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px #0C7785 !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #0C7785 99.86%), #0C7785 !important;
        }

        .m-header svg {
            color: #0C7785 !important;
        }

        .active-tab {
            border-bottom: 2px solid #0C7785 !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, rgba(104, 94, 229, 0) 3.46%, #0C7785 99.86%), #0C7785 !important;
        }

        .lastMessageIndicator {
            color: #0C7785 !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: #0C7785 !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: #0C7785 !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif

@if ($color == 'custom-color')
    <style type="text/css">
        .m-list-active,
        .m-list-active:hover,
        .m-list-active:focus {
            background: linear-gradient(141.55deg, var(--color-customColor) 3.46%, var(--color-customColor) 99.86%), var(--color-customColor) !important;
        }

        .mc-sender p {
            background: linear-gradient(141.55deg, var(--color-customColor) 3.46%, var(--color-customColor) 99.86%), var(--color-customColor) !important;
        }

        .messenger-favorites div.avatar {
            box-shadow: 0px 0px 0px 2px var(--color-customColor) !important;
        }

        .messenger-listView-tabs a,
        .messenger-listView-tabs a:hover,
        .messenger-listView-tabs a:focus {
            color: linear-gradient(141.55deg, var(--color-customColor) 3.46%, var(--color-customColor) 99.86%), var(--color-customColor) !important;
        }

        .m-header svg {
            color: var(--color-customColor) !important;
        }

        .active-tab {
            border-bottom: 2px solid var(--color-customColor) !important;
        }

        .messenger-infoView nav a {

            color: linear-gradient(141.55deg, var(--color-customColor) 3.46%, var(--color-customColor) 99.86%), var(--color-customColor) !important;
        }

        .lastMessageIndicator {
            color: var(--color-customColor) !important;
        }

        .messenger-list-item td span .lastMessageIndicator {

            color: var(--color-customColor) !important;
            font-weight: bold;
        }

        .messenger-sendCard button svg {
            color: var(--color-customColor) !important;
        }

        .messenger-list-item.m-list-active td span .lastMessageIndicator {
            color: #fff !important;
        }
    </style>
@endif