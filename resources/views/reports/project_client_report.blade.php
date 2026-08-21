<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report - {{ $project->project_name }}</title>
    <meta name="description" content="Shareable project progress report for {{ $project->project_name }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #f0f4f8;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ===== Header ===== */
        .cr-header {
            background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 50%, #2563eb 100%);
            padding: 0;
            position: relative;
            overflow: hidden;
        }
        .cr-header::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 320px; height: 320px; border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .cr-header::after {
            content: ''; position: absolute; bottom: -100px; left: 100px;
            width: 400px; height: 400px; border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .cr-header-inner {
            width: 100%;
            padding: 40px 48px 36px;
            position: relative; z-index: 1;
        }
        .cr-header-label {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(255,255,255,0.12); border-radius: 8px;
            padding: 5px 14px; font-size: 0.75rem; font-weight: 700;
            color: rgba(255,255,255,0.85); text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 16px; backdrop-filter: blur(10px);
        }
        .cr-header h1 { font-size: 2rem; font-weight: 900; color: #fff; margin-bottom: 8px; line-height: 1.2; }
        .cr-header-client { font-size: 0.9rem; color: rgba(255,255,255,0.72); display: flex; align-items: center; gap: 8px; }
        .cr-header-chips { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
        .cr-header-chip {
            background: rgba(255,255,255,0.12); border-radius: 10px; padding: 8px 16px;
            font-size: 0.78rem; font-weight: 600; color: #fff;
            display: flex; align-items: center; gap: 6px; backdrop-filter: blur(4px);
        }

        /* ===== Main Container ===== */
        .cr-container { width: 100%; padding: 32px 40px 60px; }

        /* ===== Metric Cards ===== */
        .cr-metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 32px; }
        .cr-metric-card {
            background: #fff; border-radius: 18px; border: 1px solid #e8edf5;
            padding: 24px; box-shadow: 0 4px 20px rgba(0,26,59,0.06);
            display: flex; align-items: center; gap: 18px;
            transition: all 0.3s ease; position: relative; overflow: hidden;
        }
        .cr-metric-card:hover { transform: translateY(-3px); box-shadow: 0 12px 36px rgba(0,26,59,0.12); }
        .cr-metric-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; border-radius: 18px 18px 0 0; }
        .cr-metric-blue::after   { background: linear-gradient(90deg, #001a3b, #3B82F6); }
        .cr-metric-green::after  { background: linear-gradient(90deg, #059669, #34d399); }
        .cr-metric-yellow::after { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .cr-metric-purple::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
        .cr-metric-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cr-metric-icon i { font-size: 1.4rem; }
        .cr-metric-icon-blue   { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
        .cr-metric-icon-green  { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
        .cr-metric-icon-yellow { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
        .cr-metric-icon-purple { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #6d28d9; }
        .cr-metric-info h3 { font-size: 1.7rem; font-weight: 900; color: #1e293b; line-height: 1; margin-bottom: 4px; }
        .cr-metric-info p  { font-size: 0.73rem; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .cr-health-ontrack { color: #059669 !important; }
        .cr-health-warn    { color: #d97706 !important; }
        .cr-health-danger  { color: #dc2626 !important; }

        /* ===== Content Grid ===== */
        .cr-grid { display: grid; grid-template-columns: 1fr 380px; gap: 28px; }
        @media (max-width: 1100px) { .cr-grid { grid-template-columns: 1fr; } }

        /* ===== Cards ===== */
        .cr-card { background: #fff; border-radius: 18px; border: 1px solid #e8edf5; box-shadow: 0 4px 20px rgba(0,26,59,0.06); overflow: hidden; margin-bottom: 24px; }
        .cr-card-hdr { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #fafbfd; }
        .cr-card-hdr h5 { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px; }
        .cr-card-hdr h5 i { color: #001a3b; }
        .cr-card-body { padding: 0; }

        /* ===== Modules Table ===== */
        .cr-table { width: 100%; border-collapse: collapse; }
        .cr-table th { background: #f8fafc; color: #6b7280; font-size: 0.71rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 12px 20px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        .cr-table td { padding: 15px 20px; border-bottom: 1px solid #f8fafc; font-size: 0.84rem; color: #374151; vertical-align: middle; }
        .cr-table tr:last-child td { border-bottom: none; }
        .cr-table tbody tr:hover td { background: #fafbfd; }

        .cr-badge { display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; padding: 4px 11px; font-size: 0.72rem; font-weight: 700; }
        .cr-badge-success { background: #d1fae5; color: #065f46; }
        .cr-badge-warning { background: #fef3c7; color: #92400e; }
        .cr-badge-primary { background: #dbeafe; color: #1d4ed8; }
        .cr-badge-secondary { background: #e2e8f0; color: #475569; }
        .cr-badge-approved { background: #dcfce7; color: #166534; box-shadow: inset 0 0 0 1px #bbf7d0; }

        /* ===== Recent Work Feed ===== */
        .cr-feed { padding: 8px 0; }
        .cr-feed-item { display: flex; gap: 16px; padding: 16px 24px; border-bottom: 1px solid #f8fafc; transition: background 0.15s; }
        .cr-feed-item:last-child { border-bottom: none; }
        .cr-feed-item:hover { background: #fafbfd; }
        .cr-feed-dot { width: 10px; height: 10px; border-radius: 50%; background: linear-gradient(135deg, #001a3b, #3B82F6); flex-shrink: 0; margin-top: 5px; box-shadow: 0 0 0 3px #e0eaff; }
        .cr-feed-content { flex: 1; }
        .cr-feed-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 4px; }
        .cr-feed-header strong { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
        .cr-feed-header small { font-size: 0.75rem; color: #94a3b8; white-space: nowrap; }
        .cr-feed-desc { font-size: 0.82rem; color: #475569; line-height: 1.5; margin-bottom: 4px; }
        .cr-feed-meta { font-size: 0.75rem; color: #94a3b8; display: flex; align-items: center; gap: 5px; }

        /* ===== Design Reviews ===== */
        .cr-design-group { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; }
        .cr-design-group:last-child { border-bottom: none; }
        .cr-design-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .cr-design-desc { font-size: 0.82rem; color: #6b7280; margin-bottom: 14px; }
        .cr-version-block { background: #fafbfd; border: 1px solid #e8edf5; border-radius: 12px; padding: 16px; margin-bottom: 12px; }
        .cr-version-hdr { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .cr-version-code { background: #f1f5f9; color: #475569; border-radius: 6px; padding: 3px 10px; font-size: 0.78rem; font-weight: 800; font-family: monospace; }
        .cr-figma-links { margin-bottom: 14px; }
        .cr-figma-link { display: inline-flex; align-items: center; gap: 6px; color: #001a3b; font-weight: 600; font-size: 0.82rem; text-decoration: none; background: #e0eaff; border-radius: 8px; padding: 6px 12px; margin: 3px 4px 3px 0; transition: all 0.2s; }
        .cr-figma-link:hover { background: #c7d8f8; color: #001a3b; }

        /* Feedback */
        .cr-feedback-section { margin-top: 14px; }
        .cr-feedback-section h6 { font-size: 0.78rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .cr-feedback-item { background: #fff; border: 1px solid #e8edf5; border-radius: 10px; padding: 12px 14px; margin-bottom: 8px; }
        .cr-feedback-item-hdr { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .cr-feedback-title { font-size: 0.83rem; font-weight: 700; color: #1e293b; }
        .cr-feedback-date  { font-size: 0.73rem; color: #94a3b8; }
        .cr-feedback-comment { font-size: 0.8rem; color: #475569; line-height: 1.5; }
        .cr-feedback-status { font-size: 0.7rem; color: #6b7280; margin-top: 6px; }

        /* Feedback Form */
        .cr-feedback-form { background: linear-gradient(135deg, #fafbfd, #f0f4f8); border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-top: 14px; }
        .cr-feedback-form h6 { font-size: 0.82rem; font-weight: 800; color: #001a3b; margin-bottom: 14px; display: flex; align-items: center; gap: 7px; }
        .cr-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; }
        @media (max-width: 600px) { .cr-form-row { grid-template-columns: 1fr; } }
        .cr-form-input, .cr-form-select, .cr-form-textarea {
            width: 100%; border: 2px solid #e8edf5; border-radius: 9px; padding: 9px 12px;
            font-size: 0.83rem; color: #1e293b; background: #fff; outline: none;
            transition: all 0.2s; font-family: 'Inter', sans-serif;
        }
        .cr-form-input:focus, .cr-form-select:focus, .cr-form-textarea:focus { border-color: #001a3b; box-shadow: 0 0 0 4px rgba(0,26,59,0.08); }
        .cr-form-textarea { resize: vertical; min-height: 80px; }
        .cr-form-submit { background: linear-gradient(135deg, #001a3b, #1e3a8a); color: #fff; border: none; border-radius: 9px; padding: 9px 22px; font-size: 0.83rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,26,59,0.25); }
        .cr-form-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,26,59,0.35); }
        .cr-form-file-label { font-size: 0.75rem; font-weight: 600; color: #475569; margin-bottom: 5px; display: block; }

        /* ===== Timeline (sidebar) ===== */
        .cr-timeline { padding: 8px 0; }
        .cr-timeline-item { display: flex; gap: 14px; padding: 14px 24px; border-bottom: 1px solid #f8fafc; }
        .cr-timeline-item:last-child { border-bottom: none; }
        .cr-timeline-dot { width: 10px; height: 10px; border-radius: 50%; background: linear-gradient(135deg, #001a3b, #3B82F6); flex-shrink: 0; margin-top: 4px; box-shadow: 0 0 0 3px #e0eaff; }
        .cr-timeline-body h6 { font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 3px; }
        .cr-timeline-body p  { font-size: 0.78rem; color: #6b7280; margin-bottom: 2px; }
        .cr-timeline-body small { font-size: 0.72rem; color: #94a3b8; }

        /* Empty */
        .cr-empty { text-align: center; padding: 40px 24px; color: #94a3b8; font-size: 0.84rem; }

        /* Footer */
        .cr-footer { text-align: center; padding: 24px; color: #94a3b8; font-size: 0.78rem; }
        .cr-footer strong { color: #001a3b; }

        /* Animations */
        .fade-in { animation: fadeIn 0.6s ease both; }
        .fade-in-2 { animation: fadeIn 0.6s 0.1s ease both; }
        .fade-in-3 { animation: fadeIn 0.6s 0.2s ease both; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

{{-- Header --}}
<div class="cr-header">
    <div class="cr-header-inner">
        <div class="cr-header-label"><i class="fas fa-clipboard-list"></i> Client Project Report</div>
        <h1>{{ $project->project_name }}</h1>
        <div class="cr-header-client"><i class="fas fa-building"></i> {{ $project->client_name }}</div>
        <div class="cr-header-chips">
            @if($project->project_startdate)
            <div class="cr-header-chip"><i class="fas fa-calendar"></i> Started: {{ \Carbon\Carbon::parse($project->project_startdate)->format('d M Y') }}</div>
            @endif
            @php
                $isValidEndDate = !empty($project->project_enddate) && !str_starts_with($project->project_enddate, '0000') && !str_starts_with($project->project_enddate, '-0001');
            @endphp
            @if($isValidEndDate)
            <div class="cr-header-chip"><i class="fas fa-flag-checkered"></i> Deadline: {{ \Carbon\Carbon::parse($project->project_enddate)->format('d M Y') }}</div>
            @else
            <div class="cr-header-chip" style="opacity: 0.8;"><i class="fas fa-flag-checkered"></i> Deadline: {{ __('No Deadline') }}</div>
            @endif
            <div class="cr-header-chip"><i class="fas fa-layer-group"></i> {{ $project->modules->count() }} Modules</div>
            <div class="cr-header-chip"><i class="fas fa-clock"></i> Updated: {{ now()->format('d M Y') }}</div>
        </div>
    </div>
</div>

<div class="cr-container">

    {{-- Metric Cards --}}
    <div class="cr-metrics fade-in">
        <div class="cr-metric-card cr-metric-blue">
            <div class="cr-metric-icon cr-metric-icon-blue"><i class="fas fa-heartbeat"></i></div>
            <div class="cr-metric-info">
                @php
                    $healthColor = $project->project_health == 'Critical' ? 'cr-health-danger' : ($project->project_health == 'Delayed' ? 'cr-health-warn' : 'cr-health-ontrack');
                @endphp
                <h3 class="{{ $healthColor }}">{{ $project->project_health }}</h3>
                <p>Project Health</p>
            </div>
        </div>
        <div class="cr-metric-card cr-metric-green">
            <div class="cr-metric-icon cr-metric-icon-green"><i class="fas fa-chart-pie"></i></div>
            <div class="cr-metric-info">
                <h3>{{ $project->actual_hours ?: '—' }}</h3>
                <p>Hours Delivered</p>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="cr-grid fade-in-2">
        {{-- Left Column --}}
        <div>

            {{-- Modules --}}
            <div class="cr-card mb-4">
                <div class="cr-card-hdr">
                    <h5><i class="fas fa-layer-group"></i> Project Modules</h5>
                    <span class="cr-badge cr-badge-primary">{{ $project->modules->count() }} total</span>
                </div>
                <div class="cr-card-body">
                    <div style="overflow-x:auto;">
                        <table class="cr-table">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->modules as $module)
                                <tr>
                                    <td style="font-weight:700; color:#1e293b;">{{ $module->module_name }}</td>
                                    <td>
                                        <span class="cr-badge {{ $module->status == 'completed' ? 'cr-badge-success' : 'cr-badge-warning' }}">
                                            {{ ucfirst($module->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                @if($project->modules->count() == 0)
                                <tr><td colspan="2" class="cr-empty">No modules created yet.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Recent Approved Updates --}}
            <div class="cr-card mb-4">
                <div class="cr-card-hdr">
                    <h5><i class="fas fa-check-circle"></i> Recent Approved Updates</h5>
                </div>
                <div class="cr-card-body">
                    @if($project->dailyUpdates->count() > 0)
                    <div class="cr-feed">
                        @foreach($project->dailyUpdates as $update)
                        <div class="cr-feed-item">
                            <div class="cr-feed-dot"></div>
                            <div class="cr-feed-content">
                                <div class="cr-feed-header">
                                    <strong>{{ $update->employee->name ?? 'Team Member' }}</strong>
                                    <small>{{ \Carbon\Carbon::parse($update->work_date)->format('M d, Y') }}</small>
                                </div>
                                <div class="cr-feed-desc">{{ $update->work_done }}</div>
                                <div class="cr-feed-meta">
                                    <i class="fas fa-clock"></i>
                                    {{ $update->hours_worked }} hours · {{ $update->module->module_name ?? 'General' }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="cr-empty">No approved updates yet.</div>
                    @endif
                </div>
            </div>

            {{-- Project Documents Section --}}
            <div class="cr-card mb-4">
                <div class="cr-card-hdr">
                    <h5><i class="fas fa-folder-open"></i> Project Documents</h5>
                </div>
                <div class="cr-card-body">
                    <form action="{{ route('project-documents.store', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 24px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <h6 style="margin-top:0; margin-bottom: 14px; font-size: 0.9rem; color: #1e293b;"><i class="fas fa-cloud-upload-alt" style="color: #64748b; margin-right: 6px;"></i> Upload Document</h6>
                        @csrf
                        <div style="display:flex; gap: 12px; align-items:flex-end; flex-wrap:wrap;">
                            <div style="flex:1; min-width:200px;">
                                <input type="text" name="file_name" class="cr-form-input" placeholder="Document Name (Optional)" style="background: #fff; padding: 8px 12px;">
                            </div>
                            <div style="flex:1; min-width:200px;">
                                <input type="file" name="document" class="cr-form-input" required style="background: #fff; padding: 5px 12px;">
                            </div>
                            <div>
                                <button type="submit" class="cr-form-submit" style="padding: 9px 18px; border-radius: 8px;"><i class="fas fa-upload"></i> Upload</button>
                            </div>
                        </div>
                    </form>

                    @if($project->documents && $project->documents->count() > 0)
                        <div class="cr-feed">
                            @foreach($project->documents as $doc)
                            <div class="cr-feed-item">
                                <div class="cr-feed-dot" style="background-color: #3b82f6; border-color: #dbeafe;"></div>
                                <div class="cr-feed-content">
                                    <div class="cr-feed-header">
                                        <strong>{{ $doc->file_name }}</strong>
                                    </div>
                                    <div class="cr-feed-meta" style="margin-top: 4px; margin-bottom: 10px;">
                                        <i class="fas fa-user"></i> By {{ $doc->uploader->name ?? 'Client' }} &nbsp;•&nbsp; <i class="fas fa-clock"></i> {{ $doc->created_at->format('M d, Y') }}
                                    </div>
                                    <button onclick="openDocModal({{ $doc->id }})" style="font-size:0.75rem; color:#0369a1; background:#e0f2fe; padding:4px 10px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; font-weight:600; border:none; cursor:pointer;"><i class="fas fa-eye"></i> View</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="cr-empty" style="border: 1px dashed #e2e8f0; background: #fff;">
                            No documents uploaded yet.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Design Approval History --}}
            @php
                $approvedVersions = collect();
                $allProjectDesigns = \App\Models\Design::where('project_id', $project->id)->with('versions')->get();
                foreach($allProjectDesigns as $d) {
                    foreach($d->versions as $v) {
                        // Include versions that are approved, regardless of whether they are client visible yet
                        if($v->status == 'Approved') {
                            $v->design_title = $d->title;
                            $approvedVersions->push($v);
                        }
                    }
                }
                $approvedVersions = $approvedVersions->sortByDesc('updated_at');
            @endphp
            <div class="cr-card mb-4">
                <div class="cr-card-hdr">
                    <h5><i class="fas fa-check-double"></i> Design Approval History</h5>
                </div>
                <div class="cr-card-body">
                    @if($approvedVersions->count() > 0)
                        <div class="cr-feed">
                            @foreach($approvedVersions as $av)
                            <div class="cr-feed-item">
                                <div class="cr-feed-dot" style="background-color: #10b981; border-color: #d1fae5;"></div>
                                <div class="cr-feed-content">
                                    <div class="cr-feed-header">
                                        <strong>{{ $av->design_title }} - <span style="color:#001a3b;">{{ $av->version }}</span></strong>
                                        <span class="cr-badge cr-badge-success" style="font-size:0.65rem; padding: 2px 6px;">Approved</span>
                                    </div>
                                    <div class="cr-feed-meta" style="margin-top: 4px;">
                                        <i class="fas fa-clock"></i> Internally approved on {{ $av->updated_at->format('M d, Y h:i A') }}
                                    </div>
                                    @if($av->links->count() > 0)
                                    <div style="margin-top: 8px; display:flex; gap: 8px; flex-wrap:wrap;">
                                        @foreach($av->links as $link)
                                        <a href="{{ $link->url }}" target="_blank" style="font-size:0.75rem; color:#0369a1; background:#e0f2fe; padding:2px 8px; border-radius:4px; text-decoration:none;"><i class="fas fa-external-link-alt"></i> {{ $link->title }}</a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="cr-empty">No approved design versions yet.</div>
                    @endif
                </div>
            </div>

            {{-- Design Reviews --}}
            @php
                $designs = \App\Models\Design::where('project_id', $project->id)->with(['versions' => function($query) {
                    $query->where('client_visible', true)
                          ->where('status', '!=', 'Approved')
                          ->orderBy('created_at', 'desc');
                }])->get();
                $hasVisibleDesigns = $designs->filter(fn($d) => $d->versions->count() > 0)->count() > 0;
            @endphp
            <div class="cr-card">
                <div class="cr-card-hdr">
                    <h5><i class="fas fa-palette"></i> Design Reviews</h5>
                </div>
                <div class="cr-card-body">
                    @if($hasVisibleDesigns)
                        @foreach($designs as $design)
                            @if($design->versions->count() > 0)
                            <div class="cr-design-group">
                                <div class="cr-design-title">{{ $design->title }}</div>
                                @if($design->description)
                                <div class="cr-design-desc">{{ $design->description }}</div>
                                @endif

                                @foreach($design->versions as $version)
                                <div class="cr-version-block" style="margin-bottom:8px; padding:12px;">
                                    <div class="cr-version-hdr" style="margin-bottom:0;">
                                        <span class="cr-version-code">{{ $version->version }}</span>
                                        <span class="cr-badge {{ $version->status == 'Approved' ? 'cr-badge-success' : 'cr-badge-warning' }}">{{ $version->status }}</span>
                                    </div>

                                    @if($version->links->count() > 0)
                                    <div class="cr-figma-links" style="margin-top:10px; margin-bottom:0;">
                                        @foreach($version->links as $link)
                                        <a href="{{ $link->url }}" target="_blank" class="cr-figma-link">
                                            <i class="fas fa-external-link-alt"></i> {{ $link->title }}
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endforeach

                                {{-- Combined Existing Feedback --}}
                                @php
                                    $allFeedbacks = collect();
                                    foreach($design->versions as $v) {
                                        foreach($v->feedbacks as $fb) {
                                            $fb->version_name = $v->version;
                                            $allFeedbacks->push($fb);
                                        }
                                    }
                                    $allFeedbacks = $allFeedbacks->sortByDesc('created_at');
                                @endphp

                                @if($allFeedbacks->count() > 0)
                                <div class="cr-feedback-section" style="margin-top:20px;">
                                    <h6><i class="fas fa-comments"></i> Submitted Feedback</h6>
                                    @foreach($allFeedbacks as $fb)
                                    <div class="cr-feedback-item">
                                        <div class="cr-feedback-item-hdr">
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <span class="cr-version-code" style="font-size:0.7rem; padding:2px 6px;">{{ $fb->version_name }}</span>
                                                <span class="cr-feedback-title">{{ $fb->title }}</span>
                                            </div>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                @php
                                                    if ($fb->feedback_type == 'Approval') {
                                                        $statusClass = 'cr-badge-approved';
                                                        $statusText = __('Client Approved');
                                                        $statusIcon = 'fa-check-circle';
                                                    } else {
                                                        $statusClass = $fb->status == 'Resolved' ? 'cr-badge-success' : 'cr-badge-warning';
                                                        $statusText = $fb->status;
                                                        $statusIcon = $fb->status == 'Resolved' ? 'fa-check' : 'fa-clock';
                                                    }
                                                @endphp
                                                <span class="cr-badge {{ $statusClass }}" style="font-size:0.68rem;"><i class="fas {{ $statusIcon }}"></i> {{ $statusText }}</span>
                                                @if($fb->status == 'Pending' && $fb->feedback_type != 'Approval')
                                                <form action="{{ route('design-feedbacks.destroy', $fb->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this feedback?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:0.78rem; padding:0;" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="cr-feedback-comment">{{ $fb->comment }}</div>
                                        <div class="cr-feedback-date">{{ $fb->created_at->format('M d, Y') }} - <span style="color:#001a3b; font-weight:600;">{{ $fb->feedback_type }}</span></div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @php
                                    $feedbackableVersions = $design->versions->filter(function($v) {
                                        return $v->status !== 'Approved';
                                    });
                                @endphp

                                @if($feedbackableVersions->count() > 0)
                                {{-- Single Feedback Form --}}
                                <div class="cr-feedback-form">
                                    <h6><i class="fas fa-plus-circle"></i> Submit Feedback</h6>
                                    <form action="#" method="POST" enctype="multipart/form-data" onsubmit="
                                        const vId = document.getElementById('version_select_{{ $design->id }}').value;
                                        if(!vId) { alert('Please select a version first'); return false; }
                                        this.action = '{{ route('design-feedbacks.store', 'VERSION_ID') }}'.replace('VERSION_ID', vId);
                                    ">
                                        @csrf
                                        <div class="cr-form-row">
                                            <select id="version_select_{{ $design->id }}" class="cr-form-select" required>
                                                <option value="">-- Select Version --</option>
                                                @foreach($feedbackableVersions as $v)
                                                <option value="{{ $v->id }}">{{ $v->version }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="title" class="cr-form-input" placeholder="Feedback Title" required>
                                        </div>
                                        <div class="cr-form-row">
                                            <select name="feedback_type" class="cr-form-select" required>
                                                <option value="Change Request">Change Request</option>
                                                <option value="Bug/Issue">Bug / Issue</option>
                                                <option value="General Comment">General Comment</option>
                                                <option value="Approval">Approval</option>
                                            </select>
                                            <input type="file" name="attachments[]" class="cr-form-input" style="padding:6px;" multiple accept=".png,.jpg,.jpeg,.webp,.pdf,.docx,.zip">
                                        </div>
                                        <textarea name="comment" class="cr-form-textarea" style="width:100%; margin-bottom:10px;" placeholder="Your detailed feedback..." required></textarea>
                                        <div style="text-align:right;">
                                            <button type="submit" class="cr-form-submit"><i class="fas fa-paper-plane"></i> Submit Feedback</button>
                                        </div>
                                    </form>
                                </div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    @else
                    <div class="cr-empty">No designs available for review yet.</div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right Column: Timeline --}}
        <div>
            <div class="cr-card" style="position:sticky; top:20px;">
                <div class="cr-card-hdr">
                    <h5><i class="fas fa-history"></i> Project Timeline</h5>
                </div>
                <div class="cr-card-body">
                    @if($project->activities->count() > 0)
                    <div class="cr-timeline">
                        @foreach($project->activities as $activity)
                        <div class="cr-timeline-item">
                            <div class="cr-timeline-dot"></div>
                            <div class="cr-timeline-body">
                                <h6>{{ $activity->activity_type }}</h6>
                                <p>{{ $activity->activity }}</p>
                                <small>{{ $activity->created_at->format('M d, Y g:i A') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="cr-empty">No timeline activities yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="cr-footer fade-in-3">
        <strong>{{ $project->project_name }}</strong> · Confidential Project Report · Generated {{ now()->format('d M Y') }}
    </div>
</div>

{{-- Document Preview Modals --}}
@foreach($project->documents as $doc)
<div id="docModal{{ $doc->id }}" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.85); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
    <div style="background:#fff; width:90%; max-width:900px; max-height:90vh; border-radius:12px; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
            <h5 style="margin:0; font-size:1rem; color:#0f172a;"><i class="fas fa-file-alt" style="color:#64748b; margin-right:8px;"></i> {{ $doc->file_name }}</h5>
            <button onclick="document.getElementById('docModal{{ $doc->id }}').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#64748b; cursor:pointer;">&times;</button>
        </div>
        <div style="flex:1; padding:20px; overflow-y:auto; background:#f1f5f9; display:flex; justify-content:center; align-items:center; min-height:400px;">
            @php 
                $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
            @endphp
            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                <img src="{{ Storage::url($doc->file_path) }}" style="max-width:100%; max-height:65vh; border-radius:8px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);" alt="{{ $doc->file_name }}">
            @elseif(in_array($ext, ['pdf']))
                <iframe src="{{ Storage::url($doc->file_path) }}" width="100%" height="550px" style="border:none; border-radius:8px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);"></iframe>
            @else
                <div style="text-align:center; color:#64748b;">
                    <i class="fas fa-file" style="font-size:4rem; margin-bottom:16px; color:#cbd5e1;"></i>
                    <p style="margin:0;">Preview not available for this file type.</p>
                    <small>Please download the file to view it.</small>
                </div>
            @endif
        </div>
        <div style="padding:16px 20px; border-top:1px solid #e2e8f0; text-align:right; background:#fff;">
            <a href="{{ route('project-documents.download', $doc->id) }}" class="cr-form-submit" style="padding: 8px 16px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:#0ea5e9; border-radius:6px; color:#fff; font-weight:600; font-size:0.85rem;"><i class="fas fa-download"></i> Download File</a>
        </div>
    </div>
</div>
@endforeach

<script>
    function openDocModal(id) {
        document.getElementById('docModal' + id).style.display = 'flex';
    }
</script>

</body>
</html>
