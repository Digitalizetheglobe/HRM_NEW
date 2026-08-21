@extends('layouts.admin')

@section('page-title')
    {{ __('My Projects') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('My Projects') }}</li>
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-my-projects * { font-family: 'Inter', sans-serif; }

    /* Project cards */
    .hrm-project-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        padding: 24px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 2px 10px rgba(0,26,59,0.04);
        cursor: default;
    }
    .hrm-project-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0,26,59,0.12);
        border-color: #bfdbfe;
    }

    /* Top accent bar */
    .hrm-card-accent {
        position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #001a3b, #3B82F6);
        border-radius: 18px 18px 0 0;
    }
    .hrm-card-accent-critical   { background: linear-gradient(90deg, #dc2626, #ef4444); }
    .hrm-card-accent-high       { background: linear-gradient(90deg, #d97706, #f59e0b); }
    .hrm-card-accent-normal     { background: linear-gradient(90deg, #001a3b, #3B82F6); }

    .hrm-project-card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; }
    .hrm-project-name { font-size: 1.0rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; line-height: 1.3; }
    .hrm-project-client { font-size: 0.78rem; color: #6b7280; display: flex; align-items: center; gap: 5px; }

    /* Priority badge */
    .hrm-priority-badge {
        display: inline-flex; align-items: center; gap: 4px;
        border-radius: 8px; padding: 4px 10px;
        font-size: 0.72rem; font-weight: 700;
        flex-shrink: 0;
    }
    .hrm-priority-critical { background: #fee2e2; color: #991b1b; }
    .hrm-priority-high     { background: #fef3c7; color: #92400e; }
    .hrm-priority-medium   { background: #e0e7ff; color: #3730a3; }
    .hrm-priority-low      { background: #d1fae5; color: #065f46; }

    /* Health badge */
    .hrm-health-badge {
        display: inline-flex; align-items: center; gap: 4px;
        border-radius: 8px; padding: 4px 10px;
        font-size: 0.72rem; font-weight: 700;
    }
    .hrm-health-critical  { background: #fee2e2; color: #991b1b; }
    .hrm-health-delayed   { background: #fef3c7; color: #92400e; }
    .hrm-health-ontrack   { background: #d1fae5; color: #065f46; }
    .hrm-health-default   { background: #e0e7ff; color: #3730a3; }

    /* Dates */
    .hrm-date-row { display: flex; gap: 16px; margin-bottom: 16px; }
    .hrm-date-item { display: flex; flex-direction: column; }
    .hrm-date-label { font-size: 0.7rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
    .hrm-date-value { font-size: 0.82rem; color: #374151; font-weight: 600; }



    /* Card footer */
    .hrm-card-footer {
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .hrm-view-btn {
        background: linear-gradient(135deg, #001a3b, #1e3a8a);
        color: #fff; border: none; border-radius: 10px;
        padding: 8px 18px; font-size: 0.8rem; font-weight: 700;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s; box-shadow: 0 4px 12px rgba(0,26,59,0.2);
    }
    .hrm-view-btn:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,26,59,0.3); }

    /* Empty */
    .hrm-empty-state {
        text-align: center; padding: 80px 20px;
        background: #fff; border-radius: 16px; border: 1px solid #e8edf5;
    }
    .hrm-empty-state .ico { width: 90px; height: 90px; background: linear-gradient(135deg, #e8f0fe, #c7d8f8); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .hrm-empty-state .ico i { font-size: 2.2rem; color: #001a3b; }

    /* Page header */
    .hrm-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .hrm-page-header h4 { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0; }
    .hrm-count-pill { background: linear-gradient(135deg, #001a3b, #1e3a8a); color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 0.78rem; font-weight: 700; }

    .fade-in-up { animation: fiu 0.5s ease both; }
    @keyframes fiu { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .d1 { animation-delay: 0.05s; } .d2 { animation-delay: 0.1s; } .d3 { animation-delay: 0.15s; }
    .d4 { animation-delay: 0.2s; } .d5 { animation-delay: 0.25s; } .d6 { animation-delay: 0.3s; }
</style>

<div class="hrm-my-projects">
    <div class="hrm-page-header">
        <h4>{{ __('My Projects') }}</h4>
        <span class="hrm-count-pill">{{ $projects->count() }} {{ __('Projects') }}</span>
    </div>

    @if($projects->count() > 0)
    <div class="row g-4">
        @foreach ($projects as $i => $project)
        @php
            $accentClass = $project->project_priority == 'Critical' ? 'hrm-card-accent-critical' : ($project->project_priority == 'High' ? 'hrm-card-accent-high' : 'hrm-card-accent-normal');
            $priorityClass = match($project->project_priority) {
                'Critical' => 'hrm-priority-critical',
                'High'     => 'hrm-priority-high',
                'Medium'   => 'hrm-priority-medium',
                default    => 'hrm-priority-low',
            };
            $healthClass = match($project->project_health) {
                'Critical', 'Delayed' => ($project->project_health == 'Critical' ? 'hrm-health-critical' : 'hrm-health-delayed'),
                'On Track'  => 'hrm-health-ontrack',
                default     => 'hrm-health-default',
            };

        @endphp
        <div class="col-xl-4 col-lg-6 col-md-6 fade-in-up d{{ min($i+1,6) }}">
            <div class="hrm-project-card">
                <div class="hrm-card-accent {{ $accentClass }}"></div>

                <div class="hrm-project-card-top">
                    <div>
                        <div class="hrm-project-name">{{ $project->project_name }}</div>
                        <div class="hrm-project-client"><i class="ti ti-building"></i> {{ $project->client_name }}</div>
                    </div>
                    <span class="hrm-priority-badge {{ $priorityClass }}">
                        {{ $project->project_priority }}
                    </span>
                </div>

                <div class="hrm-date-row">
                    <div class="hrm-date-item">
                        <span class="hrm-date-label">{{ __('Start') }}</span>
                        <span class="hrm-date-value">{{ \Auth::user()->dateFormat($project->project_startdate) }}</span>
                    </div>
                    <div class="hrm-date-item">
                        <span class="hrm-date-label">{{ __('Deadline') }}</span>
                        <span class="hrm-date-value">{{ \Auth::user()->dateFormat($project->project_enddate) }}</span>
                    </div>
                </div>



                <div class="hrm-card-footer">
                    <span class="hrm-health-badge {{ $healthClass }}">
                        <i class="ti ti-{{ $project->project_health == 'On Track' ? 'circle-check' : ($project->project_health == 'Critical' ? 'alert-triangle' : 'clock') }}"></i>
                        {{ $project->project_health }}
                    </span>
                    <a href="{{ route('projects.show', $project->id) }}" class="hrm-view-btn">
                        <i class="ti ti-eye"></i> {{ __('View') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="hrm-empty-state">
        <div class="ico"><i class="ti ti-clipboard-list"></i></div>
        <h5 style="color:#1e293b; font-weight:700;">{{ __('No Projects Assigned') }}</h5>
        <p style="color:#6b7280; font-size:0.88rem;">{{ __('You have no projects assigned to you yet.') }}</p>
    </div>
    @endif
</div>
@endsection
