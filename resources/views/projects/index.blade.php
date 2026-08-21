@extends('layouts.admin')

@section('page-title')
    {{ __('Project List') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Project List') }}</li>
@endsection

@section('action-button')
    @if(Auth::user()->type != 'hr')
        @if(Auth::user()->can('Create Employee') || Auth::user()->isTester() || Auth::user()->type == 'company')
            <a href="#" data-url="{{ route('projects.create') }}" data-ajax-popup="true"
                data-title="{{ __('Add New Project') }}" data-size="lg"
                class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('New Project') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    @endif
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-projects-wrap * { font-family: 'Inter', sans-serif; }

    .hrm-btn-primary {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        color: #fff; border: none; border-radius: 10px;
        padding: 8px 20px; font-size: 0.85rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
        transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(0,26,59,0.25);
    }
    .hrm-btn-primary:hover { color: #fff; transform: translateY(-1px); }

    /* Stat Cards */
    .hrm-stat-row { display: flex; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
    .hrm-stat-card {
        flex: 1; min-width: 160px;
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        padding: 20px 24px;
        box-shadow: 0 2px 10px rgba(0,26,59,0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }
    .hrm-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,26,59,0.1); }
    .hrm-stat-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .hrm-stat-icon i { font-size: 1.3rem; }
    .hrm-stat-icon-blue  { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
    .hrm-stat-icon-green { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .hrm-stat-icon-yellow{ background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .hrm-stat-icon-red   { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .hrm-stat-info h3 { font-size: 1.6rem; font-weight: 800; color: #1e293b; margin: 0; line-height: 1; }
    .hrm-stat-info p  { font-size: 0.78rem; color: #6b7280; margin: 4px 0 0; font-weight: 500; }

    /* Main card */
    .hrm-main-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 12px rgba(0,26,59,0.05);
        overflow: hidden;
    }
    .hrm-card-header-bar {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        background: #fafbfd;
    }
    .hrm-card-header-bar h5 {
        font-size: 0.95rem; font-weight: 700; color: #1e293b;
        margin: 0; display: flex; align-items: center; gap: 8px;
    }
    .hrm-search-input {
        border: 2px solid #e8edf5; border-radius: 10px;
        padding: 7px 13px 7px 34px; font-size: 0.82rem; outline: none;
        transition: border-color 0.2s; background: #fff;
        font-family: 'Inter', sans-serif;
        width: 200px;
    }
    .hrm-search-input:focus { border-color: #001a3b; }
    .hrm-search-wrap { position: relative; }
    .hrm-search-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; }

    /* Table */
    .hrm-table { width: 100%; border-collapse: collapse; }
    .hrm-table th {
        background: #f8fafc; color: #6b7280; font-size: 0.72rem;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        padding: 13px 20px; text-align: left; border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .hrm-table td {
        padding: 16px 20px; border-bottom: 1px solid #f8fafc;
        font-size: 0.84rem; color: #374151; vertical-align: middle;
    }
    .hrm-table tr:last-child td { border-bottom: none; }
    .hrm-table tr:hover td { background: #fafbfd; }
    .hrm-table tbody tr { transition: background 0.15s; }

    /* Badges */
    .hrm-tech-badge {
        display: inline-flex; align-items: center;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1d4ed8; border-radius: 6px;
        padding: 2px 8px; font-size: 0.72rem; font-weight: 700;
        margin: 2px 2px 2px 0;
    }
    .hrm-emp-badge {
        display: inline-flex; align-items: center;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46; border-radius: 6px;
        padding: 2px 8px; font-size: 0.72rem; font-weight: 700;
        margin: 2px 2px 2px 0;
    }
    .hrm-project-name { font-weight: 700; color: #1e293b; font-size: 0.88rem; }
    .hrm-client-name { font-size: 0.82rem; color: #475569; }

    /* Action buttons */
    .hrm-icon-btn {
        width: 32px; height: 32px;
        border-radius: 8px; border: none; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        text-decoration: none; transition: all 0.2s ease;
        font-size: 0.85rem;
    }
    .hrm-icon-btn-green  { background: #d1fae5; color: #065f46; }
    .hrm-icon-btn-green:hover  { background: #a7f3d0; color: #065f46; }
    .hrm-icon-btn-blue   { background: #dbeafe; color: #1d4ed8; }
    .hrm-icon-btn-blue:hover   { background: #bfdbfe; color: #1d4ed8; }
    .hrm-icon-btn-red    { background: #fee2e2; color: #b91c1c; }
    .hrm-icon-btn-red:hover    { background: #fecaca; color: #b91c1c; }

    /* Empty */
    .hrm-empty-row td { text-align: center; padding: 60px 20px; }
    .hrm-empty-icon-wrap { width: 80px; height: 80px; background: linear-gradient(135deg, #e8f0fe, #c7d8f8); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .hrm-empty-icon-wrap i { font-size: 2rem; color: #001a3b; }

    .fade-in { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
</style>

@php
    $totalProjects = $projects->count();
    $inProgress = $projects->filter(fn($p) => in_array($p->status, ['assigned', 'in_progress']))->count();
    $completed = $projects->filter(fn($p) => $p->status == 'completed')->count();
    $delayed = $projects->filter(function($p) {
        if ($p->status == 'completed') return false;
        if (empty($p->project_enddate) || str_starts_with($p->project_enddate, '0000') || str_starts_with($p->project_enddate, '-0001')) return false;
        
        $endDate = \Carbon\Carbon::parse($p->project_enddate)->endOfDay();
        $now = \Carbon\Carbon::now();
        $daysRemaining = $now->startOfDay()->diffInDays($endDate->startOfDay(), false);
        
        return $daysRemaining <= 5;
    })->count();
@endphp

<div class="hrm-projects-wrap fade-in">

    {{-- Stat Cards --}}
    <div class="hrm-stat-row">
        <div class="hrm-stat-card">
            <div class="hrm-stat-icon hrm-stat-icon-blue"><i class="ti ti-clipboard-list"></i></div>
            <div class="hrm-stat-info">
                <h3>{{ $totalProjects }}</h3>
                <p>{{ __('Total Projects') }}</p>
            </div>
        </div>
        <div class="hrm-stat-card">
            <div class="hrm-stat-icon hrm-stat-icon-yellow"><i class="ti ti-loader"></i></div>
            <div class="hrm-stat-info">
                <h3>{{ $inProgress }}</h3>
                <p>{{ __('In Progress') }}</p>
            </div>
        </div>
        <div class="hrm-stat-card">
            <div class="hrm-stat-icon hrm-stat-icon-red"><i class="ti ti-alert-triangle"></i></div>
            <div class="hrm-stat-info">
                <h3>{{ $delayed }}</h3>
                <p>{{ __('Delayed Projects') }}</p>
            </div>
        </div>
        <div class="hrm-stat-card">
            <div class="hrm-stat-icon hrm-stat-icon-green"><i class="ti ti-circle-check"></i></div>
            <div class="hrm-stat-info">
                <h3>{{ $completed }}</h3>
                <p>{{ __('Completed Projects') }}</p>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="hrm-main-card">
        <div class="hrm-card-header-bar">
            <h5><i class="ti ti-clipboard-list" style="color:#001a3b;"></i> {{ __('All Projects') }}</h5>
            <div class="hrm-search-wrap ms-auto">
                <i class="ti ti-search"></i>
                <input type="text" class="hrm-search-input" id="projectSearch" placeholder="{{ __('Search projects...') }}">
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="hrm-table" id="pc-dt-simple">
                <thead>
                    <tr>
                        <th>{{ __('Project') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Start') }}</th>
                        <th>{{ __('Deadline') }}</th>
                        <th>{{ __('Technology') }}</th>
                        @if(Auth::user()->type != 'employee' || Auth::user()->isTester())
                        <th>{{ __('Team') }}</th>
                        @endif
                        @if(Auth::user()->type != 'hr' && (Gate::check('Edit Meeting') || Gate::check('Delete Meeting') || Auth::user()->isTester()))
                        <th style="text-align:center;">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                    <tr class="project-row">
                        <td>
                            <div class="hrm-project-name">{{ $project->project_name }}</div>
                        </td>
                        <td><div class="hrm-client-name">{{ $project->client_name }}</div></td>
                        <td>
                            <span style="background:#f1f5f9; color:#475569; border-radius:6px; padding:3px 10px; font-size:0.75rem; font-weight:600;">
                                {{ $project->project_type }}
                            </span>
                        </td>
                        <td style="white-space:nowrap; font-size:0.82rem;">
                            @if($project->project_startdate)
                                {{ \Carbon\Carbon::parse($project->project_startdate)->format('d M Y') }}
                            @else
                                <span class="text-muted">{{ __('Not set') }}</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap; font-size:0.82rem;">
                            @php
                                $isValidEndDate = !empty($project->project_enddate) && !str_starts_with($project->project_enddate, '0000') && !str_starts_with($project->project_enddate, '-0001');
                            @endphp
                            @if($isValidEndDate)
                                {{ \Carbon\Carbon::parse($project->project_enddate)->format('d M Y') }}
                            @else
                                <span class="text-muted">{{ __('No Deadline') }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $techs = $project->technology;
                                if (is_string($techs)) { $techs = json_decode($techs, true); }
                            @endphp
                            @if(is_array($techs))
                                @foreach($techs as $tech)
                                    <span class="hrm-tech-badge">{{ $tech }}</span>
                                @endforeach
                            @else
                                {{ $techs }}
                            @endif
                        </td>
                        @if(Auth::user()->type != 'employee' || Auth::user()->isTester())
                        <td>
                            @if(is_array($project->assigned_data))
                                @foreach($project->assigned_data as $assignment)
                                    @foreach($assignment['employee_ids'] ?? [] as $employeeId)
                                        @if(isset($employees[$employeeId]))
                                            <span class="hrm-emp-badge">{{ $employees[$employeeId]->user->name ?? __('Unknown') }}</span>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </td>
                        @endif
                        @if(Auth::user()->type != 'hr' && (Gate::check('Edit Meeting') || Gate::check('Delete Meeting') || Auth::user()->isTester()))
                        <td>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                <a href="{{ route('projects.show', $project->id) }}"
                                   class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="{{ __('View Dashboard') }}">
                                    <i class="ti ti-eye"></i>
                                </a>
                                @if(Gate::check('Edit Meeting') || Auth::user()->isTester() || Auth::user()->type == 'company')
                                <a href="#"
                                   class="btn btn-sm btn-primary"
                                   data-url="{{ route('projects.edit', $project->id) }}"
                                   data-ajax-popup="true" data-size="lg"
                                   data-title="{{ __('Edit Project') }}"
                                   data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                @endif
                                @if(Gate::check('Delete Meeting') || Auth::user()->isTester() || Auth::user()->type == 'company')
                                {!! Form::open(['method' => 'DELETE', 'route' => ['projects.destroy', $project->id], 'style' => 'display:inline']) !!}
                                <a href="#"
                                   class="btn btn-sm btn-danger bs-pass-para"
                                   data-bs-toggle="tooltip" title="{{ __('Delete Project') }}"
                                   onclick="event.preventDefault(); document.getElementById('delete-form-{{ $project->id }}').submit();">
                                    <i class="ti ti-trash"></i>
                                </a>
                                {!! Form::close() !!}
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr class="hrm-empty-row">
                        <td colspan="10">
                            <div class="hrm-empty-icon-wrap"><i class="ti ti-clipboard-list"></i></div>
                            <h6 style="color:#374151; font-weight:700; margin-bottom:6px;">{{ __('No projects found') }}</h6>
                            <p style="color:#6b7280; font-size:0.85rem; margin:0;">{{ __('Add your first project to get started.') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Search filter
        $('#projectSearch').on('input', function() {
            var q = $(this).val().toLowerCase();
            $('.project-row').each(function() {
                $(this).toggle($(this).text().toLowerCase().includes(q));
            });
        });

        // Delete functionality
        $(document).on('click', '.bs-pass-para', function(e) {
            e.preventDefault();
            const button = $(this);
            const form = button.closest('form');
            const row = form.closest('tr');

            if (!confirm('{{ __("Are you sure you want to delete this project?") }}')) return;

            button.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(400, function() { $(this).remove(); });
                    } else {
                        alert(response.message);
                        button.prop('disabled', false);
                    }
                },
                error: function() {
                    alert('{{ __("Server error occurred") }}');
                    button.prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
@endsection