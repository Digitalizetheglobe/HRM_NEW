@extends('layouts.admin')

@section('page-title')
    {{ __('Messenger') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Messenger') }}</li>
@endsection

@section('content')
<style>
    .wa-shell {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 8px 30px -6px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        display: flex;
        height: calc(100vh - 185px);
        min-height: 580px;
        margin-top: 8px;
    }

    /* Left Sidebar */
    .wa-sidebar {
        width: 340px;
        min-width: 290px;
        max-width: 360px;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }
    .wa-sidebar-header {
        background: #f0f2f5;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }
    .wa-sidebar-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .wa-new-group-btn {
        background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%);
        color: #ffffff;
        border: none;
        border-radius: 20px;
        padding: 6px 13px;
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
    .wa-search-wrap {
        padding: 10px 14px 8px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .wa-search-input {
        width: 100%;
        background: #f0f2f5;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        color: #0f172a;
        outline: none;
        transition: all 0.2s;
    }
    .wa-search-input:focus {
        background: #ffffff;
        border-color: #018ac8;
        box-shadow: 0 0 0 3px rgba(1, 138, 200, 0.1);
    }
    .wa-tabs {
        display: flex;
        gap: 6px;
        padding: 8px 14px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }
    .wa-tab-btn {
        flex: 1;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        font-size: 12.5px;
        font-weight: 600;
        padding: 7px 10px;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .wa-tab-btn.active {
        background: #e6f4fa;
        color: #018ac8;
    }
    .wa-list {
        flex: 1;
        overflow-y: auto;
        padding: 6px 8px;
    }
    .wa-list::-webkit-scrollbar,
    .wa-messages::-webkit-scrollbar {
        width: 5px;
    }
    .wa-list::-webkit-scrollbar-thumb,
    .wa-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .wa-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: background 0.15s ease;
        margin-bottom: 2px;
    }
    .wa-item:hover {
        background: #f8fafc;
    }
    .wa-item.active {
        background: #e6f4fa;
    }
    .wa-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        background: #e2e8f0;
    }
    .wa-group-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        flex-shrink: 0;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #018ac8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .wa-item-body {
        flex: 1;
        min-width: 0;
    }
    .wa-item-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
    }
    .wa-item-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wa-item-time {
        font-size: 11px;
        color: #64748b;
        flex-shrink: 0;
    }
    .wa-item-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
    }
    .wa-item-preview {
        font-size: 12.5px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wa-unread-pill {
        background: #25d366;
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Right Chat Pane */
    .wa-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #efeae2;
        background-image: radial-gradient(rgba(15, 23, 42, 0.045) 1px, transparent 1px);
        background-size: 18px 18px;
        min-width: 0;
    }
    .wa-chat-header {
        background: #f0f2f5;
        padding: 12px 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 66px;
    }
    .wa-chat-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .wa-chat-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .wa-chat-sub {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }
    .wa-messages {
        flex: 1;
        overflow-y: auto;
        padding: 18px 24px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .wa-empty-state {
        margin: auto;
        text-align: center;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 28px 34px;
        max-width: 360px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }
    .wa-date-divider {
        align-self: center;
        background: rgba(255, 255, 255, 0.92);
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 14px;
        border-radius: 20px;
        margin: 10px 0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }
    .wa-msg-row {
        display: flex;
        flex-direction: column;
        max-width: 68%;
    }
    .wa-msg-row.mine {
        align-self: flex-end;
        align-items: flex-end;
    }
    .wa-msg-row.theirs {
        align-self: flex-start;
        align-items: flex-start;
    }
    .wa-bubble {
        padding: 8px 13px 6px;
        border-radius: 12px;
        font-size: 13.5px;
        line-height: 1.45;
        color: #111b21;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
        word-break: break-word;
    }
    .wa-msg-row.mine .wa-bubble {
        background: #d9fdd3;
        border-top-right-radius: 2px;
    }
    .wa-msg-row.theirs .wa-bubble {
        background: #ffffff;
        border-top-left-radius: 2px;
    }
    .wa-sender-name {
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        margin-bottom: 3px;
    }
    .wa-msg-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        font-size: 10px;
        color: #667781;
        margin-top: 3px;
    }

    /* Bottom Composer */
    .wa-composer {
        background: #f0f2f5;
        border-top: 1px solid #e2e8f0;
        padding: 10px 16px;
        position: relative;
    }
    .wa-emoji-popover {
        position: absolute;
        bottom: 68px;
        left: 16px;
        width: 310px;
        max-height: 230px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
        padding: 10px;
        display: none;
        z-index: 50;
    }
    .wa-emoji-popover.open {
        display: block;
    }
    .wa-emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 4px;
    }
    .wa-emoji-item {
        border: none;
        background: transparent;
        font-size: 20px;
        line-height: 1;
        padding: 6px 4px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.12s ease, transform 0.12s ease;
    }
    .wa-emoji-item:hover {
        background: #f1f5f9;
        transform: scale(1.15);
    }
    .wa-composer-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 26px;
        padding: 6px 10px 6px 14px;
    }
    .wa-attach-btn {
        color: #64748b;
        font-size: 19px;
        cursor: pointer;
        display: flex;
        align-items: center;
        margin: 0;
    }
    .wa-attach-btn:hover {
        color: #018ac8;
    }
    .wa-input {
        flex: 1;
        border: none;
        outline: none;
        resize: none;
        font-size: 13.5px;
        color: #0f172a;
        background: transparent;
        max-height: 90px;
        line-height: 1.4;
        padding: 4px 0;
    }
    .wa-send-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        cursor: pointer;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(1, 138, 200, 0.28);
    }
    .wa-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    #waMobileBackBtn {
        display: none !important;
    }
    @media (max-width: 768px) {
        #waMobileBackBtn { display: inline-flex !important; }
        .wa-sidebar { width: 100%; max-width: 100%; }
        .wa-main.mobile-hidden { display: none; }
        .wa-sidebar.mobile-hidden { display: none; }
    }
</style>

<div class="wa-shell">
    {{-- Left Sidebar --}}
    <div class="wa-sidebar" id="waSidebar">
        <div class="wa-sidebar-header">
            <h5 class="wa-sidebar-title">
                <i class="ti ti-messages" style="color: #018ac8; font-size: 21px;"></i>
                <span>{{ __('Chats') }}</span>
            </h5>
            @if (Auth::user()->type === 'company')
            <button type="button" class="wa-new-group-btn" id="openNewGroupBtn">
                <i class="ti ti-users-plus"></i> {{ __('New Group') }}
            </button>
            @endif
        </div>

        <div class="wa-search-wrap">
            <input type="text" class="wa-search-input" id="waSearchInput" placeholder="{{ __('Search active employees or groups...') }}">
        </div>

        <div class="wa-tabs">
            <button type="button" class="wa-tab-btn active" data-tab="chats">
                <i class="ti ti-message-circle"></i> {{ __('All Chats') }}
            </button>
            <button type="button" class="wa-tab-btn" data-tab="groups">
                <i class="ti ti-users"></i> {{ __('Groups') }}
            </button>
            <button type="button" class="wa-tab-btn" data-tab="employees">
                <i class="ti ti-user-check"></i> {{ __('Employees') }}
            </button>
        </div>

        <div class="wa-list" id="waListContainer">
            <p class="text-center text-muted small py-5 mb-0">{{ __('Loading...') }}</p>
        </div>
    </div>

    {{-- Right Chat Window --}}
    <div class="wa-main" id="waMain">
        <div class="wa-chat-header">
            <div class="wa-chat-header-left">
                <button type="button" class="btn btn-sm btn-light d-md-none" id="waMobileBackBtn" style="width:auto;height:auto;padding:5px 10px;">
                    <i class="ti ti-arrow-left"></i>
                </button>
                <div id="waHeaderAvatar" class="wa-group-avatar">
                    <i class="ti ti-message-circle"></i>
                </div>
                <div class="overflow-hidden">
                    <h6 class="wa-chat-title" id="waHeaderTitle">{{ __('HRM Messenger') }}</h6>
                    <p class="wa-chat-sub" id="waHeaderSub">{{ __('Select an employee or group to chat') }}</p>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-light d-none align-items-center gap-1" id="waGroupInfoBtn" style="border-radius:10px; padding: 6px 14px; font-size: 12.5px; font-weight: 600; white-space: nowrap;" title="{{ __('Group Info') }}">
                    <i class="ti ti-info-circle"></i> <span>{{ __('Group Info') }}</span>
                </button>
            </div>
        </div>

        <div class="wa-messages" id="waMessagesBox">
            <div class="wa-empty-state">
                <i class="ti ti-messages" style="font-size: 44px; color: #018ac8; opacity: 0.7;"></i>
                <h6 class="fw-bold text-dark mt-2 mb-1">{{ __('Welcome to HRM Messenger') }}</h6>
                <p class="text-muted small mb-0">{{ __('Select any active employee or create a WhatsApp-style group to start messaging.') }}</p>
            </div>
        </div>

        <div class="wa-composer" id="waComposer" style="display: none;">
            <div class="wa-emoji-popover" id="waEmojiPopover">
                <div class="wa-emoji-grid" id="waEmojiGrid"></div>
            </div>
            <div id="waSelectedFileBadge" class="small text-primary fw-semibold mb-2 px-2" style="display: none;"></div>
            <form id="waMessageForm" class="wa-composer-box">
                <button type="button" class="wa-attach-btn border-0 bg-transparent p-0" id="waEmojiToggleBtn" title="{{ __('Emoji') }}">
                    <i class="ti ti-mood-smile"></i>
                </button>
                <label class="wa-attach-btn" title="{{ __('Attach file') }}">
                    <i class="ti ti-paperclip"></i>
                    <input type="file" id="waFileInput" class="d-none">
                </label>
                <textarea id="waMessageInput" class="wa-input" rows="1" placeholder="{{ __('Type a message...') }}"></textarea>
                <button type="submit" class="wa-send-btn" id="waSendBtn">
                    <i class="ti ti-send"></i>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Create Group Modal --}}
<div class="modal fade" id="waCreateGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none; padding: 16px 22px;">
                <h5 class="modal-title text-white d-flex align-items-center gap-2">
                    <i class="ti ti-users-plus" style="color: #38bdf8;"></i> {{ __('Create New Group') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark small">{{ __('Group Name') }} <span class="text-danger">*</span></label>
                    <input type="text" id="waNewGroupName" class="form-control" placeholder="{{ __('e.g. Sales Team, Office Updates...') }}" style="border-radius: 10px;">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold text-dark small">{{ __('Select Active Employees') }} <span class="text-danger">*</span></label>
                    <input type="text" id="waMemberSearch" class="form-control form-control-sm mb-2" placeholder="{{ __('Search employees...') }}" style="border-radius: 8px;">
                    <div id="waCreateMembersList" style="max-height: 240px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 12px; padding: 6px;"></div>
                </div>
                <div id="waCreateGroupErr" class="text-danger small mt-2" style="display: none;"></div>
            </div>
            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('Cancel') }}</button>
                <button type="button" class="btn text-white fw-semibold" id="waSubmitGroupBtn" style="background: linear-gradient(135deg, #018ac8 0%, #026b9a 100%); border-radius: 10px;">
                    <i class="ti ti-check me-1"></i> {{ __('Create Group') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Group Info Modal --}}
<div class="modal fade" id="waGroupInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none; padding: 16px 22px;">
                <h5 class="modal-title text-white d-flex align-items-center gap-2">
                    <i class="ti ti-info-circle" style="color: #38bdf8;"></i> <span id="waGroupInfoTitle">{{ __('Group Info') }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="waGroupAdminBox" style="display: none;" class="mb-3 pb-3 border-bottom">
                    <label class="form-label fw-semibold text-dark small">{{ __('Rename Group') }}</label>
                    <div class="input-group mb-3">
                        <input type="text" id="waEditGroupName" class="form-control" style="border-radius: 10px 0 0 10px;">
                        <button type="button" class="btn text-white" id="waSaveGroupNameBtn" style="background: #018ac8; border-radius: 0 10px 10px 0;">{{ __('Save') }}</button>
                    </div>
                    <label class="form-label fw-semibold text-dark small">{{ __('Add Active Employee') }}</label>
                    <div class="input-group">
                        <select id="waAddMemberSelect" class="form-select" style="border-radius: 10px 0 0 10px;"></select>
                        <button type="button" class="btn text-white" id="waAddMemberBtn" style="background: #018ac8; border-radius: 0 10px 10px 0;">{{ __('Add') }}</button>
                    </div>
                </div>
                <h6 class="fw-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px;">{{ __('Members') }}</h6>
                <div id="waGroupMembersList" style="max-height: 240px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer border-top px-4 py-3 justify-content-between">
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1" id="waLeaveGroupBtn" style="padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                    <i class="ti ti-logout"></i> <span>{{ __('Leave Group') }}</span>
                </button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const csrfToken = '{{ csrf_token() }}';
    const initialUserId = {{ $initialUserId ? (int) $initialUserId : 'null' }};
    const isCompanyUser = @json(Auth::user()->type === 'company');
    const isCompanyOrHr = @json(in_array(Auth::user()->type, ['company', 'hr']));

    const endpoints = {
        data: '{{ route("chats.data") }}',
        directMessages: (id) => `{{ url('/chats/direct') }}/${id}/messages`,
        directSend: (id) => `{{ url('/chats/direct') }}/${id}/messages`,
        groupsStore: '{{ route("chat.groups.store") }}',
        groupMessages: (id) => `{{ url('/chats/groups') }}/${id}/messages`,
        groupSend: (id) => `{{ url('/chats/groups') }}/${id}/messages`,
        groupUpdate: (id) => `{{ url('/chats/groups') }}/${id}/update`,
        groupLeave: (id) => `{{ url('/chats/groups') }}/${id}/leave`,
    };

    let state = {
        tab: 'chats',
        search: '',
        users: [],
        groups: [],
        activeType: null, // 'user' | 'group'
        activeId: null,
        activeGroupMeta: null,
        lastMsgSignature: '',
    };

    const defaultAvatarUrl = '{{ asset("storage/uploads/attachments/avtar.png") }}';
    const senderColors = ['#018ac8', '#7c3aed', '#059669', '#d97706', '#db2777', '#2563eb'];
    const esc = (s) => String(s || '').replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));

    function showModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).show();
        } else if (window.jQuery) {
            window.jQuery(el).modal('show');
        }
    }
    function hideModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).hide();
        } else if (window.jQuery) {
            window.jQuery(el).modal('hide');
        }
    }

    function loadData(openInitial = false) {
        fetch(endpoints.data, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                state.users = res.users || [];
                state.groups = res.groups || [];
                renderList();
                const initialGroupId = Number(new URLSearchParams(window.location.search).get('group_id')) || null;
                if (openInitial) {
                    if (initialGroupId) {
                        openChat('group', initialGroupId);
                    } else if (initialUserId) {
                        openChat('user', initialUserId);
                    }
                }
            })
            .catch(() => {});
    }

    function renderList() {
        const box = document.getElementById('waListContainer');
        const q = state.search.toLowerCase().trim();
        let items = [];

        if (state.tab === 'groups') {
            items = state.groups.map(g => ({ kind: 'group', ...g }));
        } else if (state.tab === 'employees') {
            items = state.users.map(u => ({ kind: 'user', ...u })).sort((a, b) => a.name.localeCompare(b.name));
        } else {
            const gItems = state.groups.map(g => ({ kind: 'group', ...g }));
            const uItems = state.users.map(u => ({ kind: 'user', ...u }));
            items = [...gItems, ...uItems].sort((a, b) => (b.sort_ts || 0) - (a.sort_ts || 0));
        }

        if (q) {
            items = items.filter(i => i.name.toLowerCase().includes(q));
        }

        if (!items.length) {
            const emptyGroupMsg = isCompanyUser ? 'No groups yet. Click <strong>New Group</strong>!' : 'No groups yet.';
            box.innerHTML = `<div class="text-center text-muted small py-5">${state.tab === 'groups' ? emptyGroupMsg : 'No active employees found.'}</div>`;
            return;
        }

        box.innerHTML = items.map(item => {
            const isActive = state.activeType === item.kind && state.activeId === item.id;
            const avatarHtml = item.kind === 'group'
                ? `<div class="wa-group-avatar"><i class="ti ti-users"></i></div>`
                : `<img src="${esc(item.avatar)}" onerror="this.onerror=null;this.src='${defaultAvatarUrl}';" class="wa-avatar" alt="">`;
            const preview = item.kind === 'group' && item.last_sender
                ? `<strong>${esc(item.last_sender)}:</strong> ${esc(item.last_message)}`
                : esc(item.last_message);
            const unread = item.unread_count > 0
                ? `<span class="wa-unread-pill">${item.unread_count}</span>`
                : '';

            return `
                <div class="wa-item ${isActive ? 'active' : ''}" data-kind="${item.kind}" data-id="${item.id}">
                    ${avatarHtml}
                    <div class="wa-item-body">
                        <div class="wa-item-top">
                            <span class="wa-item-name">${esc(item.name)}</span>
                            <span class="wa-item-time">${esc(item.last_time)}</span>
                        </div>
                        <div class="wa-item-bottom">
                            <span class="wa-item-preview">${preview}</span>
                            ${unread}
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    function openChat(kind, id, isSilent = false) {
        if (!isSilent) {
            state.activeType = kind;
            state.activeId = id;
            state.lastMsgSignature = '';
            renderList();
            document.getElementById('waComposer').style.display = 'block';
            document.getElementById('waSidebar').classList.add('mobile-hidden');
            document.getElementById('waMain').classList.remove('mobile-hidden');
        }

        const url = kind === 'group' ? endpoints.groupMessages(id) : endpoints.directMessages(id);
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;

                const hAvatar = document.getElementById('waHeaderAvatar');
                const hTitle = document.getElementById('waHeaderTitle');
                const hSub = document.getElementById('waHeaderSub');
                const infoBtn = document.getElementById('waGroupInfoBtn');

                if (kind === 'group') {
                    state.activeGroupMeta = res;
                    hAvatar.outerHTML = `<div id="waHeaderAvatar" class="wa-group-avatar"><i class="ti ti-users"></i></div>`;
                    hTitle.textContent = res.group.name;
                    hSub.textContent = `${res.members.length} members`;
                    infoBtn.classList.remove('d-none');
                    infoBtn.classList.add('d-inline-flex');
                } else {
                    state.activeGroupMeta = null;
                    hAvatar.outerHTML = `<img id="waHeaderAvatar" src="${esc(res.user.avatar)}" onerror="this.onerror=null;this.src='${defaultAvatarUrl}';" class="wa-avatar" alt="">`;
                    hTitle.textContent = res.user.name;
                    hSub.textContent = res.user.type || 'Employee';
                    infoBtn.classList.add('d-none');
                    infoBtn.classList.remove('d-inline-flex');
                }

                const msgs = res.messages || [];
                const sig = `${kind}:${id}:${msgs.length}:${msgs.length ? msgs[msgs.length - 1].id : 0}`;
                if (isSilent && sig === state.lastMsgSignature) return;
                state.lastMsgSignature = sig;

                const box = document.getElementById('waMessagesBox');
                if (!msgs.length) {
                    box.innerHTML = `<div class="wa-empty-state"><p class="text-muted small mb-0">No messages yet. Say hello 👋</p></div>`;
                    return;
                }

                let lastDate = '';
                let html = '';
                msgs.forEach(m => {
                    if (m.date_label !== lastDate) {
                        lastDate = m.date_label;
                        html += `<div class="wa-date-divider">${esc(lastDate)}</div>`;
                    }
                    let attHtml = '';
                    if (m.attachment_url) {
                        attHtml = m.is_image
                            ? `<div class="mb-1"><a href="${m.attachment_url}" target="_blank"><img src="${m.attachment_url}" style="max-width:240px;border-radius:10px;display:block;"></a></div>`
                            : `<div class="mb-1"><a href="${m.attachment_url}" target="_blank" style="color:#018ac8;text-decoration:underline;font-size:12.5px;"><i class="ti ti-file me-1"></i>${esc(m.attachment_name || 'Download')}</a></div>`;
                    }
                    const senderHtml = (kind === 'group' && !m.is_mine)
                        ? `<span class="wa-sender-name" style="color:${senderColors[Number(m.sender_id || 0) % senderColors.length]};">${esc(m.sender_name)}</span>`
                        : '';
                    const ticksHtml = m.is_mine
                        ? `<i class="ti ti-checks" style="font-size:13px;color:${m.seen ? '#53bdeb' : '#8696a0'};"></i>`
                        : '';

                    html += `
                        <div class="wa-msg-row ${m.is_mine ? 'mine' : 'theirs'}">
                            <div class="wa-bubble">
                                ${senderHtml}
                                ${attHtml}
                                ${m.body ? `<div>${esc(m.body).replace(/\n/g, '<br>')}</div>` : ''}
                                <div class="wa-msg-meta">
                                    <span>${esc(m.time)}</span>
                                    ${ticksHtml}
                                </div>
                            </div>
                        </div>`;
                });

                box.innerHTML = html;
                box.scrollTop = box.scrollHeight;
            });
    }

    function sendMessage() {
        if (!state.activeType || !state.activeId) return;
        const input = document.getElementById('waMessageInput');
        const fileInp = document.getElementById('waFileInput');
        const text = input.value.trim();
        const file = fileInp.files && fileInp.files[0] ? fileInp.files[0] : null;
        if (!text && !file) return;

        const fd = new FormData();
        fd.append('_token', csrfToken);
        fd.append('message', text);
        if (file) fd.append('file', file);

        input.value = '';
        fileInp.value = '';
        document.getElementById('waSelectedFileBadge').style.display = 'none';

        const url = state.activeType === 'group'
            ? endpoints.groupSend(state.activeId)
            : endpoints.directSend(state.activeId);

        fetch(url, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    openChat(state.activeType, state.activeId, false);
                    loadData(false);
                }
            });
    }

    function init() {
        loadData(true);

        document.getElementById('waSearchInput').addEventListener('input', function () {
            state.search = this.value;
            renderList();
        });

        document.querySelectorAll('.wa-tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.wa-tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                state.tab = this.getAttribute('data-tab');
                renderList();
            });
        });

        document.getElementById('waListContainer').addEventListener('click', function (e) {
            const row = e.target.closest('.wa-item');
            if (!row) return;
            openChat(row.getAttribute('data-kind'), Number(row.getAttribute('data-id')), false);
        });

        document.getElementById('waMobileBackBtn').addEventListener('click', function () {
            document.getElementById('waSidebar').classList.remove('mobile-hidden');
            document.getElementById('waMain').classList.add('mobile-hidden');
        });

        document.getElementById('waFileInput').addEventListener('change', function () {
            const badge = document.getElementById('waSelectedFileBadge');
            if (this.files && this.files[0]) {
                badge.textContent = '📎 ' + this.files[0].name;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        });

        document.getElementById('waMessageInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        document.getElementById('waMessageForm').addEventListener('submit', function (e) {
            e.preventDefault();
            sendMessage();
        });

        // Emoji Picker
        const emojis = [
            '😀','😁','😂','🤣','😊','😇','🙂','😉','😍','🥰','😘','😋','😎','🤩','🥳','🤔',
            '🤨','😐','🙄','😮','😢','😭','😤','😡','👍','👎','👏','🙌','🙏','🤝','💪','✌️',
            '❤️','🧡','💛','💚','💙','💜','🔥','✨','🎉','🎊','✅','❌','⭐','💯','📌','📎'
        ];
        const emojiGrid = document.getElementById('waEmojiGrid');
        const emojiPopover = document.getElementById('waEmojiPopover');
        const emojiToggleBtn = document.getElementById('waEmojiToggleBtn');
        const msgInput = document.getElementById('waMessageInput');

        if (emojiGrid) {
            emojiGrid.innerHTML = emojis.map(em => `<button type="button" class="wa-emoji-item">${em}</button>`).join('');
            emojiGrid.addEventListener('click', function (e) {
                const btn = e.target.closest('.wa-emoji-item');
                if (!btn) return;
                const em = btn.textContent;
                const start = msgInput.selectionStart ?? msgInput.value.length;
                const end = msgInput.selectionEnd ?? msgInput.value.length;
                msgInput.value = msgInput.value.slice(0, start) + em + msgInput.value.slice(end);
                msgInput.focus();
                msgInput.setSelectionRange(start + em.length, start + em.length);
            });
        }

        emojiToggleBtn?.addEventListener('click', function (e) {
            e.stopPropagation();
            emojiPopover.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (emojiPopover && !emojiPopover.contains(e.target) && e.target !== emojiToggleBtn && !emojiToggleBtn?.contains(e.target)) {
                emojiPopover.classList.remove('open');
            }
        });

        // New Group Modal (company user only)
        document.getElementById('openNewGroupBtn')?.addEventListener('click', function () {
            document.getElementById('waNewGroupName').value = '';
            document.getElementById('waMemberSearch').value = '';
            document.getElementById('waCreateGroupErr').style.display = 'none';
            const list = document.getElementById('waCreateMembersList');
            list.innerHTML = state.users.map(u => `
                <label class="d-flex align-items-center gap-2 p-2 rounded wa-m-row" style="cursor:pointer;" data-name="${esc(u.name.toLowerCase())}">
                    <input type="checkbox" class="form-check-input m-0 wa-m-cb" value="${u.id}">
                    <img src="${esc(u.avatar)}" onerror="this.onerror=null;this.src='${defaultAvatarUrl}';" class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                    <span class="small fw-semibold text-dark">${esc(u.name)}</span>
                </label>
            `).join('') || '<p class="text-muted small text-center py-3 mb-0">No active employees found.</p>';
            showModal('waCreateGroupModal');
        });

        document.getElementById('waMemberSearch').addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#waCreateMembersList .wa-m-row').forEach(r => {
                r.style.display = r.getAttribute('data-name').includes(q) ? 'flex' : 'none';
            });
        });

        document.getElementById('waSubmitGroupBtn').addEventListener('click', function () {
            const name = document.getElementById('waNewGroupName').value.trim();
            const memberIds = Array.from(document.querySelectorAll('.wa-m-cb:checked')).map(c => Number(c.value));
            const err = document.getElementById('waCreateGroupErr');

            if (!name) { err.textContent = 'Please enter a group name.'; err.style.display = 'block'; return; }
            if (!memberIds.length) { err.textContent = 'Please select at least 1 active employee.'; err.style.display = 'block'; return; }

            fetch(endpoints.groupsStore, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ name, member_ids: memberIds })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) { err.textContent = res.message || 'Failed to create group.'; err.style.display = 'block'; return; }
                hideModal('waCreateGroupModal');
                loadData(false);
                openChat('group', res.group.id, false);
            });
        });

        // Group Info Modal
        document.getElementById('waGroupInfoBtn').addEventListener('click', function () {
            if (!state.activeGroupMeta) return;
            const meta = state.activeGroupMeta;
            document.getElementById('waGroupInfoTitle').textContent = meta.group.name;
            document.getElementById('waEditGroupName').value = meta.group.name;
            const canManage = meta.group.my_role === 'admin' || isCompanyOrHr;
            document.getElementById('waGroupAdminBox').style.display = canManage ? 'block' : 'none';

            const existingIds = meta.members.map(m => m.id);
            const available = state.users.filter(u => !existingIds.includes(u.id));
            document.getElementById('waAddMemberSelect').innerHTML = available.length
                ? available.map(u => `<option value="${u.id}">${esc(u.name)}</option>`).join('')
                : '<option value="">All active employees added</option>';

            document.getElementById('waGroupMembersList').innerHTML = meta.members.map(m => `
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${esc(m.avatar)}" class="rounded-circle" width="34" height="34" style="object-fit:cover;">
                        <span class="small fw-semibold text-dark">${esc(m.name)}</span>
                    </div>
                    <span class="badge ${m.role === 'admin' ? 'bg-primary' : 'bg-light text-dark'}">${esc(m.role)}</span>
                </div>
            `).join('');

            showModal('waGroupInfoModal');
        });

        document.getElementById('waSaveGroupNameBtn').addEventListener('click', function () {
            if (state.activeType !== 'group' || !state.activeId) return;
            const name = document.getElementById('waEditGroupName').value.trim();
            if (!name) return;
            fetch(endpoints.groupUpdate(state.activeId), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ name })
            }).then(() => {
                hideModal('waGroupInfoModal');
                loadData(false);
                openChat('group', state.activeId, false);
            });
        });

        document.getElementById('waAddMemberBtn').addEventListener('click', function () {
            if (state.activeType !== 'group' || !state.activeId) return;
            const userId = document.getElementById('waAddMemberSelect').value;
            if (!userId) return;
            fetch(endpoints.groupUpdate(state.activeId), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ add_member_ids: [Number(userId)] })
            }).then(() => {
                hideModal('waGroupInfoModal');
                loadData(false);
                openChat('group', state.activeId, false);
            });
        });

        document.getElementById('waLeaveGroupBtn').addEventListener('click', function () {
            if (state.activeType !== 'group' || !state.activeId) return;
            fetch(endpoints.groupLeave(state.activeId), {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            }).then(() => {
                hideModal('waGroupInfoModal');
                state.activeType = null;
                state.activeId = null;
                document.getElementById('waComposer').style.display = 'none';
                const infoBtn = document.getElementById('waGroupInfoBtn');
                infoBtn.classList.add('d-none');
                infoBtn.classList.remove('d-inline-flex');
                document.getElementById('waHeaderTitle').textContent = 'HRM Messenger';
                document.getElementById('waHeaderSub').textContent = 'Select an employee or group to chat';
                document.getElementById('waMessagesBox').innerHTML = '<div class="wa-empty-state"><p class="text-muted small mb-0">You left the group.</p></div>';
                loadData(false);
            });
        });

        // 3-second polling
        setInterval(function () {
            loadData(false);
            if (state.activeType && state.activeId) {
                openChat(state.activeType, state.activeId, true);
            }
        }, 3000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endsection

