@extends('layouts.admin')

@php
    $isProjectLead = false;
    if (\Auth::user()->type == 'employee') {
        $emp = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
        if ($emp && $project->project_lead == $emp->id) {
            $isProjectLead = true;
        }
    }
@endphp

@section('page-title')
    {{ __('Project Dashboard') }} - {{ $project->project_name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">{{ __('Project List') }}</a></li>
    <li class="breadcrumb-item">{{ $project->project_name }}</li>
@endsection

@section('action-button')
    @if(\Auth::user()->type == 'employee')
        <a href="#" data-bs-toggle="modal" data-bs-target="#addUpdateModal" class="btn btn-sm btn-info me-2 text-white" data-bs-toggle="tooltip" title="{{ __('Add Daily Update') }}">
            <i class="ti ti-plus text-white"></i>
        </a>
    @endif
    @if(\Auth::user()->type == 'company' || \Auth::user()->isTester())
        <a href="#" data-bs-toggle="modal" data-bs-target="#clientShareModal" class="btn btn-sm btn-success me-2 text-white" data-bs-toggle="tooltip" title="{{ __('Generate Client Link') }}">
            <i class="ti ti-link text-white"></i>
        </a>
    @endif
    @if(\Auth::user()->type == 'company' || \Auth::user()->can('Create Employee') || \Auth::user()->isTester() || $isProjectLead)
        <a href="#" data-bs-toggle="modal" data-bs-target="#addModuleModal" class="btn btn-sm btn-primary text-white" data-bs-toggle="tooltip" title="{{ __('Add Module') }}">
            <i class="ti ti-layout-grid-add text-white"></i>
        </a>
    @endif
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-show-wrap * { font-family: 'Inter', sans-serif; }

    .hrm-btn-primary {
        background: linear-gradient(135deg, #001a3b, #1e3a8a);
        color: #fff; border: none; border-radius: 10px; padding: 8px 18px;
        font-size: 0.82rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,26,59,0.25);
        text-decoration: none;
    }
    .hrm-btn-primary:hover { color: #fff; transform: translateY(-1px); }
    .hrm-btn-secondary {
        background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3);
        border-radius: 10px; padding: 8px 18px; font-size: 0.82rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
        text-decoration: none;
    }
    .hrm-btn-secondary:hover { background: rgba(255,255,255,0.25); color: #fff; }
    .hrm-btn-share {
        background: #e0f2fe;
        color: #0369a1;
        border: 2px solid #bae6fd;
        border-radius: 10px; padding: 7px 18px;
        font-size: 0.82rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s;
        text-decoration: none;
    }
    .hrm-btn-share:hover { background: #bae6fd; color: #0369a1; transform: translateY(-1px); }

    /* Hero Banner */
    .hrm-project-hero {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 50%, #2563eb 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,26,59,0.3);
    }
    .hrm-project-hero::before {
        content: ''; position: absolute; top: -60px; right: -60px;
        width: 250px; height: 250px; border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }
    .hrm-project-hero::after {
        content: ''; position: absolute; bottom: -80px; right: 120px;
        width: 300px; height: 300px; border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .hrm-hero-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.6; margin-bottom: 6px; }
    .hrm-project-hero h2 { font-size: 1.7rem; font-weight: 800; margin: 0 0 6px; position: relative; z-index: 1; color: white; }
    .hrm-hero-client { font-size: 0.88rem; opacity: 0.75; display: flex; align-items: center; gap: 7px; position: relative; z-index: 1; }
    .hrm-hero-meta-row { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 18px; position: relative; z-index: 1; }
    .hrm-hero-meta-chip { background: rgba(255,255,255,0.12); border-radius: 10px; padding: 8px 14px; font-size: 0.78rem; font-weight: 600; display: flex; align-items: center; gap: 6px; backdrop-filter: blur(4px); }

    /* Stat Widgets */
    .hrm-stats-row { display: flex; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
    .hrm-stat-widget {
        flex: 1; min-width: 150px;
        background: #fff; border-radius: 16px; border: 1px solid #e8edf5;
        padding: 22px; box-shadow: 0 2px 12px rgba(0,26,59,0.05);
        display: flex; flex-direction: column; align-items: center; text-align: center;
        transition: all 0.3s ease; position: relative; overflow: hidden;
    }
    .hrm-stat-widget:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,26,59,0.1); }
    .hrm-stat-widget::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
    .hrm-stat-blue::before   { background: linear-gradient(90deg, #001a3b, #3B82F6); }
    .hrm-stat-green::before  { background: linear-gradient(90deg, #059669, #34d399); }
    .hrm-stat-yellow::before { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .hrm-stat-purple::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

    .hrm-stat-icon-wrap { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
    .hrm-stat-icon-wrap i { font-size: 1.3rem; }
    .hrm-stat-icon-blue   { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
    .hrm-stat-icon-green  { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .hrm-stat-icon-yellow { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .hrm-stat-icon-purple { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #6d28d9; }

    .hrm-stat-widget h3 { font-size: 1.6rem; font-weight: 800; color: #1e293b; margin: 0 0 4px; }
    .hrm-stat-widget p  { font-size: 0.75rem; color: #6b7280; font-weight: 600; margin: 0; text-transform: uppercase; letter-spacing: 0.04em; }
    .hrm-stat-health-good   { color: #059669 !important; }
    .hrm-stat-health-bad    { color: #dc2626 !important; }
    .hrm-stat-health-warn   { color: #d97706 !important; }

    /* Section Cards */
    .hrm-section-card { background: #fff; border-radius: 16px; border: 1px solid #e8edf5; box-shadow: 0 2px 12px rgba(0,26,59,0.05); margin-bottom: 24px; overflow: hidden; }
    .hrm-section-hdr { padding: 16px 22px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #fafbfd; }
    .hrm-section-hdr h5 { font-size: 0.93rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px; }
    .hrm-section-hdr h5 i { color: #001a3b; }

    /* Table */
    .hrm-table { width: 100%; border-collapse: collapse; }
    .hrm-table th { background: #f8fafc; color: #6b7280; font-size: 0.71rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 12px 20px; text-align: left; border-bottom: 1px solid #f1f5f9; white-space: nowrap; }
    .hrm-table td { padding: 14px 20px; border-bottom: 1px solid #f8fafc; font-size: 0.83rem; color: #374151; vertical-align: middle; }
    .hrm-table tr:last-child td { border-bottom: none; }
    .hrm-table tbody tr:hover td { background: #fafbfd; }

    /* Badges */
    .hrm-badge { display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; padding: 4px 11px; font-size: 0.72rem; font-weight: 700; }
    .hrm-badge-success  { background: #d1fae5; color: #065f46; }
    .hrm-badge-warning  { background: #fef3c7; color: #92400e; }
    .hrm-badge-danger   { background: #fee2e2; color: #991b1b; }
    .hrm-badge-info     { background: #cffafe; color: #0e7490; }
    .hrm-badge-primary  { background: #dbeafe; color: #1d4ed8; }
    .hrm-badge-secondary{ background: #e2e8f0; color: #475569; }



    /* Avatar group */
    .hrm-avatar-group { display: flex; align-items: center; }
    .hrm-avatar { width: 30px; height: 30px; border-radius: 50%; border: 2px solid #fff; object-fit: cover; margin-right: -8px; }

    /* Action buttons */
    .hrm-icon-btn { width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; transition: all 0.2s; text-decoration: none; }
    .hrm-icon-btn-primary { background: #dbeafe; color: #1d4ed8; }
    .hrm-icon-btn-primary:hover { background: #bfdbfe; color: #1d4ed8; }
    .hrm-icon-btn-info    { background: #cffafe; color: #0e7490; }
    .hrm-icon-btn-info:hover { background: #a5f3fc; color: #0e7490; }
    .hrm-icon-btn-danger  { background: #fee2e2; color: #b91c1c; }
    .hrm-icon-btn-danger:hover { background: #fecaca; color: #b91c1c; }

    /* Activities timeline */
    .hrm-timeline { padding: 8px 0; }
    .hrm-timeline-item { display: flex; gap: 14px; padding: 14px 0; border-bottom: 1px solid #f8fafc; position: relative; }
    .hrm-timeline-item:last-child { border-bottom: none; }
    .hrm-timeline-dot { width: 10px; height: 10px; border-radius: 50%; background: linear-gradient(135deg, #001a3b, #3B82F6); flex-shrink: 0; margin-top: 4px; box-shadow: 0 0 0 3px #e0eaff; }
    .hrm-timeline-content h6 { font-size: 0.82rem; font-weight: 700; color: #1e293b; margin: 0 0 3px; }
    .hrm-timeline-content p  { font-size: 0.78rem; color: #6b7280; margin: 0 0 3px; }
    .hrm-timeline-content small { font-size: 0.72rem; color: #94a3b8; }

    /* Pending updates section */
    .hrm-pending-card { background: #fffbeb; border: 1px solid #fde68a; border-radius: 16px; overflow: hidden; margin-bottom: 24px; }
    .hrm-pending-hdr  { padding: 14px 22px; border-bottom: 1px solid #fde68a; display: flex; align-items: center; gap: 8px; }
    .hrm-pending-hdr h5 { font-size: 0.93rem; font-weight: 700; color: #92400e; margin: 0; display: flex; align-items: center; gap: 8px; }
    .hrm-approve-btn { background: #d1fae5; color: #065f46; border: none; border-radius: 8px; padding: 5px 12px; font-size: 0.76rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px; }
    .hrm-approve-btn:hover { background: #a7f3d0; }
    .hrm-reject-btn  { background: #fee2e2; color: #b91c1c; border: none; border-radius: 8px; padding: 5px 12px; font-size: 0.76rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 4px; }
    .hrm-reject-btn:hover { background: #fecaca; }

    /* Empty */
    .hrm-empty-td { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.85rem; }

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

    .hrm-share-link-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 16px; margin-top: 14px; }
    .hrm-share-link-box label { font-size: 0.78rem; font-weight: 700; color: #059669; display: flex; align-items: center; gap: 5px; margin-bottom: 8px; }
    .hrm-share-link-input-group { display: flex; gap: 8px; }
    .hrm-share-link-input-group input { flex: 1; border: 1px solid #bbf7d0; border-radius: 8px; padding: 7px 12px; font-size: 0.82rem; background: #fff; outline: none; }
    .hrm-copy-btn { background: #059669; color: #fff; border: none; border-radius: 8px; padding: 7px 14px; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
    .hrm-copy-btn:hover { background: #047857; }

    .fade-in { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="hrm-show-wrap fade-in">

    {{-- Hero Banner --}}
    <div class="hrm-project-hero mt-5">
        <div class="hrm-hero-label">{{ __('Project Dashboard') }}</div>
        <h2>{{ $project->project_name }}</h2>
        <div class="hrm-hero-client">
            <i class="ti ti-building"></i>
            {{ __('Client') }}: {{ $project->client_name }}
            @if($project->project_type)
            &nbsp;·&nbsp; <i class="ti ti-tag"></i> {{ $project->project_type }}
            @endif
            @if($project->project_lead)
                @php $lead = \App\Models\Employee::find($project->project_lead); @endphp
                @if($lead)
                &nbsp;·&nbsp; <i class="ti ti-user-check"></i> {{ __('Lead') }}: {{ $lead->name }} {{ $lead->last_name }}
                @endif
            @endif
        </div>
        <div class="hrm-hero-meta-row">
            @if($project->project_startdate)
            <div class="hrm-hero-meta-chip">
                <i class="ti ti-calendar"></i> {{ \Carbon\Carbon::parse($project->project_startdate)->format('d M Y') }}
            </div>
            @endif
            @php
                $isValidEndDate = !empty($project->project_enddate) && !str_starts_with($project->project_enddate, '0000') && !str_starts_with($project->project_enddate, '-0001');
            @endphp
            @if($isValidEndDate)
            <div class="hrm-hero-meta-chip">
                <i class="ti ti-flag"></i> {{ \Carbon\Carbon::parse($project->project_enddate)->format('d M Y') }}
            </div>
            @else
            <div class="hrm-hero-meta-chip" style="opacity: 0.8;">
                <i class="ti ti-flag"></i> {{ __('No Deadline') }}
            </div>
            @endif
            <div class="hrm-hero-meta-chip">
                <i class="ti ti-users"></i> {{ $project->modules->count() }} {{ __('Modules') }}
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="hrm-stats-row">
        <div class="hrm-stat-widget hrm-stat-blue">
            <div class="hrm-stat-icon-wrap hrm-stat-icon-blue"><i class="ti ti-activity"></i></div>
            <h3 class="hrm-stat-health-{{ $project->project_health == 'Critical' ? 'bad' : ($project->project_health == 'Delayed' ? 'warn' : 'good') }}">
                {{ $project->project_health }}
            </h3>
            <p>{{ __('Project Health') }}</p>
        </div>


        <div class="hrm-stat-widget hrm-stat-purple">
            <div class="hrm-stat-icon-wrap hrm-stat-icon-purple"><i class="ti ti-history"></i></div>
            <h3>{{ $project->actual_hours ?: '—' }}</h3>
            <p>{{ __('Actual Hours') }}</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-xl-8">

            {{-- Modules Table --}}
            <div class="hrm-section-card">
                <div class="hrm-section-hdr">
                    <h5><i class="ti ti-layout-grid"></i> {{ __('Project Modules') }}</h5>
                    <span style="background:#f1f5f9; color:#475569; border-radius:8px; padding:3px 10px; font-size:0.75rem; font-weight:700;">
                        {{ $project->modules->count() }} {{ __('Total') }}
                    </span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="hrm-table">
                        <thead>
                            <tr>
                                <th>{{ __('Module') }}</th>
                                <th>{{ __('Team') }}</th>

                                <th>{{ __('Status') }}</th>
                                @if(\Auth::user()->type == 'company' || \Auth::user()->can('Create Employee') || \Auth::user()->isTester() || $isProjectLead)
                                <th style="text-align:center;">{{ __('Actions') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->modules as $module)
                            <tr>
                                <td style="font-weight:700; color:#1e293b;">{{ $module->module_name }}</td>
                                <td>
                                    @php $moduleEmployees = $module->employees(); @endphp
                                    @if($moduleEmployees->count() > 0)
                                    <div class="hrm-avatar-group">
                                        @foreach($moduleEmployees->take(4) as $employee)
                                            @php $profilePath = !empty($employee->user->avatar) ? asset('storage/uploads/avatar/' . $employee->user->avatar) : asset('storage/uploads/avatar/avatar.png'); @endphp
                                            <img alt="{{ $employee->name }}" data-bs-toggle="tooltip" title="{{ $employee->name }}" src="{{ $profilePath }}" class="hrm-avatar">
                                        @endforeach
                                        @if($moduleEmployees->count() > 4)
                                            <span style="width:30px;height:30px;border-radius:50%;background:#e0e7ff;color:#3730a3;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:800;border:2px solid #fff;margin-left:-8px;">+{{ $moduleEmployees->count()-4 }}</span>
                                        @endif
                                    </div>
                                    @else
                                        <span style="font-size:0.78rem; color:#94a3b8;">{{ __('Unassigned') }}</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="hrm-badge {{ $module->status == 'completed' ? 'hrm-badge-success' : 'hrm-badge-warning' }}">
                                        {{ ucfirst($module->status) }}
                                    </span>
                                </td>
                                @if(\Auth::user()->type == 'company' || \Auth::user()->can('Create Employee') || \Auth::user()->isTester() || $isProjectLead)
                                <td>
                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="#" class="btn btn-sm btn-info"
                                           data-url="{{ route('project-modules.edit', $module->id) }}"
                                           data-ajax-popup="true" data-size="md" data-title="{{ __('Edit Module') }}" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['project-modules.destroy', $module->id], 'id' => 'delete-module-'.$module->id, 'style' => 'display:inline']) !!}
                                        <a href="#" class="btn btn-sm btn-danger bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                           onclick="event.preventDefault(); document.getElementById('delete-module-{{ $module->id }}').submit();">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                        {!! Form::close() !!}
                                    </div>


                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @if($project->modules->count() == 0)
                            <tr><td colspan="5" class="hrm-empty-td">{{ __('No modules found. Add modules to start tracking.') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Design Reviews --}}
            <div class="hrm-section-card">
                <div class="hrm-section-hdr">
                    <h5><i class="ti ti-layout-grid"></i> {{ __('Design Reviews') }}</h5>
                    @php
                        $isDesigner = false;
                        if(\Auth::user()->type == 'employee') {
                            $emp = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
                            if($emp) {
                                $dept = \App\Models\Department::find($emp->department_id);
                                if($dept && in_array($dept->name, ['UI-UX Designer','UI/UX Designer'])) { $isDesigner = true; }
                            }
                        }
                    @endphp
                    @if(\Auth::user()->type == 'company' || \Auth::user()->can('Create Employee') || $isDesigner || \Auth::user()->isTester())
                    <a href="{{ route('designs.index', ['project_id' => $project->id]) }}" class="hrm-badge hrm-badge-primary" style="text-decoration:none;">
                        <i class="ti ti-external-link"></i> {{ __('Manage Designs') }}
                    </a>
                    @endif
                </div>
                <div style="overflow-x:auto;">
                    <table class="hrm-table">
                        <thead>
                            <tr>
                                <th>{{ __('Design Title') }}</th>
                                <th>{{ __('Latest Version') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $designs = \App\Models\Design::where('project_id', $project->id)->with('latestVersion')->get(); @endphp
                            @foreach($designs as $design)
                            <tr>
                                <td style="font-weight:600; color:#1e293b;">
                                    {{ $design->title }}
                                    @php
                                        $pc = 0;
                                        if($design->latestVersion) {
                                            $pc = \App\Models\DesignFeedback::where('design_version_id', $design->latestVersion->id)->where('status','Pending')->count();
                                        }
                                    @endphp
                                    @if($pc > 0)
                                        <span class="hrm-badge hrm-badge-danger ms-1" style="font-size:0.68rem; animation: hrm-pulse 2s infinite;">
                                            <i class="ti ti-bell"></i> {{ $pc }} New
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span style="background:#f1f5f9; color:#475569; border-radius:6px; padding:2px 9px; font-size:0.76rem; font-weight:700; font-family:monospace;">
                                        {{ $design->latestVersion ? $design->latestVersion->version : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($design->latestVersion)
                                        <span class="hrm-badge {{ $design->latestVersion->status == 'Approved' ? 'hrm-badge-success' : ($design->latestVersion->status == 'Rejected' ? 'hrm-badge-danger' : 'hrm-badge-warning') }}">
                                            {{ $design->latestVersion->status }}
                                        </span>
                                    @else
                                        <span class="hrm-badge hrm-badge-secondary">{{ __('No Version') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('designs.show', $design->id) }}" class="hrm-icon-btn hrm-icon-btn-info" title="{{ __('View Details') }}">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @if($designs->count() == 0)
                            <tr><td colspan="4" class="hrm-empty-td">{{ __('No design reviews found.') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pending Daily Updates (Admin) --}}
            @if(\Auth::user()->type == 'company' || \Auth::user()->can('Create Employee') || \Auth::user()->isTester())
            @php $pendingUpdates = $project->dailyUpdates->where('status', 'pending'); @endphp
            @if($pendingUpdates->count() > 0)
            <div class="hrm-pending-card">
                <div class="hrm-pending-hdr">
                    <h5><i class="ti ti-clock"></i> {{ __('Pending Daily Updates') }}
                        <span class="hrm-badge hrm-badge-warning">{{ $pendingUpdates->count() }}</span>
                    </h5>
                </div>
                <div style="overflow-x:auto;">
                    <table class="hrm-table">
                        <thead>
                            <tr>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Module') }}</th>
                                <th>{{ __('Work Done') }}</th>
                                <th>{{ __('Hours') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUpdates as $update)
                            <tr>
                                <td style="font-weight:600;">{{ $update->employee->name ?? '-' }}</td>
                                <td><span class="hrm-badge hrm-badge-info">{{ $update->module->module_name ?? '-' }}</span></td>
                                <td style="max-width:220px; font-size:0.82rem;">{{ $update->work_done }}</td>
                                <td><span class="hrm-badge hrm-badge-secondary">{{ $update->hours_worked }}h</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('project-updates.approve', $update->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="hrm-approve-btn"><i class="ti ti-check"></i> {{ __('Approve') }}</button>
                                        </form>
                                        <form action="{{ route('project-updates.reject', $update->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="hrm-reject-btn"><i class="ti ti-x"></i> {{ __('Reject') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @endif

            {{-- Project Documents Section --}}
            <div class="hrm-section-card mb-4">
                <div class="hrm-section-hdr" style="display:flex; justify-content:space-between; align-items:center;">
                    <h5><i class="ti ti-files"></i> {{ __('Project Documents') }}</h5>
                </div>
                <div style="padding: 24px;">
                    <form action="{{ route('project-documents.store', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 24px; display:flex; gap: 12px; align-items:flex-end; background:#f8fafc; padding:16px; border-radius:12px; border:1px dashed #cbd5e1; flex-wrap:wrap;">
                        @csrf
                        <div style="flex:1; min-width:200px;">
                            <label style="font-size:0.8rem; font-weight:600; color:#475569; margin-bottom:4px; display:block;">{{ __('Document Name (Optional)') }}</label>
                            <input type="text" name="file_name" class="form-control" placeholder="e.g. Requirements V2" style="border-radius:8px; border:1px solid #e2e8f0; padding:8px 12px; font-size:0.9rem;">
                        </div>
                        <div style="flex:1; min-width:200px;">
                            <label style="font-size:0.8rem; font-weight:600; color:#475569; margin-bottom:4px; display:block;">{{ __('File') }} *</label>
                            <input type="file" name="document" class="form-control" required style="border-radius:8px; border:1px solid #e2e8f0; padding:5px 12px; font-size:0.9rem;">
                        </div>
                        <div>
                            <button type="submit" class="hrm-btn-primary" style="padding: 9px 18px;"><i class="ti ti-upload"></i> {{ __('Upload') }}</button>
                        </div>
                    </form>

                    @if($project->documents && $project->documents->count() > 0)
                        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                            @foreach($project->documents as $doc)
                            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; display:flex; gap:16px; align-items:center; transition:all 0.2s; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
                                <div style="width:48px; height:48px; border-radius:10px; background:#e0f2fe; color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0;">
                                    <i class="ti ti-file"></i>
                                </div>
                                <div style="flex:1; overflow:hidden;">
                                    <h6 style="margin:0 0 4px; font-weight:700; font-size:0.95rem; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $doc->file_name }}">
                                        {{ $doc->file_name }}
                                    </h6>
                                    <div style="font-size:0.75rem; color:#64748b; margin-bottom:6px;">
                                        {{ __('By') }} {{ $doc->uploader->name ?? __('Unknown') }} • {{ $doc->created_at->format('M d, Y') }}
                                    </div>
                                    <div style="display:flex; gap:10px;">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#docModal{{ $doc->id }}" style="font-size:0.8rem; font-weight:600; color:#0ea5e9; text-decoration:none;"><i class="ti ti-eye"></i> {{ __('View') }}</a>
                                        @if(\Auth::user()->type == 'company' || \Auth::id() == $doc->uploaded_by || \Auth::user()->isTester())
                                        <form action="{{ route('project-documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('{{ __('Delete this document?') }}');" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="background:none; border:none; color:#ef4444; font-size:0.8rem; font-weight:600; padding:0; cursor:pointer;"><i class="ti ti-trash"></i> {{ __('Delete') }}</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align:center; padding:40px; color:#94a3b8; background:#f8fafc; border-radius:12px; border:1px dashed #e2e8f0;">
                            <i class="ti ti-file-off" style="font-size:2rem; margin-bottom:10px; display:block; color:#cbd5e1;"></i>
                            {{ __('No documents uploaded yet.') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="col-xl-4">
            <div class="hrm-section-card" style="position: sticky; top: 20px;">
                <div class="hrm-section-hdr">
                    <h5><i class="ti ti-activity"></i> {{ __('Recent Activities') }}</h5>
                </div>
                <div style="padding: 8px 22px 16px;">
                    @if($project->activities->count() > 0)
                    <div class="hrm-timeline">
                        @foreach($project->activities->sortByDesc('created_at')->take(12) as $activity)
                        <div class="hrm-timeline-item">
                            <div class="hrm-timeline-dot"></div>
                            <div class="hrm-timeline-content">
                                <h6>{{ $activity->activity_type }}</h6>
                                <p>{{ $activity->activity }}</p>
                                <small>{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="color:#94a3b8; text-align:center; padding: 40px 0; font-size:0.85rem;">{{ __('No activities yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Daily Update Modal (Employee) --}}
@if(\Auth::user()->type == 'employee')
<div class="modal fade hrm-modal" id="addUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-plus me-2"></i>{{ __('Submit Daily Update') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('project-updates.store', $project->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-layout-grid"></i> {{ __('Module') }}</label>
                        <select name="module_id" class="hrm-modal-select" required>
                            <option value="">{{ __('Select Module') }}</option>
                            @php $empId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0; @endphp
                            @foreach($project->modules as $module)
                                @php $assignedIds = is_array($module->employee_ids) ? $module->employee_ids : []; @endphp
                                @if(in_array($empId, $assignedIds) || in_array((string)$empId, $assignedIds))
                                    <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-calendar"></i> {{ __('Work Date') }}</label>
                        <input type="date" name="work_date" class="hrm-modal-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-file-text"></i> {{ __('Work Done') }}</label>
                        <textarea name="work_done" class="hrm-modal-textarea" rows="3" required placeholder="{{ __('Describe what you completed today...') }}"></textarea>
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
@endif

{{-- Add Module Modal --}}
@if(\Auth::user()->type == 'company' || \Auth::user()->can('Create Employee') || \Auth::user()->isTester() || $isProjectLead)
<div class="modal fade hrm-modal" id="addModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-plus me-2"></i>{{ __('Add Project Module') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('project-modules.store', $project->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-typography"></i> {{ __('Module Name') }}</label>
                        <input type="text" name="module_name" class="hrm-modal-input" required placeholder="{{ __('e.g. Login System, UI Design') }}">
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-file-text"></i> {{ __('Description') }}</label>
                        <textarea name="description" class="hrm-modal-textarea" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-users"></i> {{ __('Assign Employees') }}</label>
                        <select name="employee_ids[]" id="module-employee-choices" class="hrm-modal-select" multiple="multiple" data-placeholder="{{ __('Select Employees') }}">
                            @foreach($project->getEmployeeNames() as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <small style="color:#6b7280; font-size:0.75rem; margin-top:4px; display:block;">{{ __('Only employees assigned to this project are listed.') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="hrm-modal-btn-submit"><i class="ti ti-plus"></i> {{ __('Create Module') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Client Sharing Modal --}}
@if(\Auth::user()->type == 'company' || \Auth::user()->isTester())
<div class="modal fade hrm-modal" id="clientShareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-share me-2"></i>{{ __('Client Sharing Settings') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('projects.share_settings', $project->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4 p-3 rounded-3" style="background:#f8fafc; border:1px solid #e8edf5;">
                        <div class="d-flex align-items-center gap-3">
                            <input type="checkbox" class="form-check-input" id="share_link_enabled" name="share_link_enabled" {{ $project->share_link_enabled ? 'checked' : '' }} style="width:18px;height:18px;">
                            <label for="share_link_enabled" style="cursor:pointer; margin:0;">
                                <div style="font-weight:700; font-size:0.88rem; color:#1e293b;">{{ __('Enable Shareable Client Link') }}</div>
                                <div style="font-size:0.75rem; color:#6b7280;">{{ __('Generate a public URL for the client to view progress') }}</div>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-lock"></i> {{ __('Password Protection') }}</label>
                        <input type="text" name="share_password" class="hrm-modal-input" value="{{ $project->share_password }}" placeholder="{{ __('Leave blank for no password') }}">
                        <small style="color:#6b7280; font-size:0.75rem; margin-top:4px; display:block;">{{ __('If set, the client must enter this password.') }}</small>
                    </div>
                    @if($project->share_link_enabled && $project->share_token)
                    <div class="hrm-share-link-box">
                        <label><i class="ti ti-link"></i> {{ __('Shareable Link') }}</label>
                        <div class="hrm-share-link-input-group">
                            <input type="text" value="{{ route('project.public.report', $project->share_token) }}" readonly id="clientLinkUrl">
                            <button class="hrm-copy-btn" type="button" onclick="copyToClipboard('clientLinkUrl')">{{ __('Copy') }}</button>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="hrm-modal-btn-submit"><i class="ti ti-device-floppy"></i> {{ __('Save Settings') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function copyToClipboard(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(function() {
        var btn = copyText.nextElementSibling;
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="ti ti-check"></i> Copied!';
        btn.style.background = '#059669';
        setTimeout(function() {
            btn.innerHTML = original;
            btn.style.background = '';
        }, 2000);
    });
}
</script>
@endif

@push('script-page')
<script>
$(document).ready(function() {
    $('#addModuleModal').on('shown.bs.modal', function () {
        if (!window.moduleEmployeeChoices) {
            window.moduleEmployeeChoices = new Choices('#module-employee-choices', {
                removeItemButton: true,
            });
        }
    });
});
</script>
@endpush

<style>
@keyframes hrm-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
    50% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
}
</style>

{{-- Document Preview Modals --}}
@foreach($project->documents as $doc)
<div class="modal fade hrm-modal" id="docModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-file me-2"></i>{{ $doc->file_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#f1f5f9; display:flex; justify-content:center; align-items:center; min-height:400px; padding:20px;">
                @php 
                    $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                    <img src="{{ Storage::url($doc->file_path) }}" style="max-width:100%; max-height:65vh; border-radius:8px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);" alt="{{ $doc->file_name }}">
                @elseif(in_array($ext, ['pdf']))
                    <iframe src="{{ Storage::url($doc->file_path) }}" width="100%" height="550px" style="border:none; border-radius:8px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);"></iframe>
                @else
                    <div style="text-align:center; color:#64748b;">
                        <i class="ti ti-file" style="font-size:4rem; margin-bottom:16px; color:#cbd5e1;"></i>
                        <p style="margin:0;">{{ __('Preview not available for this file type.') }}</p>
                        <small>{{ __('Please download the file to view it.') }}</small>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <a href="{{ route('project-documents.download', $doc->id) }}" class="hrm-btn-primary" style="text-decoration:none;"><i class="ti ti-download"></i> {{ __('Download File') }}</a>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
