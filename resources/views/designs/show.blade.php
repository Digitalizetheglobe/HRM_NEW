@extends('layouts.admin')

@section('page-title')
    {{ __('Design Details') }} - {{ $design->title }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects.show', $design->project_id) }}">{{ $design->project->project_name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('designs.index', ['project_id' => $design->project_id]) }}">{{ __('Designs') }}</a></li>
    <li class="breadcrumb-item">{{ $design->title }}</li>
@endsection

@section('action-button')
    @php
        $canAddVersion = false;
        if (\Auth::user()->type !== 'employee') {
            $canAddVersion = true;
        } else {
            $btnEmp = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
            if ($btnEmp) {
                $btnDept = \App\Models\Department::find($btnEmp->department_id);
                $btnDeptName = $btnDept ? strtolower(trim($btnDept->name)) : '';
                $designerNames = ['ui-ux designer', 'ui/ux designer', 'ui-ux', 'uiux designer', 'designer', 'graphic designer', 'ui ux designer', 'tester', 'quality assurance', 'qa'];
                $canAddVersion = in_array($btnDeptName, $designerNames);
            }
        }
    @endphp
    @if($canAddVersion)
        <a href="#" data-bs-toggle="modal" data-bs-target="#addVersionModal" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Add Version') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endif
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-show-wrap * { font-family: 'Inter', sans-serif; }

    .hrm-btn-primary {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        color: #fff; border: none; border-radius: 10px;
        padding: 8px 20px; font-size: 0.85rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
        transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(0,26,59,0.25);
    }
    .hrm-btn-primary:hover { color: #fff; transform: translateY(-1px); }

    /* Hero Info Card */
    .hrm-hero-card {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        border-radius: 20px;
        padding: 32px;
        color: #fff;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,26,59,0.25);
    }
    .hrm-hero-card::after {
        content: '';
        position: absolute; top: -30px; right: -30px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }
    .hrm-hero-card::before {
        content: '';
        position: absolute; bottom: -50px; right: 80px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .hrm-hero-badge {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 4px 14px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 14px;
        backdrop-filter: blur(10px);
    }
    .hrm-hero-card h2 {
        font-size: 1.6rem; font-weight: 800; margin-bottom: 8px; position: relative; z-index: 1; color: #fff
    }
    .hrm-hero-meta {
        display: flex; flex-wrap: wrap; gap: 20px; margin-top: 16px; position: relative; z-index: 1;
    }
    .hrm-hero-meta-item { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; opacity: 0.85; }
    .hrm-hero-meta-item i { font-size: 1rem; opacity: 0.7; }
    .hrm-hero-edit-btn {
        position: absolute; top: 28px; right: 32px;
        background: rgba(255,255,255,0.15);
        color: #fff; border: none; border-radius: 10px;
        padding: 8px 16px; font-size: 0.82rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s; backdrop-filter: blur(10px); z-index: 2;
    }
    .hrm-hero-edit-btn:hover { background: rgba(255,255,255,0.25); color: #fff; }

    /* Section card */
    .hrm-section-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 12px rgba(0,26,59,0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .hrm-section-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfd;
    }
    .hrm-section-header h5 {
        font-size: 0.95rem; font-weight: 700; color: #1e293b;
        margin: 0; display: flex; align-items: center; gap: 8px;
    }
    .hrm-section-header h5 i { color: #001a3b; }
    .hrm-section-body { padding: 4px 0; }

    /* Table */
    .hrm-table { width: 100%; border-collapse: collapse; }
    .hrm-table th {
        background: #f8fafc; color: #6b7280; font-size: 0.72rem;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        padding: 12px 20px; text-align: left; border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .hrm-table td {
        padding: 14px 20px; border-bottom: 1px solid #f8fafc;
        font-size: 0.85rem; color: #374151; vertical-align: middle;
    }
    .hrm-table tr:last-child td { border-bottom: none; }
    .hrm-table tr:hover td { background: #fafbfd; }

    /* Status badges */
    .hrm-badge {
        display: inline-flex; align-items: center; gap: 5px;
        border-radius: 20px; padding: 4px 12px;
        font-size: 0.73rem; font-weight: 700;
    }
    .hrm-badge-approved { background: #d1fae5; color: #065f46; }
    .hrm-badge-pending  { background: #fef3c7; color: #92400e; }
    .hrm-badge-resolved { background: #dbeafe; color: #1d4ed8; }
    .hrm-badge-danger   { background: #fee2e2; color: #991b1b; }
    .hrm-badge-secondary{ background: #e2e8f0; color: #475569; }
    .hrm-badge-review   { background: #ede9fe; color: #6d28d9; }
    .hrm-badge-info     { background: #cffafe; color: #0e7490; }

    .hrm-version-badge {
        background: #f1f5f9; color: #475569;
        border-radius: 8px; padding: 3px 10px;
        font-size: 0.75rem; font-weight: 700;
        font-family: monospace;
    }

    /* Thumbnail */
    .hrm-thumb {
        width: 42px; height: 42px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e8edf5;
        transition: transform 0.2s;
    }
    .hrm-thumb:hover { transform: scale(1.5); z-index: 10; position: relative; }

    /* Action buttons */
    .hrm-tbl-btn {
        display: inline-flex; align-items: center; gap: 4px;
        border-radius: 8px; padding: 5px 10px;
        font-size: 0.76rem; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        transition: all 0.2s ease;
    }
    .hrm-tbl-btn-info    { background: #e0f2fe; color: #0369a1; }
    .hrm-tbl-btn-info:hover { background: #bae6fd; }
    .hrm-tbl-btn-success { background: #d1fae5; color: #065f46; }
    .hrm-tbl-btn-success:hover { background: #a7f3d0; }

    /* Empty */
    .hrm-empty-row td { text-align: center; padding: 48px 20px; color: #94a3b8; }
    .hrm-empty-row .hrm-empty-icon { font-size: 2rem; margin-bottom: 8px; opacity: 0.4; display: block; }

    /* Modal */
    .hrm-modal .modal-content { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: hidden; }
    .hrm-modal .modal-header {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        padding: 20px 24px; border: none;
    }
    .hrm-modal .modal-title { color: #fff; font-weight: 700; font-size: 1rem; }
    .hrm-modal .btn-close { filter: brightness(0) invert(1); }
    .hrm-modal .modal-body { padding: 24px; }
    .hrm-modal .modal-footer { padding: 16px 24px; background: #fafbfd; border-top: 1px solid #f1f5f9; }

    .hrm-modal-label {
        font-size: 0.8rem; font-weight: 600; color: #374151;
        text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;
        display: flex; align-items: center; gap: 6px;
    }
    .hrm-modal-label i { color: #001a3b; }
    .hrm-modal-input, .hrm-modal-select {
        width: 100%; border: 2px solid #e8edf5; border-radius: 10px;
        padding: 9px 13px; font-size: 0.85rem; color: #1e293b;
        background: #fafbfd; outline: none; transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .hrm-modal-input:focus, .hrm-modal-select:focus { border-color: #001a3b; background: #fff; box-shadow: 0 0 0 4px rgba(0,26,59,0.08); }
    .hrm-switch-label { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #374151; cursor: pointer; }

    .hrm-btn-cancel {
        background: #f1f5f9; color: #374151; border: none; border-radius: 10px;
        padding: 9px 20px; font-size: 0.84rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 5px; transition: all 0.2s; cursor: pointer;
    }
    .hrm-btn-cancel:hover { background: #e2e8f0; }
    .hrm-btn-submit {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        color: #fff; border: none; border-radius: 10px;
        padding: 9px 22px; font-size: 0.84rem; font-weight: 700;
        display: inline-flex; align-items: center; gap: 5px;
        cursor: pointer; transition: all 0.2s;
        box-shadow: 0 4px 14px rgba(0,26,59,0.25);
    }
    .hrm-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,26,59,0.35); }

    .hrm-file-link {
        display: inline-flex; align-items: center; gap: 4px;
        background: #e0f2fe; color: #0369a1;
        border-radius: 6px; padding: 3px 8px;
        font-size: 0.75rem; font-weight: 600; text-decoration: none;
    }
    .hrm-file-link:hover { background: #bae6fd; color: #0369a1; }

    .hrm-versions-section { padding: 20px 24px; }
    .hrm-version-group {
        background: #fafbfd; border: 1px solid #e8edf5;
        border-radius: 12px; margin-bottom: 12px; overflow: hidden;
    }
    .hrm-version-group-header {
        padding: 14px 18px; display: flex; align-items: center; gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
    }
    .hrm-version-links { padding: 14px 18px; }
    .hrm-version-link-item {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 0; border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    .hrm-version-link-item:last-child { border-bottom: none; }
    .hrm-version-link-item a { color: #001a3b; font-weight: 600; text-decoration: none; }
    .hrm-version-link-item a:hover { text-decoration: underline; }

    .hrm-status-update-form { display: inline-flex; align-items: center; gap: 8px; margin-left: auto; }
    .hrm-status-select { border: 2px solid #e8edf5; border-radius: 8px; padding: 4px 10px; font-size: 0.78rem; font-weight: 600; color: #374151; outline: none; cursor: pointer; }
    .hrm-status-update-btn { background: #001a3b; color: #fff; border: none; border-radius: 8px; padding: 5px 12px; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
    .hrm-status-update-btn:hover { background: #1e3a8a; }

    .fade-in { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
</style>

@php
    // Role detection
    $isDesigner = false;
    $isDeveloper = false;
    if (\Auth::user()->type === 'employee') {
        $authEmp = \App\Models\Employee::where('user_id', \Auth::user()->id)->first();
        if ($authEmp) {
            $dept = \App\Models\Department::find($authEmp->department_id);
            $deptName = $dept ? strtolower(trim($dept->name)) : '';
            $designerDepts = ['ui-ux designer', 'ui/ux designer', 'ui-ux', 'uiux designer', 'designer', 'graphic designer', 'ui ux designer', 'tester', 'quality assurance', 'qa'];
            $isDesigner = in_array($deptName, $designerDepts);
            $isDeveloper = !$isDesigner;
        }
    } else {
        // Company/admin can see everything
        $isDesigner = true;
    }
@endphp

<div class="hrm-show-wrap fade-in">

    {{-- Hero Info Card --}}
    <div class="hrm-hero-card mt-5">
        <a href="{{ route('designs.edit', $design->id) }}" class="hrm-hero-edit-btn">
            <i class="ti ti-pencil"></i> {{ __('Edit') }}
        </a>
        <div class="hrm-hero-badge">
            <i class="ti ti-layout-grid"></i> {{ __('Design') }}
        </div>
        <h2>{{ $design->title }}</h2>
        @if($design->description)
            <p style="opacity:0.75; margin:0; font-size:0.9rem; position:relative; z-index:1;">{{ $design->description }}</p>
        @endif
        <div class="hrm-hero-meta">
            <div class="hrm-hero-meta-item">
                <i class="ti ti-folder"></i>
                {{ $design->project->project_name }}
            </div>
            <div class="hrm-hero-meta-item">
                <i class="ti ti-user"></i>
                {{ __('By') }}: {{ $design->creator->name ?? '-' }}
            </div>
            <div class="hrm-hero-meta-item">
                <i class="ti ti-versions"></i>
                {{ $design->versions->count() }} {{ __('Versions') }}
            </div>
        </div>
    </div>

    {{-- Versions List --}}
    @php
        // Developers only see Approved versions
        $visibleVersions = $isDeveloper
            ? $design->versions->where('status', 'Approved')
            : $design->versions;
    @endphp

    @if($visibleVersions->count() > 0)
    <div class="hrm-section-card mb-4">
        <div class="hrm-section-header">
            <h5><i class="ti ti-versions"></i>
                @if($isDeveloper)
                    {{ __('Approved Versions') }}
                @else
                    {{ __('All Versions') }}
                @endif
            </h5>
        </div>
        <div class="hrm-versions-section">
            @foreach($visibleVersions as $version)
            <div class="hrm-version-group">
                <div class="hrm-version-group-header">
                    <span class="hrm-version-badge">{{ $version->version }}</span>
                    @php
                        $vs = $version->status;
                        $vc = match($vs) {
                            'Approved' => 'hrm-badge-approved',
                            'Rejected' => 'hrm-badge-danger',
                            'Draft'    => 'hrm-badge-secondary',
                            default    => 'hrm-badge-review',
                        };
                    @endphp
                    <span class="hrm-badge {{ $vc }}">{{ $vs }}</span>
                    @if($version->client_visible)
                        <span class="hrm-badge hrm-badge-info"><i class="ti ti-eye"></i> {{ __('Client Visible') }}</span>
                    @endif

                    {{-- Only Designers can update version status --}}
                    @if($isDesigner)
                    <div style="display:flex; gap: 8px; align-items:center;">
                        <form action="{{ route('design-versions.update', $version->id) }}" method="POST" class="hrm-status-update-form" style="margin:0;">
                            @csrf @method('PUT')
                            <select name="status" class="hrm-status-select">
                                @foreach(['Draft','Internal Review','Client Review','Feedback Received','Changes In Progress','Ready For Approval','Approved','Rejected','Archived'] as $opt)
                                <option value="{{ $opt }}" {{ $version->status == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="hrm-status-update-btn">{{ __('Update') }}</button>
                        </form>
                        <form action="{{ route('design-versions.destroy', $version->id) }}" method="POST" class="d-inline" style="margin:0;" onsubmit="return confirm('{{ __('Are you sure you want to delete this version? All associated feedback and links will also be lost.') }}');">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:#ef4444; border:none; color:white; border-radius:6px; padding:6px 12px; cursor:pointer;" title="{{ __('Delete Version') }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                @if($version->links->count() > 0)
                <div class="hrm-version-links">
                    <p style="font-size:0.78rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:8px;">
                        <i class="ti ti-link me-1"></i> {{ __('Figma Links') }}
                    </p>
                    @foreach($version->links as $link)
                    <div class="hrm-version-link-item">
                        <i class="ti ti-external-link" style="color:#001a3b;"></i>
                        <a href="{{ $link->url }}" target="_blank">{{ $link->title }}</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @elseif($isDeveloper)
    <div class="hrm-section-card mb-4">
        <div class="hrm-section-header">
            <h5><i class="ti ti-versions"></i> {{ __('Approved Versions') }}</h5>
        </div>
        <div style="text-align:center; padding:48px 24px; color:#94a3b8;">
            <div style="width:72px;height:72px;background:linear-gradient(135deg,#e8f0fe,#c7d8f8);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i class="ti ti-clock" style="font-size:1.8rem;color:#001a3b;"></i>
            </div>
            <p style="font-weight:700;color:#374151;margin-bottom:4px;">{{ __('No approved versions yet') }}</p>
            <p style="font-size:0.83rem;">{{ __('Versions will appear here once approved by the Design team.') }}</p>
        </div>
    </div>
    @endif

    {{-- Feedback Table — hidden from Developers --}}
    @if($isDesigner)
    <div class="hrm-section-card">
        <div class="hrm-section-header">
            <h5><i class="ti ti-message-circle"></i> {{ __('Design Reviews & Feedback') }}</h5>
            @php
                $totalPending = 0;
                foreach($design->versions as $v) {
                    $totalPending += $v->feedbacks->filter(function($fb) {
                        return $fb->status == 'Pending' && $fb->feedback_type != 'Approval';
                    })->count();
                }
            @endphp
            @if($totalPending > 0)
                <span class="hrm-badge hrm-badge-danger" style="animation: hrm-pulse 2s infinite;">
                    <i class="ti ti-bell"></i> {{ $totalPending }} {{ __('Pending') }}
                </span>
            @endif
        </div>
        <div class="hrm-section-body">
            <div style="overflow-x:auto;">
                <table class="hrm-table">
                    <thead>
                        <tr>
                            <th>{{ __('Version') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Comment') }}</th>
                            <th>{{ __('Attachments') }}</th>
                            <th>{{ __('Submitted By') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allFeedbacks = collect();
                            foreach($design->versions as $v) {
                                foreach($v->feedbacks as $fb) {
                                    $fb->version_name = $v->version;
                                    $fb->design_version_id = $v->id;
                                    $allFeedbacks->push($fb);
                                }
                            }
                            $allFeedbacks = $allFeedbacks->sortByDesc('created_at');
                        @endphp

                        @foreach($allFeedbacks as $fb)
                        <tr>
                            <td><span class="hrm-version-badge">{{ $fb->version_name }}</span></td>
                            <td style="font-weight:600; color:#1e293b; max-width:150px;">{{ $fb->title }}</td>
                            <td>
                                <span class="hrm-badge hrm-badge-info">{{ $fb->feedback_type }}</span>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width:220px; font-size:0.83rem;" title="{{ $fb->comment }}">
                                    {{ $fb->comment }}
                                </span>
                            </td>
                            <td>
                                @if($fb->attachments && $fb->attachments->count() > 0)
                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                    @foreach($fb->attachments as $attachment)
                                        @php
                                            $filePath = $attachment->file_path;
                                            $fileUrl  = asset('storage/' . $filePath);
                                            $ext      = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                            $isImage  = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                            $isPdf    = $ext === 'pdf';
                                        @endphp
                                        @if($isImage)
                                            <a href="{{ $fileUrl }}" target="_blank" title="{{ $attachment->file_name }}"
                                               style="display:inline-block;">
                                                <img src="{{ $fileUrl }}"
                                                     alt="{{ $attachment->file_name }}"
                                                     style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.1);"
                                                     onerror="this.onerror=null;this.src='';this.parentElement.innerHTML='<span style=\'background:#fee2e2;color:#dc2626;border-radius:6px;padding:4px 8px;font-size:11px;\'><i class=\'ti ti-photo-off\'></i> Broken</span>';">
                                            </a>
                                        @elseif($isPdf)
                                            <a href="{{ $fileUrl }}" target="_blank" title="{{ $attachment->file_name }}"
                                               style="display:inline-flex;align-items:center;gap:4px;background:#fee2e2;color:#dc2626;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:600;text-decoration:none;">
                                                <i class="ti ti-file-type-pdf"></i> PDF
                                            </a>
                                        @else
                                            <a href="{{ $fileUrl }}" target="_blank" title="{{ $attachment->file_name }}"
                                               style="display:inline-flex;align-items:center;gap:4px;background:#e0f2fe;color:#0369a1;border-radius:6px;padding:4px 10px;font-size:11px;font-weight:600;text-decoration:none;">
                                                <i class="ti ti-paperclip"></i> {{ strtoupper($ext) }}
                                            </a>
                                        @endif
                                    @endforeach
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size:12px;">—</span>
                                @endif
                            </td>
                            <td style="font-size:0.83rem;">{{ $fb->submitter->name ?? __('Client') }}</td>
                            <td style="font-size:0.83rem; white-space:nowrap;">{{ $fb->created_at->format('M d, Y') }}</td>
                            <td>
                                @php
                                    if ($fb->feedback_type == 'Approval') {
                                        $statusClass = 'hrm-badge-approved';
                                        $statusIcon = 'ti-circle-check';
                                        $statusText = __('Client Approved');
                                    } else {
                                        $statusClass = $fb->status == 'Pending' ? 'hrm-badge-pending' : 'hrm-badge-resolved';
                                        $statusIcon  = $fb->status == 'Pending' ? 'ti-clock' : 'ti-check';
                                        $statusText  = $fb->status;
                                    }
                                @endphp
                                <span class="hrm-badge {{ $statusClass }}"><i class="ti {{ $statusIcon }}"></i> {{ $statusText }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button data-bs-toggle="modal" data-bs-target="#feedbackModal_{{ $fb->id }}" class="hrm-tbl-btn hrm-tbl-btn-info" title="{{ __('View Feedback') }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    @if($fb->status == 'Pending' && $fb->feedback_type != 'Approval')
                                    <form action="{{ route('design-feedbacks.update', $fb->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="Resolved">
                                        <button type="submit" class="hrm-tbl-btn hrm-tbl-btn-success" title="{{ __('Mark Done') }}">
                                            <i class="ti ti-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @if($allFeedbacks->count() == 0)
                        <tr class="hrm-empty-row">
                            <td colspan="9">
                                <span class="hrm-empty-icon">💬</span>
                                {{ __('No feedback submitted yet.') }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif {{-- end @if($isDesigner) for feedback table --}}

{{-- Version Detail Modals --}}
@foreach($design->versions as $version)
<div class="modal fade hrm-modal" id="versionModal_{{ $version->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-versions me-2"></i>{{ __('Version') }}: {{ $version->version }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <p class="hrm-modal-label"><i class="ti ti-link"></i> {{ __('Figma Links') }}</p>
                    @if($version->links->count() > 0)
                    <div class="list-group list-group-flush rounded-3" style="border: 1px solid #e8edf5;">
                        @foreach($version->links as $link)
                        <a href="{{ $link->url }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2" style="font-size:0.85rem; font-weight:600; color:#001a3b;">
                            <i class="ti ti-external-link"></i> {{ $link->title }}
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted" style="font-size:0.85rem;">{{ __('No links added.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Feedback Detail Modals --}}
@if(isset($allFeedbacks))
@foreach($allFeedbacks as $fb)
<div class="modal fade hrm-modal" id="feedbackModal_{{ $fb->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-message-circle me-2"></i>{{ __('Feedback Details') }} - {{ $fb->version_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="hrm-modal-label"><i class="ti ti-typography"></i> {{ __('Title') }}</p>
                        <p style="font-size:0.95rem; font-weight:600; color:#1e293b;">{{ $fb->title }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="hrm-modal-label"><i class="ti ti-tag"></i> {{ __('Type') }}</p>
                        <span class="hrm-badge hrm-badge-info">{{ $fb->feedback_type }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="hrm-modal-label"><i class="ti ti-align-left"></i> {{ __('Comment') }}</p>
                    <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0; font-size:0.9rem; color:#475569; white-space:pre-wrap;">{{ $fb->comment ?? __('No comment provided.') }}</div>
                </div>

                <div class="mb-4">
                    <p class="hrm-modal-label"><i class="ti ti-paperclip"></i> {{ __('Attachments') }}</p>
                    @if($fb->attachments && $fb->attachments->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                        @foreach($fb->attachments as $attachment)
                            @php
                                $filePath = $attachment->file_path;
                                $fileUrl  = asset('storage/' . $filePath);
                                $ext      = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isImage  = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                $isPdf    = $ext === 'pdf';
                            @endphp
                            @if($isImage)
                                <a href="{{ $fileUrl }}" target="_blank" title="{{ $attachment->file_name }}">
                                    <img src="{{ $fileUrl }}" alt="{{ $attachment->file_name }}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;box-shadow:0 2px 5px rgba(0,0,0,.05);">
                                </a>
                            @elseif($isPdf)
                                <a href="{{ $fileUrl }}" target="_blank" title="{{ $attachment->file_name }}" style="display:inline-flex;align-items:center;gap:6px;background:#fee2e2;color:#dc2626;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid #fca5a5;">
                                    <i class="ti ti-file-type-pdf" style="font-size:1.2rem;"></i> PDF
                                </a>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank" title="{{ $attachment->file_name }}" style="display:inline-flex;align-items:center;gap:6px;background:#e0f2fe;color:#0369a1;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid #bae6fd;">
                                    <i class="ti ti-paperclip" style="font-size:1.2rem;"></i> {{ strtoupper($ext) }}
                                </a>
                            @endif
                        @endforeach
                        </div>
                    @else
                        <p class="text-muted" style="font-size:0.85rem;">{{ __('No attachments.') }}</p>
                    @endif
                </div>

                <div class="mb-4">
                    @php
                        // Find the version object for this feedback to get links
                        $currentVersion = $design->versions->where('id', $fb->design_version_id)->first();
                    @endphp
                    <p class="hrm-modal-label"><i class="ti ti-link"></i> {{ __('Figma Links') }}</p>
                    @if($currentVersion && $currentVersion->links->count() > 0)
                    <div class="list-group list-group-flush rounded-3" style="border: 1px solid #e8edf5;">
                        @foreach($currentVersion->links as $link)
                        <a href="{{ $link->url }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2" style="font-size:0.85rem; font-weight:600; color:#001a3b;">
                            <i class="ti ti-external-link"></i> {{ $link->title }}
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted" style="font-size:0.85rem;">{{ __('No links added to this version.') }}</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endforeach
@endif

{{-- Add Version Modal --}}
<div class="modal fade hrm-modal" id="addVersionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-plus me-2"></i>{{ __('Add New Version') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('design-versions.store', $design->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-hash"></i> {{ __('Version Number/Name') }} *</label>
                        <input type="text" name="version" class="hrm-modal-input" required placeholder="{{ __('e.g. V1, V1.1, V2') }}">
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-typography"></i> {{ __('Figma Link Title') }}</label>
                        <input type="text" name="links[0][title]" class="hrm-modal-input" placeholder="{{ __('e.g. Desktop Design') }}">
                    </div>
                    <div class="mb-3">
                        <label class="hrm-modal-label"><i class="ti ti-link"></i> {{ __('Figma Link URL') }}</label>
                        <input type="url" name="links[0][url]" class="hrm-modal-input" placeholder="https://figma.com/...">
                    </div>
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fafc; border:1px solid #e8edf5;">
                        <input type="checkbox" class="form-check-input" id="client_visible" name="client_visible" value="1" style="width:18px;height:18px;">
                        <label for="client_visible" class="hrm-switch-label">
                            <div>
                                <span style="font-weight:600; font-size:0.85rem;">{{ __('Visible to Client') }}</span>
                                <p style="margin:0; font-size:0.75rem; color:#6b7280;">{{ __('Client can see this version in the shared report') }}</p>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hrm-btn-cancel" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="hrm-btn-submit"><i class="ti ti-plus"></i> {{ __('Add Version') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes hrm-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
    50% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
}
</style>
@endsection
