@extends('layouts.admin')

@section('page-title')
    {{ __('My Daily Work') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('My Daily Updates') }}</li>
@endsection

@section('action-button')
    <div class="d-flex align-items-center gap-3 flex-wrap" style="margin-top: -5px;">
        <button id="mainCreateBtn" data-bs-toggle="modal" data-bs-target="#addProjectUpdateModal" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </button>
    </div>
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-updates-wrap * { font-family: 'Inter', sans-serif; }

    /* Page header */
    .hrm-page-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .hrm-page-hdr h4 { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px; }
    .hrm-page-hdr h4 i { color: #001a3b; }

    /* Stat pills */
    .hrm-stat-pills { display: flex; gap: 10px; flex-wrap: wrap; }
    .hrm-stat-pill { display: flex; align-items: center; gap: 7px; background: #fff; border: 1px solid #e8edf5; border-radius: 10px; padding: 8px 16px; font-size: 0.8rem; font-weight: 700; box-shadow: 0 2px 8px rgba(0,26,59,0.04); }
    .hrm-stat-pill .num { font-size: 1.1rem; font-weight: 800; }
    .hrm-stat-pill.approved .num { color: #059669; }
    .hrm-stat-pill.pending  .num { color: #d97706; }
    .hrm-stat-pill.rejected .num { color: #dc2626; }

    /* Primary Button */
    .hrm-btn-primary {
        background: linear-gradient(135deg, #001a3b, #1e3a8a);
        color: #fff; border: none; border-radius: 10px; padding: 8px 18px;
        font-size: 0.82rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,26,59,0.25);
        text-decoration: none;
    }
    .hrm-btn-primary:hover { color: #fff; transform: translateY(-1px); }

    /* Action buttons */
    .hrm-icon-btn { width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; transition: all 0.2s; text-decoration: none; }
    .hrm-icon-btn-primary { background: #dbeafe; color: #1d4ed8; }
    .hrm-icon-btn-primary:hover { background: #bfdbfe; color: #1d4ed8; }
    .hrm-icon-btn-danger  { background: #fee2e2; color: #b91c1c; }
    .hrm-icon-btn-danger:hover { background: #fecaca; color: #b91c1c; }

    /* Tabs styling */
    .hrm-tabs-nav {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 24px;
        padding-bottom: 8px;
        flex-wrap: nowrap;
        overflow-x: auto;
    }
    .hrm-tab-btn {
        background: none;
        border: none;
        padding: 7px 14px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .hrm-tab-btn.active {
        background: #eff6ff; color: #1e3a8a;
    }
    .hrm-tab-btn:hover:not(.active) {
        background: #f8fafc; color: #1e293b;
    }
    .hrm-tab-content { position: absolute; visibility: hidden; height: 0; overflow: hidden; opacity: 0; pointer-events: none; width: 100%; }
    .hrm-tab-content.active { position: static; visibility: visible; height: auto; overflow: visible; opacity: 1; pointer-events: auto; }

    /* Main card */
    .hrm-main-card {
        background: #fff; border-radius: 16px; border: 1px solid #e8edf5;
        box-shadow: 0 2px 12px rgba(0,26,59,0.05); overflow: hidden;
    }
    .hrm-card-hdr {
        padding: 18px 24px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 16px;
        background: #fafbfd;
    }
    .hrm-card-hdr h5 { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px; }

    /* Table */
    .hrm-table { width: 100%; border-collapse: collapse; }
    .hrm-table th {
        background: #f8fafc; color: #6b7280; font-size: 0.72rem;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        padding: 12px 20px; text-align: left; border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .hrm-table td { padding: 16px 20px; border-bottom: 1px solid #f8fafc; font-size: 0.84rem; color: #374151; vertical-align: middle; white-space: nowrap; }
    .hrm-table tr:last-child td { border-bottom: none; }
    .hrm-table tbody tr { transition: background 0.15s; }
    .hrm-table tbody tr:hover td { background: #fafbfd; }

    /* Status badges */
    .hrm-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 20px; padding: 4px 12px; font-size: 0.73rem; font-weight: 700; }
    .hrm-badge-approved { background: #d1fae5; color: #065f46; }
    .hrm-badge-pending  { background: #fef3c7; color: #92400e; }
    .hrm-badge-rejected { background: #fee2e2; color: #991b1b; }

    .hrm-date-chip { display: inline-flex; align-items: center; gap: 5px; background: #f1f5f9; border-radius: 8px; padding: 4px 10px; font-size: 0.78rem; color: #475569; font-weight: 600; }
    .hrm-project-link { font-weight: 700; color: #001a3b; font-size: 0.85rem; }
    .hrm-module-tag { background: #e0e7ff; color: #3730a3; border-radius: 6px; padding: 2px 9px; font-size: 0.76rem; font-weight: 700; }
    .hrm-hours-tag { background: #fef3c7; color: #92400e; border-radius: 6px; padding: 2px 9px; font-size: 0.78rem; font-weight: 700; }
    .hrm-work-text { font-size: 0.82rem; color: #475569; max-width: 280px; }

    /* Empty */
    .hrm-empty-cell { text-align: center; padding: 60px 20px; }
    .hrm-empty-cell .ico { width: 80px; height: 80px; background: linear-gradient(135deg, #e8f0fe, #c7d8f8); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .hrm-empty-cell .ico i { font-size: 2rem; color: #001a3b; }

    /* Modals */
    .hrm-modal .modal-content { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: visible; }
    .hrm-modal .modal-header { background: linear-gradient(135deg, #001a3b, #1e3a8a); padding: 20px 24px; border: none; border-top-left-radius: 16px; border-top-right-radius: 16px; }
    .hrm-modal .modal-title { color: #fff; font-weight: 700; font-size: 1rem; }
    .hrm-modal .btn-close { filter: brightness(0) invert(1); }
    .hrm-modal .modal-body { padding: 24px; overflow: visible; }
    .hrm-modal .modal-footer { padding: 16px 24px; background: #fafbfd; border-top: 1px solid #f1f5f9; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; }
    .hrm-modal-label { font-size: 0.8rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .hrm-modal-label i { color: #001a3b; }
    .hrm-modal-input, .hrm-modal-select, .hrm-modal-textarea { width: 100%; border: 2px solid #e8edf5; border-radius: 10px; padding: 9px 13px; font-size: 0.85rem; color: #1e293b; background: #fafbfd; outline: none; transition: all 0.2s; font-family: 'Inter', sans-serif; }
    .hrm-modal-input:focus, .hrm-modal-select:focus, .hrm-modal-textarea:focus { border-color: #001a3b; background: #fff; box-shadow: 0 0 0 4px rgba(0,26,59,0.08); }
    .hrm-modal-textarea { resize: vertical; min-height: 90px; }
    .hrm-modal-btn-cancel { background: #f1f5f9; color: #374151; border: none; border-radius: 10px; padding: 9px 20px; font-size: 0.84rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.2s; }
    .hrm-modal-btn-cancel:hover { background: #e2e8f0; }
    .hrm-modal-btn-submit { background: linear-gradient(135deg, #001a3b, #1e3a8a); color: #fff; border: none; border-radius: 10px; padding: 9px 22px; font-size: 0.84rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,26,59,0.25); }
    .hrm-modal-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,26,59,0.35); }

    /* Filter bar buttons */
    .hrm-date-filter {
        width: auto;
        padding: 5px 10px;
        font-size: 0.8rem;
        cursor: pointer;
        min-width: 120px;
        max-width: 145px;
    }
    .hrm-to-label { font-weight: 600; font-size: 0.82rem; color: #64748b; white-space: nowrap; }
    .hrm-filter-btn {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: all 0.2s;
        text-decoration: none;
        flex-shrink: 0;
    }
    .hrm-filter-btn-primary { background: #1d4ed8; color: #fff; }
    .hrm-filter-btn-primary:hover { background: #1e40af; color: #fff; }
    .hrm-filter-btn-danger { background: #ef4444; color: #fff; }
    .hrm-filter-btn-danger:hover { background: #dc2626; color: #fff; }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .hrm-page-hdr { flex-direction: column; align-items: flex-start; gap: 16px; }
        .hrm-page-hdr > div { width: 100%; display: flex; flex-direction: column; gap: 10px; }
        .hrm-stat-pills { flex-direction: column; align-items: stretch; }

        .row.align-items-center.mb-4.mt-4 > div { width: 100%; }
        .row.align-items-center.mb-4.mt-4 form { flex-wrap: wrap; gap: 8px; }
        .hrm-date-filter { flex: 1; min-width: 100px; max-width: none; }

        .hrm-card-hdr { flex-direction: column; align-items: flex-start; gap: 12px; }
        .hrm-card-hdr > div { width: 100%; }
        .hrm-card-hdr form { width: 100%; }
        .hrm-card-hdr form .input-group { width: 100% !important; }

        .hrm-tabs-nav { overflow-x: auto; }
    }

    .fade-in { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="hrm-updates-wrap fade-in">


    {{-- Summary Cards & Filter --}}
    <div class="row align-items-center mb-4 mt-4">
        <div class="col-md-4">
            <h4 class="mb-0" style="font-size:1.1rem; font-weight:800; color:#1e293b;">
                <i class="ti ti-chart-pie" style="color:#001a3b;"></i> {{ __('Hours Summary') }}
            </h4>
        </div>
        <div class="col-md-8 text-md-end mt-3 mt-md-0">
            <form action="{{ route('projects.my_updates') }}" method="GET" class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-end">
                <input type="date" name="start_date" class="hrm-modal-input hrm-date-filter" value="{{ $startDate }}" onclick="this.showPicker()" required>
                <span class="text-muted hrm-to-label">{{ __('to') }}</span>
                <input type="date" name="end_date" class="hrm-modal-input hrm-date-filter" value="{{ $endDate }}" onclick="this.showPicker()" required>
                <button type="submit" class="hrm-filter-btn hrm-filter-btn-primary" data-bs-toggle="tooltip" title="{{ __('Filter') }}">
                    <i class="ti ti-filter"></i>
                </button>
                <a href="{{ route('projects.my_updates') }}" class="hrm-filter-btn hrm-filter-btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                    <i class="ti ti-refresh"></i>
                </a>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Available Hours -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div style="background:#fff; border-radius:16px; border:1px solid #e8edf5; padding:20px; box-shadow:0 2px 12px rgba(0,26,59,0.05); display:flex; align-items:center; gap:16px;">
                <div style="width:50px; height:50px; background:#eff6ff; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#1d4ed8; font-size:1.5rem;">
                    <i class="ti ti-calendar-time"></i>
                </div>
                <div>
                    <p style="margin:0; font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">{{ __('Total Available Hours') }}</p>
                    @php
                        $ah = (float)$totalAvailableHours;
                        $ahH = (int)floor($ah); $ahM = (int)round(($ah-$ahH)*60);
                        $ahLabel = ($ahH > 0 && $ahM > 0) ? $ahH.'h '.$ahM.'min' : ($ahH > 0 ? $ahH.'h' : $ahM.'min');
                    @endphp
                    <h3 style="margin:0; font-size:1.5rem; font-weight:800; color:#1e293b;">{{ $ahLabel }}<span style="font-size:1rem; color:#64748b; margin-left:4px;">hrs</span></h3>
                </div>
            </div>
        </div>
        <!-- Logged Hours -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div style="background:#fff; border-radius:16px; border:1px solid #e8edf5; padding:20px; box-shadow:0 2px 12px rgba(0,26,59,0.05); display:flex; align-items:center; gap:16px;">
                <div style="width:50px; height:50px; background:#fef3c7; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#b45309; font-size:1.5rem;">
                    <i class="ti ti-clock"></i>
                </div>
                <div>
                    <p style="margin:0; font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">{{ __('Working / Logged Hours') }}</p>
                    @php
                        $wh = (float)$totalWorkingHours;
                        $whH = (int)floor($wh); $whM = (int)round(($wh-$whH)*60);
                        $whLabel = ($whH > 0 && $whM > 0) ? $whH.'h '.$whM.'min' : ($whH > 0 ? $whH.'h' : $whM.'min');
                    @endphp
                    <h3 style="margin:0; font-size:1.5rem; font-weight:800; color:#1e293b;">{{ $whLabel }}<span style="font-size:1rem; color:#64748b; margin-left:4px;">hrs</span></h3>
                </div>
            </div>
        </div>
        <!-- Remaining Hours -->
        <div class="col-lg-4 col-md-6 mb-3">
            @php
                $isOvertime = $remainingHours < 0;
                $cardBg = $isOvertime ? '#fef2f2' : '#fff';
                $iconBg = $isOvertime ? '#fee2e2' : '#d1fae5';
                $iconColor = $isOvertime ? '#dc2626' : '#059669';
                $titleText = $isOvertime ? __('Exceeded / Overtime') : __('Remaining Hours');
                $displayHours = $isOvertime ? abs($remainingHours) : $remainingHours;
            @endphp
            <div style="background:{{ $cardBg }}; border-radius:16px; border:1px solid {{ $isOvertime ? '#fca5a5' : '#e8edf5' }}; padding:20px; box-shadow:0 2px 12px rgba(0,26,59,0.05); display:flex; align-items:center; gap:16px; transition:all 0.3s;">
                <div style="width:50px; height:50px; background:{{ $iconBg }}; border-radius:12px; display:flex; align-items:center; justify-content:center; color:{{ $iconColor }}; font-size:1.5rem;">
                    <i class="ti {{ $isOvertime ? 'ti-alert-triangle' : 'ti-chart-arcs' }}"></i>
                </div>
                <div>
                    <p style="margin:0; font-size:0.8rem; font-weight:700; color:{{ $isOvertime ? '#b91c1c' : '#64748b' }}; text-transform:uppercase; letter-spacing:0.04em;">
                        {{ $titleText }}
                    </p>
                    <h3 style="margin:0; font-size:1.5rem; font-weight:800; color:{{ $isOvertime ? '#991b1b' : '#1e293b' }};">
                        @php
                            $rh = (float)$displayHours;
                            $rhH = (int)floor($rh); $rhM = (int)round(($rh-$rhH)*60);
                            $rhLabel = ($rhH > 0 && $rhM > 0) ? $rhH.'h '.$rhM.'min' : ($rhH > 0 ? $rhH.'h' : ($rhM > 0 ? $rhM.'min' : '0h'));
                        @endphp
                        {{ $rhLabel }}<span style="font-size:1rem; opacity:0.8; margin-left:4px;">hrs</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="hrm-tabs-nav">
        <button class="hrm-tab-btn active" onclick="switchTab(event, 'project-updates-tab')">
            <i class="ti ti-calendar-event"></i> {{ __('Project Daily Updates') }}
        </button>
        <button class="hrm-tab-btn" onclick="switchTab(event, 'general-tasks-tab')">
            <i class="ti ti-checklist"></i> {{ __('General Daily Tasks') }}
        </button>
    </div>

    {{-- Project Daily Updates Tab --}}
    <div id="project-updates-tab" class="hrm-tab-content active">
        {{-- Status Pills --}}
        @php
            $approvedCount = $updates->where('status', 'approved')->count();
            $pendingCount  = $updates->where('status', 'pending')->count();
            $rejectedCount = $updates->where('status', 'rejected')->count();
        @endphp
        <div class="row mb-1 mt-4">
            <!-- Approved -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div style="background:#fff; border-radius:16px; border:1px solid #e8edf5; padding:20px; box-shadow:0 2px 12px rgba(0,26,59,0.05); display:flex; align-items:center; gap:16px;">
                    <div style="width:50px; height:50px; background:#d1fae5; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#059669; font-size:1.5rem;">
                        <i class="ti ti-circle-check"></i>
                    </div>
                    <div>
                        <p style="margin:0; font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">{{ __('Approved') }}</p>
                        <h3 style="margin:0; font-size:1.5rem; font-weight:800; color:#1e293b;">{{ $approvedCount }}</h3>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div style="background:#fff; border-radius:16px; border:1px solid #e8edf5; padding:20px; box-shadow:0 2px 12px rgba(0,26,59,0.05); display:flex; align-items:center; gap:16px;">
                    <div style="width:50px; height:50px; background:#fef3c7; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#d97706; font-size:1.5rem;">
                        <i class="ti ti-clock"></i>
                    </div>
                    <div>
                        <p style="margin:0; font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">{{ __('Pending') }}</p>
                        <h3 style="margin:0; font-size:1.5rem; font-weight:800; color:#1e293b;">{{ $pendingCount }}</h3>
                    </div>
                </div>
            </div>
            <!-- Rejected -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div style="background:#fff; border-radius:16px; border:1px solid #e8edf5; padding:20px; box-shadow:0 2px 12px rgba(0,26,59,0.05); display:flex; align-items:center; gap:16px;">
                    <div style="width:50px; height:50px; background:#fee2e2; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#dc2626; font-size:1.5rem;">
                        <i class="ti ti-circle-x"></i>
                    </div>
                    <div>
                        <p style="margin:0; font-size:0.8rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.04em;">{{ __('Rejected') }}</p>
                        <h3 style="margin:0; font-size:1.5rem; font-weight:800; color:#1e293b;">{{ $rejectedCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="hrm-main-card">
            <div class="hrm-card-hdr">
                <h5>{{ __('Project Daily Updates') }}</h5>
                <span style="background:#f1f5f9; color:#475569; border-radius:8px; padding:4px 12px; font-size:0.78rem; font-weight:700; margin-left:auto;">
                    {{ $updates->count() }} {{ __('records') }}
                </span>
            </div>
            <div style="overflow-x:auto;">
                <table class="hrm-table" id="pc-dt-simple">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Project') }}</th>
                            <th>{{ __('Module') }}</th>
                            <th>{{ __('Work Done') }}</th>
                            <th>{{ __('Hours') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($updates as $update)
                        <tr>
                            <td>
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Auth::user()->dateFormat($update->work_date) }}
                                </span>
                            </td>
                            <td>
                                <span class="hrm-project-link">{{ $update->project->project_name ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="hrm-module-tag">{{ $update->module->module_name ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="hrm-work-text">{{ $update->work_done }}</div>
                            </td>
                            <td>
                                <span class="hrm-hours-tag"><i class="ti ti-clock me-1"></i>{{ $update->hours_worked }}h</span>
                            </td>
                            <td>
                                @if($update->status == 'approved')
                                    <span class="hrm-badge hrm-badge-approved"><i class="ti ti-circle-check"></i> {{ __('Approved') }}</span>
                                @elseif($update->status == 'rejected')
                                    <span class="hrm-badge hrm-badge-rejected"><i class="ti ti-circle-x"></i> {{ __('Rejected') }}</span>
                                @else
                                    <span class="hrm-badge hrm-badge-pending"><i class="ti ti-clock"></i> {{ __('Pending') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if($updates->count() == 0)
                        <tr>
                            <td colspan="6" class="hrm-empty-cell">
                                <div class="ico"><i class="ti ti-report-off"></i></div>
                                <h6 style="color:#374151; font-weight:700; margin-bottom:4px;">{{ __('No Updates Found') }}</h6>
                                <p style="color:#6b7280; font-size:0.83rem; margin:0;">{{ __('Submit daily updates from your project page.') }}</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- General Daily Tasks Tab --}}
    <div id="general-tasks-tab" class="hrm-tab-content">
        <div class="hrm-main-card">
            <div class="hrm-card-hdr">
                <h5><i class="ti ti-checklist" style="color:#001a3b;"></i> {{ __('General Daily Tasks') }}</h5>
                <span style="background:#f1f5f9; color:#475569; border-radius:8px; padding:4px 12px; font-size:0.78rem; font-weight:700; margin-left:auto;">
                    {{ $generalTasks->count() }} {{ __('records') }}
                </span>
            </div>
            <div style="overflow-x:auto;">
                <table class="hrm-table" id="pc-dt-simple2">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Project Name') }}</th>
                            <th>{{ __('Task Title') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Hours') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($generalTasks as $task)
                        <tr>
                            <td>
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Auth::user()->dateFormat($task->work_date) }}
                                </span>
                            </td>
                            <td>
                                <span class="hrm-project-link">{{ $task->project_name ?? '-' }}</span>
                            </td>
                            <td>
                                <span style="font-weight:700; color:#1e293b;">{{ $task->task_title }}</span>
                            </td>
                            <td>
                                <div class="hrm-work-text">{{ $task->task_description }}</div>
                            </td>
                            <td>
                                @php
                                    $d = (float)$task->duration;
                                    $dHrs = (int)floor($d);
                                    $dMins = (int)round(($d - $dHrs) * 60);
                                    if ($dHrs > 0 && $dMins > 0) {
                                        $dLabel = $dHrs . 'h ' . $dMins . 'min';
                                    } elseif ($dHrs > 0) {
                                        $dLabel = $dHrs . ($dHrs == 1 ? ' hr' : ' hrs');
                                    } else {
                                        $dLabel = $dMins . ' min';
                                    }
                                @endphp
                                <span class="hrm-hours-tag"><i class="ti ti-clock me-1"></i>{{ $dLabel }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editGeneralTaskModal_{{ $task->id }}" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['general-daily-tasks.destroy', $task->id], 'id' => 'delete-task-'.$task->id, 'style' => 'display:inline']) !!}
                                    <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                       onclick="event.preventDefault(); if(confirm('{{ __('Are you sure you want to delete this task?') }}')) { document.getElementById('delete-task-{{ $task->id }}').submit(); }">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                    {!! Form::close() !!}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($generalTasks->count() == 0)
                        <tr>
                            <td colspan="6" class="hrm-empty-cell">
                                <div class="ico"><i class="ti ti-report-off"></i></div>
                                <h6 style="color:#374151; font-weight:700; margin-bottom:4px;">{{ __('No General Tasks Found') }}</h6>
                                <p style="color:#6b7280; font-size:0.83rem; margin:0;">{{ __('Click "Add Daily Task" to log your general operational tasks.') }}</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Project Daily Update Modal --}}
<div class="modal fade hrm-modal" id="addProjectUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-plus me-2"></i>{{ __('Submit Daily Update') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="projectUpdateForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-building"></i> {{ __('Project') }}</label>
                        <select name="project_id" id="project_id_select" class="hrm-modal-select" required onchange="updateModuleDropdown()">
                            <option value="">{{ __('Select Project') }}</option>
                            @foreach($myProjects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-layout-grid"></i> {{ __('Module') }}</label>
                        <select name="module_id" id="module_id_select" class="hrm-modal-select" required disabled>
                            <option value="">{{ __('Select Module') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-calendar"></i> {{ __('Work Date') }}</label>
                        <input type="date" name="work_date" class="hrm-modal-input" style="cursor:pointer;" value="{{ date('Y-m-d') }}" onclick="this.showPicker()" required>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-file-text"></i> {{ __('Work Done') }}</label>
                        <textarea name="work_done" class="hrm-modal-textarea" required placeholder="{{ __('Describe what you completed today...') }}"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-clock"></i> {{ __('Hours Worked') }}</label>
                        <input type="number" step="0.1" name="hours_worked" class="hrm-modal-input" required placeholder="e.g. 7.5">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="hrm-modal-btn-submit"><i class="ti ti-send"></i> {{ __('Submit Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add General Daily Task Modal --}}
<div class="modal fade hrm-modal" id="addGeneralTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-plus me-2"></i>{{ __('Add Daily Task') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generalTaskForm" action="{{ route('general-daily-tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-building"></i> {{ __('Project Name (Optional)') }}</label>
                        <input type="text" name="project_name" class="hrm-modal-input" placeholder="{{ __('e.g. Internal Admin, Temporary Client') }}">
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-calendar"></i> {{ __('Work Date') }}</label>
                        <input type="date" name="work_date" class="hrm-modal-input" style="cursor:pointer;" value="{{ date('Y-m-d') }}" onclick="this.showPicker()" required>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-clock"></i> {{ __('Time Duration') }}</label>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <div style="flex:1;">
                                <select id="add_hours" class="hrm-modal-input" style="cursor:pointer;" onchange="updateAddDuration()">
                                    @for($h = 0; $h <= 12; $h++)
                                        <option value="{{ $h }}">{{ $h }} {{ $h == 1 ? 'Hour' : 'Hours' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div style="flex:1;">
                                <select id="add_minutes" class="hrm-modal-input" style="cursor:pointer;" onchange="updateAddDuration()">
                                    <option value="0">0 Minutes</option>
                                    <option value="5">5 Minutes</option>
                                    <option value="10">10 Minutes</option>
                                    <option value="15">15 Minutes</option>
                                    <option value="20">20 Minutes</option>
                                    <option value="25">25 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                    <option value="35">35 Minutes</option>
                                    <option value="40">40 Minutes</option>
                                    <option value="45">45 Minutes</option>
                                    <option value="50">50 Minutes</option>
                                    <option value="55">55 Minutes</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="duration" id="add_duration_hidden" required>
                        <small id="add_duration_display" style="color:#6366f1; font-weight:600; margin-top:6px; display:block;"></small>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-heading"></i> {{ __('Task Title') }}</label>
                        <input type="text" name="task_title" class="hrm-modal-input" required placeholder="{{ __('e.g. Code refactoring, Server Setup') }}">
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-file-text"></i> {{ __('Task Description') }}</label>
                        <textarea name="task_description" class="hrm-modal-textarea" rows="4" required placeholder="{{ __('Provide description of the task...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="hrm-modal-btn-submit"><i class="ti ti-send"></i> {{ __('Submit Task') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit General Daily Task Modals --}}
@foreach ($generalTasks as $task)
<div class="modal fade hrm-modal" id="editGeneralTaskModal_{{ $task->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-pencil me-2"></i>{{ __('Edit General Daily Task') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('general-daily-tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-building"></i> {{ __('Project Name (Optional)') }}</label>
                        <input type="text" name="project_name" class="hrm-modal-input" value="{{ $task->project_name }}" placeholder="{{ __('e.g. Internal Admin, Temporary Client') }}">
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-calendar"></i> {{ __('Work Date') }}</label>
                        <input type="date" name="work_date" class="hrm-modal-input" style="cursor:pointer;" value="{{ $task->work_date }}" onclick="this.showPicker()" required>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-clock"></i> {{ __('Time Duration') }}</label>
                        @php
                            $editHours = floor($task->duration);
                            $editMins = round(($task->duration - $editHours) * 60);
                            // Round to nearest 5
                            $editMins = round($editMins / 5) * 5;
                            if ($editMins >= 60) { $editHours++; $editMins = 0; }
                        @endphp
                        <div style="display:flex; gap:10px; align-items:center;">
                            <div style="flex:1;">
                                <select id="edit_hours_{{ $task->id }}" class="hrm-modal-input" style="cursor:pointer;" onchange="updateEditDuration({{ $task->id }})">
                                    @for($h = 0; $h <= 12; $h++)
                                        <option value="{{ $h }}" {{ $editHours == $h ? 'selected' : '' }}>{{ $h }} {{ $h == 1 ? 'Hour' : 'Hours' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div style="flex:1;">
                                <select id="edit_minutes_{{ $task->id }}" class="hrm-modal-input" style="cursor:pointer;" onchange="updateEditDuration({{ $task->id }})">
                                    @foreach([0,5,10,15,20,25,30,35,40,45,50,55] as $m)
                                        <option value="{{ $m }}" {{ $editMins == $m ? 'selected' : '' }}>{{ $m }} Minutes</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="duration" id="edit_duration_hidden_{{ $task->id }}" value="{{ $task->duration }}" required>
                        <small id="edit_duration_display_{{ $task->id }}" style="color:#6366f1; font-weight:600; margin-top:6px; display:block;">
                            @if($editHours > 0 && $editMins > 0)
                                {{ $editHours }} hr {{ $editMins }} min
                            @elseif($editHours > 0)
                                {{ $editHours }} {{ $editHours == 1 ? 'hr' : 'hrs' }}
                            @elseif($editMins > 0)
                                {{ $editMins }} min
                            @endif
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-heading"></i> {{ __('Task Title') }}</label>
                        <input type="text" name="task_title" class="hrm-modal-input" value="{{ $task->task_title }}" required placeholder="{{ __('e.g. Code refactoring, Server Setup') }}">
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-file-text"></i> {{ __('Task Description') }}</label>
                        <textarea name="task_description" class="hrm-modal-textarea" rows="4" required placeholder="{{ __('Provide description of the task...') }}">{{ $task->task_description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="hrm-modal-btn-submit"><i class="ti ti-device-floppy"></i> {{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    const projectModules = {
        @php $empId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0; @endphp
        @foreach($myProjects as $project)
            "{{ $project->id }}": [
                @foreach($project->modules as $module)
                    @php $assignedIds = is_array($module->employee_ids) ? $module->employee_ids : []; @endphp
                    @if(in_array($empId, $assignedIds) || in_array((string)$empId, $assignedIds))
                        { id: "{{ $module->id }}", name: "{{ addslashes($module->module_name) }}" },
                    @endif
                @endforeach
            ],
        @endforeach
    };

    // --- Duration Helpers for General Task modals ---
    function formatDurationLabel(hours, minutes) {
        var parts = [];
        if (hours > 0) parts.push(hours + (hours === 1 ? ' hr' : ' hrs'));
        if (minutes > 0) parts.push(minutes + ' min');
        return parts.length ? parts.join(' ') : '0 min';
    }

    function updateAddDuration() {
        var hours   = parseInt(document.getElementById('add_hours').value) || 0;
        var minutes = parseInt(document.getElementById('add_minutes').value) || 0;
        var decimal = hours + (minutes / 60);
        document.getElementById('add_duration_hidden').value = decimal > 0 ? decimal.toFixed(4) : '';
        document.getElementById('add_duration_display').textContent = decimal > 0 ? '= ' + formatDurationLabel(hours, minutes) : '';
    }

    function updateEditDuration(taskId) {
        var hours   = parseInt(document.getElementById('edit_hours_' + taskId).value) || 0;
        var minutes = parseInt(document.getElementById('edit_minutes_' + taskId).value) || 0;
        var decimal = hours + (minutes / 60);
        document.getElementById('edit_duration_hidden_' + taskId).value = decimal > 0 ? decimal.toFixed(4) : '';
        document.getElementById('edit_duration_display_' + taskId).textContent = decimal > 0 ? '= ' + formatDurationLabel(hours, minutes) : '';
    }

    // Validate Add form: must have duration > 0
    document.getElementById('generalTaskForm').addEventListener('submit', function(e) {
        var val = parseFloat(document.getElementById('add_duration_hidden').value) || 0;
        if (val <= 0) {
            e.preventDefault();
            alert('Please select a time duration (at least 1 minute).');
        }
    });

    function updateModuleDropdown(selectedModuleId = null) {
        let projectId = document.getElementById('project_id_select').value;
        let moduleSelect = document.getElementById('module_id_select');
        let form = document.getElementById('projectUpdateForm');
        
        moduleSelect.innerHTML = '<option value="">{{ __('Select Module') }}</option>';
        
        if (projectId) {
            form.action = "{{ url('projects') }}/" + projectId + "/updates";
            moduleSelect.disabled = false;
            let modules = projectModules[projectId] || [];
            modules.forEach(function(module) {
                let option = document.createElement('option');
                option.value = module.id;
                option.textContent = module.name;
                if (selectedModuleId && selectedModuleId == module.id) {
                    option.selected = true;
                }
                moduleSelect.appendChild(option);
            });
        } else {
            form.action = "";
            moduleSelect.disabled = true;
        }
    }

    // Reset modals when they are opened to ensure a fresh form every time
    document.addEventListener('DOMContentLoaded', function() {
        let addGeneralModal = document.getElementById('addGeneralTaskModal');
        if (addGeneralModal) {
            addGeneralModal.addEventListener('show.bs.modal', function () {
                let form = document.getElementById('generalTaskForm');
                form.reset();
                form.querySelector('input[name="project_name"]').value = '';
                form.querySelector('input[name="task_title"]').value = '';
                form.querySelector('textarea[name="task_description"]').value = '';
                document.getElementById('add_hours').value = '0';
                document.getElementById('add_minutes').value = '0';
                document.getElementById('add_duration_hidden').value = '';
                document.getElementById('add_duration_display').textContent = '';
            });
        }

        let addProjectModal = document.getElementById('addProjectUpdateModal');
        if (addProjectModal) {
            addProjectModal.addEventListener('show.bs.modal', function () {
                let form = document.getElementById('projectUpdateForm');
                form.reset();
                form.querySelector('textarea[name="work_done"]').value = '';
                form.querySelector('textarea[name="comment"]').value = '';
                document.getElementById('project_id_select').value = '';
                updateModuleDropdown();
            });
        }
    });

function switchTab(evt, tabId) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("hrm-tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("hrm-tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabId).classList.add("active");
    evt.currentTarget.classList.add("active");

    let mainBtn = document.getElementById('mainCreateBtn');
    if (mainBtn) {
        if (tabId === 'project-updates-tab') {
            mainBtn.setAttribute('data-bs-target', '#addProjectUpdateModal');
        } else {
            mainBtn.setAttribute('data-bs-target', '#addGeneralTaskModal');
        }
    }
}

$(document).ready(function() {
    let hasNewEntries = false;

    $('#projectUpdateForm, #generalTaskForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let formData = form.serialize();
        let submitBtn = form.find('.hrm-modal-btn-submit');
        let originalText = submitBtn.html();
        
        submitBtn.html('<i class="ti ti-loader"></i> {{ __('Submitting...') }}').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            success: function(response) {
                submitBtn.html(originalText).prop('disabled', false);
                hasNewEntries = true;

                if (response.success) {
                    show_toastr('Success', response.success, 'success');
                } else if (response.message) {
                    show_toastr('Success', response.message, 'success');
                } else {
                    show_toastr('Success', '{{ __('Saved successfully.') }}', 'success');
                }

                // Clear input fields for continuous entry
                form.find('textarea[name="work_done"], textarea[name="task_description"], input[name="hours_worked"], input[name="duration"], input[name="task_title"]').val('');
                // Note: project, module, date are kept intact for quick batch entry
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);
                let errorMessage = '{{ __('Something went wrong.') }}';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    for (let key in errors) {
                        errorMessage = errors[key][0];
                        break;
                    }
                } else if (xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message)) {
                    errorMessage = xhr.responseJSON.error || xhr.responseJSON.message;
                }
                show_toastr('Error', errorMessage, 'error');
            }
        });
    });

    $('#addProjectUpdateModal, #addGeneralTaskModal').on('hidden.bs.modal', function () {
        if (hasNewEntries) {
            location.reload();
        }
    });
});
</script>
@endsection
