@extends('layouts.admin')

@section('page-title')
    {{ __('Project Reports') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Project Reports') }}</li>
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-reports-wrap * { font-family: 'Inter', sans-serif; }

    /* Stat Row */
    .hrm-stat-row { display: flex; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
    .hrm-stat-card {
        flex: 1; min-width: 160px;
        background: #fff; border-radius: 16px; border: 1px solid #e8edf5;
        padding: 22px; box-shadow: 0 2px 10px rgba(0,26,59,0.04);
        position: relative; overflow: hidden;
        transition: all 0.3s ease;
    }
    .hrm-stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,26,59,0.1); }
    .hrm-stat-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
    .hrm-stat-blue::after   { background: linear-gradient(90deg, #001a3b, #3B82F6); }
    .hrm-stat-green::after  { background: linear-gradient(90deg, #059669, #34d399); }
    .hrm-stat-yellow::after { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .hrm-stat-red::after    { background: linear-gradient(90deg, #dc2626, #f87171); }
    .hrm-stat-card-inner { display: flex; align-items: center; gap: 16px; }
    .hrm-stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .hrm-stat-icon i { font-size: 1.3rem; }
    .hrm-stat-icon-blue   { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
    .hrm-stat-icon-green  { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .hrm-stat-icon-yellow { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .hrm-stat-icon-red    { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .hrm-stat-info h3 { font-size: 1.7rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1; }
    .hrm-stat-info p  { font-size: 0.75rem; color: #6b7280; font-weight: 600; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 0.04em; }

    /* Section card */
    .hrm-section-card { background: #fff; border-radius: 16px; border: 1px solid #e8edf5; box-shadow: 0 2px 12px rgba(0,26,59,0.05); margin-bottom: 24px; overflow: hidden; }
    .hrm-section-hdr { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #fafbfd; }
    .hrm-section-hdr h5 { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px; }
    .hrm-section-hdr h5 i { color: #001a3b; }

    /* Table */
    .hrm-table { width: 100%; border-collapse: collapse; }
    .hrm-table th { background: #f8fafc; color: #6b7280; font-size: 0.71rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 12px 20px; text-align: left; border-bottom: 1px solid #f1f5f9; white-space: nowrap; }
    .hrm-table td { padding: 15px 20px; border-bottom: 1px solid #f8fafc; font-size: 0.84rem; color: #374151; vertical-align: middle; }
    .hrm-table tr:last-child td { border-bottom: none; }
    .hrm-table tbody tr:hover td { background: #fafbfd; }
    .hrm-table tbody tr { transition: background 0.15s; }

    /* Badges */
    .hrm-badge { display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; padding: 4px 11px; font-size: 0.72rem; font-weight: 700; }
    .hrm-badge-success  { background: #d1fae5; color: #065f46; }
    .hrm-badge-warning  { background: #fef3c7; color: #92400e; }
    .hrm-badge-danger   { background: #fee2e2; color: #991b1b; }
    .hrm-badge-info     { background: #cffafe; color: #0e7490; }
    .hrm-badge-primary  { background: #dbeafe; color: #1d4ed8; }



    .hrm-project-link { font-weight: 700; color: #001a3b; text-decoration: none; font-size: 0.88rem; }
    .hrm-project-link:hover { text-decoration: underline; }

    /* Employee row */
    .hrm-emp-row { display: flex; align-items: center; gap: 10px; }
    .hrm-emp-avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #e8edf5; }
    .hrm-emp-name { font-weight: 700; color: #1e293b; font-size: 0.85rem; }

    .hrm-hours-chip { background: #fef3c7; color: #92400e; border-radius: 8px; padding: 3px 10px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
    .hrm-updates-chip { background: #e0e7ff; color: #3730a3; border-radius: 8px; padding: 3px 10px; font-size: 0.78rem; font-weight: 700; }
    .hrm-date-chip { background: #f1f5f9; color: #475569; border-radius: 8px; padding: 3px 10px; font-size: 0.78rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }

    /* Empty */
    .hrm-empty-td { text-align: center; padding: 48px 20px; color: #94a3b8; font-size: 0.85rem; }

    .fade-in { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
</style>

@php
    $totalProjects = $projects->count();
    $onTrack = $projects->filter(fn($p) => $p->project_health == 'On Track')->count();
    $totalHours = $projects->sum('actual_hours');
@endphp

<div class="hrm-reports-wrap fade-in mt-5">
    {{-- Stat Cards --}}
    <div class="hrm-stat-row">
        <div class="hrm-stat-card hrm-stat-blue">
            <div class="hrm-stat-card-inner">
                <div class="hrm-stat-icon hrm-stat-icon-blue"><i class="ti ti-clipboard-list"></i></div>
                <div class="hrm-stat-info">
                    <h3>{{ $totalProjects }}</h3>
                    <p>{{ __('Total Projects') }}</p>
                </div>
            </div>
        </div>

        <div class="hrm-stat-card hrm-stat-yellow">
            <div class="hrm-stat-card-inner">
                <div class="hrm-stat-icon hrm-stat-icon-yellow"><i class="ti ti-circle-check"></i></div>
                <div class="hrm-stat-info">
                    <h3>{{ $onTrack }}</h3>
                    <p>{{ __('On Track') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects Overview Table --}}
    <div class="hrm-section-card">
        <div class="hrm-section-hdr">
            <h5><i class="ti ti-clipboard-list"></i> {{ __('All Projects Overview') }}</h5>
            <span style="background:#f1f5f9; color:#475569; border-radius:8px; padding:4px 12px; font-size:0.75rem; font-weight:700;">
                {{ $totalProjects }} {{ __('Projects') }}
            </span>
        </div>
        <div style="overflow-x:auto;">
            <table class="hrm-table" id="pc-dt-simple">
                <thead>
                    <tr>
                        <th>{{ __('Project') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Health') }}</th>

                        <th>{{ __('Actual Hours') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" class="hrm-project-link">
                                {{ $project->project_name }}
                            </a>
                        </td>
                        <td style="color:#475569;">{{ $project->client_name }}</td>
                        <td>
                            <span class="hrm-badge {{ $project->project_health == 'Critical' ? 'hrm-badge-danger' : ($project->project_health == 'Delayed' ? 'hrm-badge-warning' : 'hrm-badge-success') }}">
                                <i class="ti ti-{{ $project->project_health == 'On Track' ? 'circle-check' : ($project->project_health == 'Critical' ? 'alert-triangle' : 'clock') }}"></i>
                                {{ $project->project_health }}
                            </span>
                        </td>

                        <td><span class="hrm-hours-chip" style="background:#d1fae5; color:#065f46;"><i class="ti ti-clock-play"></i>{{ $project->actual_hours ?: '—' }}</span></td>
                    </tr>
                    @endforeach
                    @if($projects->count() == 0)
                    <tr><td colspan="6" class="hrm-empty-td">{{ __('No project data available.') }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Employee Productivity --}}
    <div class="hrm-section-card">
        <div class="hrm-section-hdr">
            <h5><i class="ti ti-users"></i> {{ __('Employee Productivity & Workload') }}</h5>
        </div>
        <div style="overflow-x:auto;">
            <table class="hrm-table" id="pc-dt-simple-2">
                <thead>
                    <tr>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Total Updates') }}</th>
                        <th>{{ __('Total Hours Logged') }}</th>
                        <th>{{ __('Latest Update') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>
                            <div class="hrm-emp-row">
                                <span class="hrm-emp-name">{{ $employee->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="hrm-updates-chip">{{ $employee->dailyUpdates->count() }} {{ __('updates') }}</span>
                        </td>
                        <td>
                            <span class="hrm-hours-chip">
                                <i class="ti ti-clock"></i>
                                {{ $employee->dailyUpdates->sum('hours_worked') }}h
                            </span>
                        </td>
                        <td>
                            @if($employee->dailyUpdates->count() > 0)
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($employee->dailyUpdates->sortByDesc('work_date')->first()->work_date)->format('M d, Y') }}
                                </span>
                            @else
                                <span style="color:#94a3b8; font-size:0.82rem;">{{ __('No updates') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($employees->count() == 0)
                    <tr><td colspan="4" class="hrm-empty-td">{{ __('No employee productivity data available.') }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
