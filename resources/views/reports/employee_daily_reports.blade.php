@extends('layouts.admin')

@section('page-title')
    {{ __('Employee Daily Reports') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Employee Daily Reports') }}</li>
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-emp-reports * { font-family: 'Inter', sans-serif; }

    /* ── Filter Panel ─────────────────────── */
    .hrm-filter-panel {
        background:#fff; border-radius:16px; border:1px solid #e8edf5;
        box-shadow:0 2px 12px rgba(0,26,59,.05); padding:24px; margin-bottom:24px;
    }
    .hrm-filter-title { font-size:.82rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    .hrm-filter-title i { color:#001a3b; }
    .hrm-filter-grid { display:flex; gap:16px; flex-wrap:wrap; align-items:flex-end; }
    .hrm-filter-field { flex:1; min-width:200px; }
    .hrm-filter-label { font-size:.78rem; font-weight:700; color:#374151; margin-bottom:7px; display:flex; align-items:center; gap:5px; text-transform:uppercase; letter-spacing:.04em; }
    .hrm-filter-label i { color:#001a3b; }
    .hrm-filter-input, .hrm-filter-select { width:100%; border:2px solid #e8edf5; border-radius:10px; padding:9px 13px; font-size:.85rem; color:#1e293b; background:#fafbfd; outline:none; transition:all .2s; font-family:'Inter',sans-serif; }
    .hrm-filter-input:focus, .hrm-filter-select:focus { border-color:#001a3b; background:#fff; box-shadow:0 0 0 4px rgba(0,26,59,.08); }
    .hrm-filter-actions { display:flex; gap:8px; padding-bottom:1px; }

    /* ── Results banner ───────────────────── */
    .hrm-results-banner { display:flex; align-items:center; gap:12px; background:linear-gradient(135deg,#eff6ff,#dbeafe); border:1px solid #bfdbfe; border-radius:10px; padding:12px 18px; margin-bottom:20px; flex-wrap:wrap; }
    .hrm-results-banner strong { color:#1d4ed8; font-weight:800; }
    .hrm-results-banner span  { font-size:.84rem; color:#1e40af; }

    /* ── Section card ─────────────────────── */
    .hrm-section-card { background:#fff; border-radius:16px; border:1px solid #e8edf5; box-shadow:0 2px 12px rgba(0,26,59,.05); overflow:hidden; }
    .hrm-section-hdr  { padding:18px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; background:#fafbfd; }
    .hrm-section-hdr h5 { font-size:.95rem; font-weight:700; color:#1e293b; margin:0; display:flex; align-items:center; gap:8px; }
    .hrm-section-hdr h5 i { color:#001a3b; }

    /* ── Table ────────────────────────────── */
    .hrm-table { width:100%; border-collapse:collapse; }
    .hrm-table th { background:#f8fafc; color:#6b7280; font-size:.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:12px 20px; text-align:left; border-bottom:1px solid #f1f5f9; white-space:nowrap; }
    .hrm-table td { padding:14px 20px; border-bottom:1px solid #f8fafc; font-size:.83rem; color:#374151; vertical-align:middle; white-space:nowrap; }
    .hrm-table tr:last-child td { border-bottom:none; }
    .hrm-table tbody tr:hover td { background:#fafbfd; }
    .hrm-table tbody tr { transition:background .15s; }

    /* ── Chips & badges ───────────────────── */
    .hrm-emp-cell   { display:flex; align-items:center; gap:10px; }
    .hrm-emp-avatar { width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid #e8edf5; }
    .hrm-emp-name   { font-weight:700; color:#1e293b; font-size:.85rem; }
    .hrm-hours-chip { background:#fef3c7; color:#92400e; border-radius:8px; padding:5px 12px; font-size:.8rem; font-weight:800; display:inline-flex; align-items:center; gap:5px; }
    .hrm-date-chip  { background:#f1f5f9; color:#475569; border-radius:8px; padding:5px 12px; font-size:.8rem; font-weight:600; display:inline-flex; align-items:center; gap:5px; }
    .hrm-count-badge{ background:#e0e7ff; color:#3730a3; border-radius:20px; padding:3px 10px; font-size:.72rem; font-weight:700; }



    /* ── Tabs ─────────────────────────────── */
    .hrm-tabs-nav { display:flex; gap:12px; border-bottom:2px solid #f1f5f9; margin-bottom:24px; padding-bottom:8px; }
    .hrm-tab-btn  { background:none; border:none; padding:8px 18px; font-size:.86rem; font-weight:700; color:#64748b; cursor:pointer; border-radius:8px; transition:all .2s; display:flex; align-items:center; gap:6px; }
    .hrm-tab-btn.active { background:#eff6ff; color:#1e3a8a; }
    .hrm-tab-btn:hover:not(.active) { background:#f8fafc; color:#1e293b; }
    .hrm-tab-content { display:none; }
    .hrm-tab-content.active { display:block; }

    /* ── Empty state ──────────────────────── */
    .hrm-empty-td  { text-align:center; padding:60px 20px; }
    .hrm-empty-ico { width:80px; height:80px; background:linear-gradient(135deg,#e8f0fe,#c7d8f8); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
    .hrm-empty-ico i { font-size:2rem; color:#001a3b; }

    /* ── Detail Modal ─────────────────────────────────────────────── */
    .hrm-detail-modal .modal-content {
        border-radius: 24px; border: none;
        box-shadow: 0 32px 80px rgba(0,26,59,.22), 0 0 0 1px rgba(255,255,255,.08);
        overflow: hidden; background: #f0f4f8;
    }
    /* Hero header */
    .hrm-modal-hero {
        background: linear-gradient(135deg, #0a1628 0%, #0f2a5e 50%, #1e3a8a 100%);
        padding: 28px 32px 0;
        position: relative;
        overflow: hidden;
    }
    .hrm-modal-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at 80% 0%, rgba(99,179,255,.18) 0%, transparent 60%),
                    radial-gradient(ellipse at 20% 100%, rgba(139,92,246,.12) 0%, transparent 60%);
    }
    .hrm-modal-hero-top {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px; position: relative; z-index: 1;
    }
    .hrm-modal-hero-identity { display: flex; align-items: center; gap: 14px; }
    .hrm-modal-hero-avatar {
        width: 52px; height: 52px; border-radius: 50%; object-fit: cover;
        border: 3px solid rgba(255,255,255,.25);
        box-shadow: 0 0 0 4px rgba(99,179,255,.2);
    }
    .hrm-modal-hero-name { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
    .hrm-modal-hero-date { font-size: .8rem; color: rgba(255,255,255,.6); margin-top: 2px; display: flex; align-items: center; gap: 5px; }
    .hrm-modal-hero-close {
        width: 36px; height: 36px; border-radius: 50%;
        background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18);
        color: #fff; font-size: .9rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s; backdrop-filter: blur(4px);
        flex-shrink: 0;
    }
    .hrm-modal-hero-close:hover { background: rgba(255,255,255,.22); transform: scale(1.05); }
    /* Stats strip */
    .hrm-modal-stats {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 1px; background: rgba(255,255,255,.08);
        border-radius: 16px 16px 0 0; overflow: hidden;
        position: relative; z-index: 100;
        height:130px
    }
    .hrm-modal-stat {
        padding: 16px 20px; background: rgba(255,255,255,.06);
        backdrop-filter: blur(8px); text-align: center;
        transition: background .2s;
    }
    .hrm-modal-stat:hover { background: rgba(255,255,255,.1); }
    .hrm-modal-stat-label {
        font-size: .65rem; font-weight: 700; color: rgba(255,255,255,.5);
        text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px;
    }
    .hrm-modal-stat-value {
        font-size: 1.15rem; font-weight: 800; color: #fff; letter-spacing: -.02em;
    }
    .hrm-modal-stat-icon { font-size: .85rem; color: rgba(255,255,255,.45); margin-bottom: 2px; }
    /* Body */
    .hrm-detail-modal .modal-body { 
        padding: 24px 28px; background: #f0f4f8; 
        border-radius: 0 0 24px 24px; 
    }

    /* Entries section heading */
    .hrm-entries-heading {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 16px;
    }
    .hrm-entries-heading-line {
        flex: 1; height: 1px;
        background: linear-gradient(90deg, #cbd5e1, transparent);
    }
    .hrm-entries-heading-text {
        font-size: .7rem; font-weight: 800; color: #64748b;
        text-transform: uppercase; letter-spacing: .1em; white-space: nowrap;
    }
    .hrm-entries-count-pill {
        background: #e2e8f0; color: #475569;
        border-radius: 20px; padding: 2px 10px;
        font-size: .68rem; font-weight: 700;
    }

    /* Entry cards — modern left-accent style */
    .hrm-entry-card {
        background: #fff; border-radius: 14px;
        border: 1px solid #e8edf5; border-left: 4px solid #cbd5e1;
        padding: 16px 20px; margin-bottom: 10px;
        box-shadow: 0 1px 4px rgba(0,26,59,.04);
        transition: box-shadow .2s, transform .15s, border-left-color .2s;
        position: relative;
    }
    .hrm-entry-card.hrm-card-project  { border-left-color: #3b82f6; }
    .hrm-entry-card.hrm-card-general  { border-left-color: #10b981; }
    .hrm-entry-card:hover {
        box-shadow: 0 6px 24px rgba(0,26,59,.1);
        transform: translateY(-2px);
    }
    .hrm-entry-card:last-child { margin-bottom: 0; }
    .hrm-entry-hdr  { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 10px; }
    .hrm-entry-badges { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }

    /* Type pill */
    .hrm-type-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .68rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: .05em; border-radius: 20px; padding: 3px 10px;
    }
    .hrm-type-pill.hrm-pill-project  { background: linear-gradient(135deg,#dbeafe,#eff6ff); color: #1d4ed8; }
    .hrm-type-pill.hrm-pill-general  { background: linear-gradient(135deg,#d1fae5,#ecfdf5); color: #065f46; }

    /* Project / module chips */
    .hrm-chip-proj {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: .75rem; font-weight: 600; color: #374151;
        background: #f1f5f9; border: 1px solid #e2e8f0;
        border-radius: 6px; padding: 3px 10px;
    }
    .hrm-chip-proj i { color: #6b7280; }
    .hrm-chip-mod {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: .75rem; font-weight: 600; color: #4f46e5;
        background: #eef2ff; border: 1px solid #c7d2fe;
        border-radius: 6px; padding: 3px 10px;
    }
    .hrm-chip-task {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: .75rem; font-weight: 600; color: #0f766e;
        background: #f0fdfa; border: 1px solid #99f6e4;
        border-radius: 6px; padding: 3px 10px;
    }
    /* Hours badge */
    .hrm-hrs-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .82rem; font-weight: 800;
        background: linear-gradient(135deg,#fef9c3,#fef3c7);
        color: #92400e; border: 1px solid #fde68a;
        border-radius: 10px; padding: 5px 14px;
        flex-shrink: 0; white-space: nowrap;
    }
    /* Description */
    .hrm-entry-desc {
        font-size: .82rem; color: #64748b; line-height: 1.65;
        padding-top: 10px; border-top: 1px solid #f1f5f9;
        margin-top: 2px;
    }
    /* Custom Scrollbar for modal body to prevent corner overlap */
    .hrm-detail-modal .modal-body::-webkit-scrollbar { width: 6px; }
    .hrm-detail-modal .modal-body::-webkit-scrollbar-track { background: transparent; margin-bottom: 24px; margin-top: 4px; }
    .hrm-detail-modal .modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
    .hrm-detail-modal .modal-body::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Legacy classes kept for backward compat */
    .hrm-entry-proj { font-size:.8rem; font-weight:700; background:#dbeafe; color:#1d4ed8; border-radius:6px; padding:3px 10px; display:inline-flex; align-items:center; gap:5px; }
    .hrm-entry-mod  { font-size:.8rem; font-weight:700; background:#e0e7ff; color:#3730a3; border-radius:6px; padding:3px 10px; display:inline-flex; align-items:center; gap:5px; }
    .hrm-entry-hrs  { font-size:.85rem; font-weight:800; background:#fef3c7; color:#92400e; border-radius:8px; padding:4px 12px; display:inline-flex; align-items:center; gap:5px; flex-shrink:0; }
    .hrm-entry-type-tag { font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.05em; border-radius:5px; padding:2px 8px; }
    .hrm-type-project { background:#eff6ff; color:#2563eb; }
    .hrm-type-general { background:#f0fdf4; color:#16a34a; }
    .hrm-task-chip { background:#e0e7ff; color:#3730a3; border-radius:6px; padding:3px 10px; font-size:.78rem; font-weight:700; display:inline-flex; align-items:center; gap:4px; }
    .hrm-section-divider { font-size:.75rem; font-weight:800; color:#001a3b; text-transform:uppercase; letter-spacing:.07em; margin:20px 0 12px; display:flex; align-items:center; gap:8px; }
    .hrm-section-divider::after { content:''; flex:1; height:1px; background:#e8edf5; }

    .fade-in { animation:fadeIn .5s ease; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .hrm-modal-stats { 
            grid-template-columns: 1fr; 
            border-radius: 12px 12px 0 0;
            height: 100px; 
        }
        .hrm-modal-stat { 
            display: flex; align-items: center; justify-content: space-between; 
            padding: 12px 20px; text-align: left; 
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .hrm-modal-stat-icon { display: none; }
        .hrm-modal-stat-label { margin-bottom: 0; font-size: 0.75rem; }
        .hrm-modal-stat-value { font-size: 1rem; }
        
        .hrm-modal-hero-top { flex-direction: column; align-items: flex-start; gap: 16px; margin-bottom: 16px; }
        .hrm-modal-hero-close { position: absolute; top: 0; right: 0; }
        
        .hrm-detail-modal .modal-body { padding: 16px; }
        .hrm-entry-hdr { flex-direction: column; align-items: flex-start; gap: 8px; }
        .hrm-hrs-badge { align-self: flex-start; }
        
        .hrm-section-hdr { flex-direction: column; align-items: flex-start; gap: 10px; }
        .hrm-tabs-nav { flex-wrap: wrap; }
        .hrm-tab-btn { flex: 1; text-align: center; justify-content: center; white-space: nowrap; }

        .row.align-items-center.mb-4 > div { width: 100%; text-align: left !important; margin-bottom: 10px; }
        .row.align-items-center.mb-4 form { flex-direction: column; align-items: stretch !important; width: 100%; }
        .row.align-items-center.mb-4 form input, .row.align-items-center.mb-4 form select { width: 100% !important; margin-bottom: 10px; }
        .row.align-items-center.mb-4 form .btn { width: 100%; justify-content: center; }
    }
</style>

@php
    $isCompanyOrTester = (\Auth::user()->type == 'company' || \Auth::user()->isTester());
@endphp

<div class="hrm-emp-reports fade-in">

    {{-- Filter Panel --}}
    <div class="hrm-filter-panel">
        {{ Form::open(['route' => ['report.employee_daily'], 'method' => 'GET', 'id' => 'report_employee_daily_filter']) }}
        <div class="hrm-filter-grid">
            <div class="hrm-filter-field">
                <label class="hrm-filter-label"><i class="ti ti-user"></i> {{ __('Employee') }}</label>
                {{ Form::select('employee_id', ['' => __('All Employees')] + $allEmployees->toArray(), request()->get('employee_id'), ['class' => 'hrm-filter-select select2', 'id' => 'employee_id']) }}
            </div>
            <div class="hrm-filter-field">
                <label class="hrm-filter-label"><i class="ti ti-calendar"></i> {{ __('Start Date') }}</label>
                <input type="date" name="start_date" class="hrm-filter-input" style="cursor:pointer;" value="{{ $startDate }}" onclick="this.showPicker()" required>
            </div>
            <div class="hrm-filter-field">
                <label class="hrm-filter-label"><i class="ti ti-calendar"></i> {{ __('End Date') }}</label>
                <input type="date" name="end_date" class="hrm-filter-input" style="cursor:pointer;" value="{{ $endDate }}" onclick="this.showPicker()" required>
            </div>
            <div class="hrm-filter-actions">
                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Apply Filter') }}" onclick="document.getElementById('report_employee_daily_filter').submit(); return false;">
                    <i class="ti ti-search"></i>
                </a>
                <a href="{{ route('report.employee_daily') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset Filter') }}">
                    <i class="ti ti-x"></i>
                </a>
            </div>
        </div>
        {{ Form::close() }}
    </div>

    @if(request()->filled('employee_id'))
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div style="background:#fff;border-radius:16px;border:1px solid #e8edf5;padding:20px;box-shadow:0 2px 12px rgba(0,26,59,.05);display:flex;align-items:center;gap:16px;">
                <div style="width:50px;height:50px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#1d4ed8;font-size:1.5rem;"><i class="ti ti-calendar-time"></i></div>
                <div>
                    <p style="margin:0;font-size:.8rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;">{{ __('Total Available Hours') }}</p>
                    <h3 style="margin:0;font-size:1.5rem;font-weight:800;color:#1e293b;">{{ $totalAvailableHours }}<span style="font-size:1rem;color:#64748b;margin-left:4px;">hrs</span></h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div style="background:#fff;border-radius:16px;border:1px solid #e8edf5;padding:20px;box-shadow:0 2px 12px rgba(0,26,59,.05);display:flex;align-items:center;gap:16px;">
                <div style="width:50px;height:50px;background:#fef3c7;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#b45309;font-size:1.5rem;"><i class="ti ti-clock"></i></div>
                <div>
                    <p style="margin:0;font-size:.8rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;">{{ __('Working / Logged Hours') }}</p>
                    <h3 style="margin:0;font-size:1.5rem;font-weight:800;color:#1e293b;">{{ $totalWorkingHours }}<span style="font-size:1rem;color:#64748b;margin-left:4px;">hrs</span></h3>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            @php
                $isOvertime  = $remainingHours < 0;
                $cardBg      = $isOvertime ? '#fef2f2' : '#fff';
                $iconBg      = $isOvertime ? '#fee2e2' : '#d1fae5';
                $iconColor   = $isOvertime ? '#dc2626' : '#059669';
                $titleText   = $isOvertime ? __('Exceeded / Overtime') : __('Remaining Hours');
                $displayHrs  = $isOvertime ? abs($remainingHours) : $remainingHours;
            @endphp
            <div style="background:{{ $cardBg }};border-radius:16px;border:1px solid {{ $isOvertime ? '#fca5a5' : '#e8edf5' }};padding:20px;box-shadow:0 2px 12px rgba(0,26,59,.05);display:flex;align-items:center;gap:16px;">
                <div style="width:50px;height:50px;background:{{ $iconBg }};border-radius:12px;display:flex;align-items:center;justify-content:center;color:{{ $iconColor }};font-size:1.5rem;"><i class="ti {{ $isOvertime ? 'ti-alert-triangle' : 'ti-chart-arcs' }}"></i></div>
                <div>
                    <p style="margin:0;font-size:.8rem;font-weight:700;color:{{ $isOvertime ? '#b91c1c' : '#64748b' }};text-transform:uppercase;letter-spacing:.04em;">{{ $titleText }}</p>
                    <h3 style="margin:0;font-size:1.5rem;font-weight:800;color:{{ $isOvertime ? '#991b1b' : '#1e293b' }};">{{ $displayHrs }}<span style="font-size:1rem;opacity:.8;margin-left:4px;">hrs</span></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="hrm-results-banner">
        <i class="ti ti-filter-check" style="color:#1d4ed8;font-size:1rem;"></i>
        @if($isCompanyOrTester)
            <span>Showing <strong>{{ $groupedReports->count() }}</strong> daily report entries (<strong>{{ $updates->count() }}</strong> project updates, <strong>{{ $generalTasks->count() }}</strong> general tasks)</span>
        @else
            <span>Showing <strong>{{ $groupedUpdates->count() }}</strong> project update entries and <strong>{{ $groupedTasks->count() }}</strong> general task entries</span>
        @endif
    </div>
    @endif

    {{-- Tabs --}}
    @if(!$isCompanyOrTester)
    <div class="hrm-tabs-nav mt-3">
        <button class="hrm-tab-btn active" onclick="switchTab(event,'project-updates-report-tab')">
            <i class="ti ti-calendar-event"></i> {{ __('Project Daily Updates') }}
        </button>
        <button class="hrm-tab-btn" onclick="switchTab(event,'general-tasks-report-tab')">
            <i class="ti ti-list-check"></i> {{ __('General Daily Tasks') }}
        </button>
    </div>
    @endif

    @if($isCompanyOrTester)
    {{-- Consolidated Daily Reports --}}
    <div class="hrm-section-card mt-3">
        <div class="hrm-section-hdr">
            <h5><i class="ti ti-report"></i> {{ __('Employee Daily Reports') }}</h5>
            <span style="background:#f1f5f9;color:#475569;border-radius:8px;padding:4px 12px;font-size:.75rem;font-weight:700;">
                {{ $groupedReports->count() }} {{ __('days') }}
            </span>
        </div>
        <div style="overflow-x:auto;">
            <table class="hrm-table" id="pc-dt-simple">
                <thead>
                    <tr>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Hours') }}</th>
                        <th>{{ __('Entries') }}</th>
                        <th style="text-align:center;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedReports as $idx => $group)
                    @php
                        $emp = $group['employee'];
                        $profilePath = !empty($emp?->user?->avatar)
                            ? asset('storage/uploads/avatar/' . $emp->user->avatar)
                            : asset('storage/uploads/avatar/avatar.png');
                    @endphp
                    <tr>
                        <td>
                            <div class="hrm-emp-cell">
                                <img alt="avatar" src="{{ $profilePath }}" class="hrm-emp-avatar">
                                <span class="hrm-emp-name">{{ $emp?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="hrm-date-chip">
                                <i class="ti ti-calendar"></i>
                                {{ \Carbon\Carbon::parse($group['work_date'])->format('M d, Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="hrm-hours-chip">
                                <i class="ti ti-clock"></i>
                                {{ $group['total_hours'] }}h
                            </span>
                        </td>
                        <td>
                            <span class="hrm-count-badge">
                                {{ $group['all_entries']->count() }} {{ $group['all_entries']->count() == 1 ? 'entry' : 'entries' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                {{-- Show Detail Modal --}}
                                <button type="button" class="btn btn-sm btn-info text-white"
                                    data-bs-toggle="modal"
                                    data-bs-target="#reportDetailModal_{{ $idx }}"
                                    data-bs-toggle="tooltip" title="{{ __('Show') }}">
                                    <i class="ti ti-eye"></i>
                                </button>
                                {{-- Delete entire group --}}
                                <button type="button" class="btn btn-sm btn-danger text-white"
                                    onclick="confirmDeleteGroup('report-group-form-{{ $idx }}')"
                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                    <i class="ti ti-trash"></i>
                                </button>
                                {{-- Hidden bulk delete forms --}}
                                @php $delIdx = 0; @endphp
                                @foreach($group['project_entries'] as $pEntry)
                                <form id="report-del-form-{{ $idx }}-{{ $delIdx }}"
                                      action="{{ route('project-updates.destroy', $pEntry->id) }}"
                                      method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                                @php $delIdx++; @endphp
                                @endforeach
                                @foreach($group['general_entries'] as $gEntry)
                                <form id="report-del-form-{{ $idx }}-{{ $delIdx }}"
                                      action="{{ route('general-daily-tasks.destroy', $gEntry->id) }}"
                                      method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                                @php $delIdx++; @endphp
                                @endforeach
                                <span id="report-group-form-{{ $idx }}"
                                      data-count="{{ $delIdx }}"
                                      data-prefix="report-del-form-{{ $idx }}-"
                                      style="display:none;"></span>
                            </div>
                        </td>
                    </tr>

                    {{-- Consolidated Detail Modal (redesigned) --}}
                    <div class="modal fade hrm-detail-modal" id="reportDetailModal_{{ $idx }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                {{-- Hero Header --}}
                                <div class="hrm-modal-hero">
                                    <div class="hrm-modal-hero-top">
                                        <div class="hrm-modal-hero-identity">
                                            <img src="{{ $profilePath }}" alt="" class="hrm-modal-hero-avatar" onerror="this.src='{{ asset('storage/uploads/avatar/avatar.png') }}'">
                                            <div>
                                                <div class="hrm-modal-hero-name">{{ $emp?->name ?? 'Employee' }}</div>
                                                <div class="hrm-modal-hero-date">
                                                    <i class="ti ti-calendar-event"></i>
                                                    {{ \Carbon\Carbon::parse($group['work_date'])->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="hrm-modal-hero-close" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    {{-- Stat Strip --}}
                                    <div class="hrm-modal-stats">
                                        <div class="hrm-modal-stat">
                                            <div class="hrm-modal-stat-icon"><i class="ti ti-clock"></i></div>
                                            <div class="hrm-modal-stat-label">{{ __('Total Hours') }}</div>
                                            <div class="hrm-modal-stat-value">{{ $group['total_hours'] }}h</div>
                                        </div>
                                        <div class="hrm-modal-stat">
                                            <div class="hrm-modal-stat-icon"><i class="ti ti-briefcase"></i></div>
                                            <div class="hrm-modal-stat-label">{{ __('Project Updates') }}</div>
                                            <div class="hrm-modal-stat-value">{{ $group['project_entries']->count() }}</div>
                                        </div>
                                        <div class="hrm-modal-stat">
                                            <div class="hrm-modal-stat-icon"><i class="ti ti-list-check"></i></div>
                                            <div class="hrm-modal-stat-label">{{ __('General Tasks') }}</div>
                                            <div class="hrm-modal-stat-value">{{ $group['general_entries']->count() }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Body --}}
                                <div class="modal-body">
                                    {{-- Entries heading --}}
                                    <div class="hrm-entries-heading">
                                        <span class="hrm-entries-heading-text"><i class="ti ti-layout-grid"></i> {{ __('Daily Entries') }}</span>
                                        <span class="hrm-entries-count-pill">{{ $group['all_entries']->count() }} {{ $group['all_entries']->count() == 1 ? 'entry' : 'entries' }}</span>
                                        <div class="hrm-entries-heading-line"></div>
                                    </div>

                                    @foreach($group['all_entries'] as $entry)
                                        @if($entry->entry_type === 'project')
                                        <div class="hrm-entry-card hrm-card-project">
                                            <div class="hrm-entry-hdr">
                                                <div class="hrm-entry-badges">
                                                    <span class="hrm-type-pill hrm-pill-project">
                                                        <i class="ti ti-briefcase"></i> {{ __('Project Update') }}
                                                    </span>
                                                    @if($entry->project)
                                                    <span class="hrm-chip-proj">
                                                        <i class="ti ti-folder"></i> {{ $entry->project->project_name }}
                                                    </span>
                                                    @endif
                                                    @if($entry->module)
                                                    <span class="hrm-chip-mod">
                                                        <i class="ti ti-layout-grid"></i> {{ $entry->module->module_name }}
                                                    </span>
                                                    @endif
                                                </div>
                                                <span class="hrm-hrs-badge">
                                                    <i class="ti ti-clock"></i> {{ $entry->hours_worked }}h
                                                </span>
                                            </div>
                                            <div class="hrm-entry-desc">{{ $entry->work_done ?: __('No description provided.') }}</div>
                                        </div>
                                        @else
                                        <div class="hrm-entry-card hrm-card-general">
                                            <div class="hrm-entry-hdr">
                                                <div class="hrm-entry-badges">
                                                    <span class="hrm-type-pill hrm-pill-general">
                                                        <i class="ti ti-list-check"></i> {{ __('General Task') }}
                                                    </span>
                                                    <span class="hrm-chip-task">
                                                        <i class="ti ti-tag"></i> {{ $entry->task_title }}
                                                    </span>
                                                    @if($entry->project_name)
                                                    <span class="hrm-chip-proj">
                                                        <i class="ti ti-folder"></i> {{ $entry->project_name }}
                                                    </span>
                                                    @endif
                                                </div>
                                                <span class="hrm-hrs-badge">
                                                    <i class="ti ti-clock"></i> {{ $entry->duration }}h
                                                </span>
                                            </div>
                                            @if($entry->task_description)
                                            <div class="hrm-entry-desc">{{ $entry->task_description }}</div>
                                            @endif
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="hrm-empty-td">
                            <div class="hrm-empty-ico"><i class="ti ti-report-off"></i></div>
                            <h6 style="color:#374151;font-weight:700;margin-bottom:4px;">{{ __('No Daily Reports Found') }}</h6>
                            <p style="color:#6b7280;font-size:.83rem;margin:0;">{{ __('Try adjusting the filter options.') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    {{-- ════════════════════════════════════════════════════
         PROJECT DAILY UPDATES TAB
    ════════════════════════════════════════════════════ --}}
    <div id="project-updates-report-tab" class="hrm-tab-content active">
        <div class="hrm-section-card">
            <div class="hrm-section-hdr">
                <h5><i class="ti ti-report"></i> {{ __('Project Daily Updates') }}</h5>
                <span style="background:#f1f5f9;color:#475569;border-radius:8px;padding:4px 12px;font-size:.75rem;font-weight:700;">
                    {{ $groupedUpdates->count() }} {{ __('entries') }}
                </span>
            </div>
            <div style="overflow-x:auto;">
                <table class="hrm-table" id="pc-dt-simple">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Hours') }}</th>
                            <th>{{ __('Entries') }}</th>
                            <th style="text-align:center;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedUpdates as $idx => $group)
                        @php
                            $emp = $group['employee'];
                            $profilePath = !empty($emp?->user?->avatar)
                                ? asset('storage/uploads/avatar/' . $emp->user->avatar)
                                : asset('storage/uploads/avatar/avatar.png');
                        @endphp
                        <tr>
                            <td>
                                <div class="hrm-emp-cell">
                                    <img alt="avatar" src="{{ $profilePath }}" class="hrm-emp-avatar">
                                    <span class="hrm-emp-name">{{ $emp?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($group['work_date'])->format('M d, Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="hrm-hours-chip">
                                    <i class="ti ti-clock"></i>
                                    {{ $group['total_hours'] }}h
                                </span>
                            </td>
                            <td>
                                <span class="hrm-count-badge">
                                    {{ $group['entries']->count() }} {{ $group['entries']->count() == 1 ? 'entry' : 'entries' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    {{-- Show Detail Modal --}}
                                    <button type="button" class="btn btn-sm btn-info text-white"
                                        data-bs-toggle="modal"
                                        data-bs-target="#updateDetailModal_{{ $idx }}"
                                        data-bs-toggle="tooltip" title="{{ __('Show') }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    {{-- Delete entire group (all entries for this emp+date) --}}
                                    <button type="button" class="btn btn-sm btn-danger text-white"
                                        onclick="confirmDeleteGroup('update-group-form-{{ $idx }}')"
                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    {{-- Hidden bulk delete forms --}}
                                    @foreach($group['entries'] as $eIdx => $entry)
                                    <form id="update-del-form-{{ $idx }}-{{ $eIdx }}"
                                          action="{{ route('project-updates.destroy', $entry->id) }}"
                                          method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                    @endforeach
                                    <span id="update-group-form-{{ $idx }}"
                                          data-count="{{ $group['entries']->count() }}"
                                          data-prefix="update-del-form-{{ $idx }}-"
                                          style="display:none;"></span>
                                </div>
                            </td>
                        </tr>

                        {{-- Project Updates Detail Modal (redesigned) --}}
                        <div class="modal fade hrm-detail-modal" id="updateDetailModal_{{ $idx }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="hrm-modal-hero">
                                        <div class="hrm-modal-hero-top">
                                            <div class="hrm-modal-hero-identity">
                                                <img src="{{ $profilePath }}" alt="" class="hrm-modal-hero-avatar" onerror="this.src='{{ asset('storage/uploads/avatar/avatar.png') }}'">
                                                <div>
                                                    <div class="hrm-modal-hero-name">{{ $emp?->name ?? 'Employee' }}</div>
                                                    <div class="hrm-modal-hero-date">
                                                        <i class="ti ti-calendar-event"></i>
                                                        {{ \Carbon\Carbon::parse($group['work_date'])->format('d M Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="hrm-modal-hero-close" data-bs-dismiss="modal" aria-label="Close">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        <div class="hrm-modal-stats">
                                            <div class="hrm-modal-stat">
                                                <div class="hrm-modal-stat-icon"><i class="ti ti-clock"></i></div>
                                                <div class="hrm-modal-stat-label">{{ __('Total Hours') }}</div>
                                                <div class="hrm-modal-stat-value">{{ $group['total_hours'] }}h</div>
                                            </div>
                                            <div class="hrm-modal-stat">
                                                <div class="hrm-modal-stat-icon"><i class="ti ti-briefcase"></i></div>
                                                <div class="hrm-modal-stat-label">{{ __('Entries') }}</div>
                                                <div class="hrm-modal-stat-value">{{ $group['entries']->count() }}</div>
                                            </div>
                                            <div class="hrm-modal-stat">
                                                <div class="hrm-modal-stat-icon"><i class="ti ti-folder"></i></div>
                                                <div class="hrm-modal-stat-label">{{ __('Type') }}</div>
                                                <div class="hrm-modal-stat-value" style="font-size:.85rem;">Project</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <div class="hrm-entries-heading">
                                            <span class="hrm-entries-heading-text"><i class="ti ti-briefcase"></i> {{ __('Project Entries') }}</span>
                                            <span class="hrm-entries-count-pill">{{ $group['entries']->count() }}</span>
                                            <div class="hrm-entries-heading-line"></div>
                                        </div>
                                        @foreach($group['entries'] as $entry)
                                        <div class="hrm-entry-card hrm-card-project">
                                            <div class="hrm-entry-hdr">
                                                <div class="hrm-entry-badges">
                                                    <span class="hrm-type-pill hrm-pill-project">
                                                        <i class="ti ti-briefcase"></i> {{ __('Project') }}
                                                    </span>
                                                    @if($entry->project)
                                                    <span class="hrm-chip-proj">
                                                        <i class="ti ti-folder"></i> {{ $entry->project->project_name }}
                                                    </span>
                                                    @endif
                                                    @if($entry->module)
                                                    <span class="hrm-chip-mod">
                                                        <i class="ti ti-layout-grid"></i> {{ $entry->module->module_name }}
                                                    </span>
                                                    @endif
                                                </div>
                                                <span class="hrm-hrs-badge">
                                                    <i class="ti ti-clock"></i> {{ $entry->hours_worked }}h
                                                </span>
                                            </div>
                                            <div class="hrm-entry-desc">{{ $entry->work_done ?: __('No description provided.') }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="hrm-empty-td">
                                <div class="hrm-empty-ico"><i class="ti ti-report-off"></i></div>
                                <h6 style="color:#374151;font-weight:700;margin-bottom:4px;">{{ __('No Daily Reports Found') }}</h6>
                                <p style="color:#6b7280;font-size:.83rem;margin:0;">{{ __('Try adjusting the filter options.') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════
         GENERAL DAILY TASKS TAB
    ════════════════════════════════════════════════════ --}}
    <div id="general-tasks-report-tab" class="hrm-tab-content">
        <div class="hrm-section-card">
            <div class="hrm-section-hdr">
                <h5><i class="ti ti-list-check"></i> {{ __('General Daily Tasks') }}</h5>
                <span style="background:#f1f5f9;color:#475569;border-radius:8px;padding:4px 12px;font-size:.75rem;font-weight:700;">
                    {{ $groupedTasks->count() }} {{ __('entries') }}
                </span>
            </div>
            <div style="overflow-x:auto;">
                <table class="hrm-table" id="pc-dt-general">
                    <thead>
                        <tr>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Hours') }}</th>
                            <th>{{ __('Tasks') }}</th>
                            <th style="text-align:center;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedTasks as $gIdx => $gGroup)
                        @php
                            $gEmp = $gGroup['employee'];
                            $gProfilePath = !empty($gEmp?->user?->avatar)
                                ? asset('storage/uploads/avatar/' . $gEmp->user->avatar)
                                : asset('storage/uploads/avatar/avatar.png');
                        @endphp
                        <tr>
                            <td>
                                <div class="hrm-emp-cell">
                                    <img alt="avatar" src="{{ $gProfilePath }}" class="hrm-emp-avatar">
                                    <span class="hrm-emp-name">{{ $gEmp?->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($gGroup['work_date'])->format('M d, Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="hrm-hours-chip">
                                    <i class="ti ti-clock"></i>
                                    {{ $gGroup['total_hours'] }}h
                                </span>
                            </td>
                            <td>
                                <span class="hrm-count-badge">
                                    {{ $gGroup['entries']->count() }} {{ $gGroup['entries']->count() == 1 ? 'task' : 'tasks' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-info text-white"
                                        data-bs-toggle="modal"
                                        data-bs-target="#taskDetailModal_{{ $gIdx }}"
                                        data-bs-toggle="tooltip" title="{{ __('Show') }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger text-white"
                                        onclick="confirmDeleteGroup('task-group-form-{{ $gIdx }}')"
                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    @foreach($gGroup['entries'] as $tIdx => $task)
                                    <form id="task-del-form-{{ $gIdx }}-{{ $tIdx }}"
                                          action="{{ route('general-daily-tasks.destroy', $task->id) }}"
                                          method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                    @endforeach
                                    <span id="task-group-form-{{ $gIdx }}"
                                          data-count="{{ $gGroup['entries']->count() }}"
                                          data-prefix="task-del-form-{{ $gIdx }}-"
                                          style="display:none;"></span>
                                </div>
                            </td>
                        </tr>

                        {{-- General Task Detail Modal (redesigned) --}}
                        <div class="modal fade hrm-detail-modal" id="taskDetailModal_{{ $gIdx }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="hrm-modal-hero">
                                        <div class="hrm-modal-hero-top">
                                            <div class="hrm-modal-hero-identity">
                                                <img src="{{ $gProfilePath }}" alt="" class="hrm-modal-hero-avatar" onerror="this.src='{{ asset('storage/uploads/avatar/avatar.png') }}'">
                                                <div>
                                                    <div class="hrm-modal-hero-name">{{ $gEmp?->name ?? 'Employee' }}</div>
                                                    <div class="hrm-modal-hero-date">
                                                        <i class="ti ti-calendar-event"></i>
                                                        {{ \Carbon\Carbon::parse($gGroup['work_date'])->format('d M Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="hrm-modal-hero-close" data-bs-dismiss="modal" aria-label="Close">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        <div class="hrm-modal-stats">
                                            <div class="hrm-modal-stat">
                                                <div class="hrm-modal-stat-icon"><i class="ti ti-clock"></i></div>
                                                <div class="hrm-modal-stat-label">{{ __('Total Hours') }}</div>
                                                <div class="hrm-modal-stat-value">{{ $gGroup['total_hours'] }}h</div>
                                            </div>
                                            <div class="hrm-modal-stat">
                                                <div class="hrm-modal-stat-icon"><i class="ti ti-list-check"></i></div>
                                                <div class="hrm-modal-stat-label">{{ __('Tasks') }}</div>
                                                <div class="hrm-modal-stat-value">{{ $gGroup['entries']->count() }}</div>
                                            </div>
                                            <div class="hrm-modal-stat">
                                                <div class="hrm-modal-stat-icon"><i class="ti ti-tag"></i></div>
                                                <div class="hrm-modal-stat-label">{{ __('Type') }}</div>
                                                <div class="hrm-modal-stat-value" style="font-size:.85rem;">General</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <div class="hrm-entries-heading">
                                            <span class="hrm-entries-heading-text"><i class="ti ti-list-check"></i> {{ __('Task Entries') }}</span>
                                            <span class="hrm-entries-count-pill">{{ $gGroup['entries']->count() }}</span>
                                            <div class="hrm-entries-heading-line"></div>
                                        </div>
                                        @foreach($gGroup['entries'] as $task)
                                        <div class="hrm-entry-card hrm-card-general">
                                            <div class="hrm-entry-hdr">
                                                <div class="hrm-entry-badges">
                                                    <span class="hrm-type-pill hrm-pill-general">
                                                        <i class="ti ti-list-check"></i> {{ __('General Task') }}
                                                    </span>
                                                    <span class="hrm-chip-task">
                                                        <i class="ti ti-tag"></i> {{ $task->task_title }}
                                                    </span>
                                                    @if($task->project_name)
                                                    <span class="hrm-chip-proj">
                                                        <i class="ti ti-folder"></i> {{ $task->project_name }}
                                                    </span>
                                                    @endif
                                                </div>
                                                <span class="hrm-hrs-badge">
                                                    <i class="ti ti-clock"></i> {{ $task->duration }}h
                                                </span>
                                            </div>
                                            @if($task->task_description)
                                            <div class="hrm-entry-desc">{{ $task->task_description }}</div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="hrm-empty-td">
                                <div class="hrm-empty-ico"><i class="ti ti-report-off"></i></div>
                                <h6 style="color:#374151;font-weight:700;margin-bottom:4px;">{{ __('No General Tasks Found') }}</h6>
                                <p style="color:#6b7280;font-size:.83rem;margin:0;">{{ __('Try adjusting the filter options.') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

@push('script-page')
<script>
    function switchTab(evt, tabId) {
        document.querySelectorAll('.hrm-tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.hrm-tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
    }

    /**
     * Confirm and submit all hidden delete forms for a grouped row.
     * @param {string} groupSpanId  – the ID of the <span> holding meta attributes
     */
    function confirmDeleteGroup(groupSpanId) {
        if (!confirm('{{ __("Are you sure you want to delete all entries for this employee on this date? This action cannot be undone.") }}')) return;

        const span  = document.getElementById(groupSpanId);
        const count  = parseInt(span.dataset.count);
        const prefix = span.dataset.prefix;

        for (let i = 0; i < count; i++) {
            const form = document.getElementById(prefix + i);
            if (form) form.submit();
        }
    }
</script>
@endpush
@endsection
