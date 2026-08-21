@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Designs') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    @if(isset($project))
    <li class="breadcrumb-item"><a href="{{ route('projects.show', $project->id) }}">{{ $project->project_name }}</a></li>
    @else
    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">{{ __('Projects') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Designs') }}</li>
@endsection

@section('action-button')
    <a href="{{ route('designs.create', isset($project) ? ['project_id' => $project->id] : []) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Add Design') }}">
        <i class="ti ti-plus"></i>
    </a>
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .hrm-designs-wrap * { font-family: 'Inter', sans-serif; }

    .hrm-btn-primary {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(0, 26, 59, 0.25);
    }
    .hrm-btn-primary:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,26,59,0.35); }

    .hrm-designs-wrap {
        padding: 8px 0;
    }

    /* Empty state */
    .hrm-empty {
        text-align: center;
        padding: 80px 20px;
    }
    .hrm-empty .hrm-empty-icon {
        width: 90px; height: 90px;
        background: linear-gradient(135deg, #e8f0fe, #c7d8f8);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
    }
    .hrm-empty .hrm-empty-icon i { font-size: 2.2rem; color: #001a3b; }
    .hrm-empty h5 { color: #1e293b; font-weight: 700; }
    .hrm-empty p { color: #6b7280; font-size: 0.9rem; }

    /* Design cards */
    .hrm-design-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        padding: 24px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 2px 10px rgba(0,26,59,0.04);
    }
    .hrm-design-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #001a3b, #3B82F6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .hrm-design-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,26,59,0.12); border-color: #c0cfe8; }
    .hrm-design-card:hover::before { opacity: 1; }

    .hrm-card-icon {
        width: 48px; height: 48px;
        background: linear-gradient(135deg, #e8f0fe, #c7d8f8);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px;
    }
    .hrm-card-icon i { font-size: 1.3rem; color: #001a3b; }

    .hrm-design-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .hrm-design-project {
        font-size: 0.8rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 14px;
    }

    .hrm-design-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .hrm-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f1f5f9;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 0.78rem;
        color: #475569;
        font-weight: 500;
    }
    .hrm-meta-chip i { font-size: 0.75rem; }

    .hrm-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .hrm-status-approved { background: #d1fae5; color: #065f46; }
    .hrm-status-draft    { background: #e0e7ff; color: #3730a3; }
    .hrm-status-review   { background: #fef3c7; color: #92400e; }
    .hrm-status-rejected { background: #fee2e2; color: #991b1b; }
    .hrm-status-other    { background: #f3f4f6; color: #374151; }

    .hrm-feedback-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        animation: hrm-pulse 2s infinite;
    }
    @keyframes hrm-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
        50% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
    }

    .hrm-card-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .hrm-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .hrm-action-view   { background: #e0f2fe; color: #0369a1; }
    .hrm-action-view:hover   { background: #bae6fd; color: #0369a1; }
    .hrm-action-edit   { background: #e0e7ff; color: #4338ca; }
    .hrm-action-edit:hover   { background: #c7d2fe; color: #4338ca; }
    .hrm-action-delete { background: #fee2e2; color: #b91c1c; }
    .hrm-action-delete:hover { background: #fecaca; color: #b91c1c; }

    .hrm-search-bar {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0,26,59,0.04);
    }
    .hrm-search-bar input {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 14px 8px 36px;
        font-size: 0.85rem;
        outline: none;
        transition: border-color 0.2s;
        width: 260px;
    }
    .hrm-search-bar input:focus { border-color: #3B82F6; }
    .hrm-search-wrap { position: relative; }
    .hrm-search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; }
    .hrm-count-chip { background: linear-gradient(135deg, #001a3b, #1e3a8a); color: #fff; border-radius: 20px; padding: 4px 14px; font-size: 0.78rem; font-weight: 700; }

    .fade-in-up { animation: fadeInUp 0.5s ease both; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .delay-1 { animation-delay: 0.05s; } .delay-2 { animation-delay: 0.1s; } .delay-3 { animation-delay: 0.15s; }
    .delay-4 { animation-delay: 0.2s; } .delay-5 { animation-delay: 0.25s; } .delay-6 { animation-delay: 0.3s; }
</style>

<div class="hrm-designs-wrap">
    {{-- Search / Count bar --}}
    <div class="hrm-search-bar">
        <div class="hrm-search-wrap">
            <i class="ti ti-search"></i>
            <input type="text" id="designSearch" placeholder="{{ __('Search designs...') }}">
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size:0.82rem;">{{ $designs->count() }} {{ __('total') }}</span>
            <span class="hrm-count-chip">{{ $designs->where('latestVersion.status', 'Approved')->count() }} {{ __('Approved') }}</span>
        </div>
    </div>

    {{-- Cards grid --}}
    @if($designs->count() > 0)
    <div class="row g-3" id="designsGrid">
        @foreach($designs as $i => $design)
        @php
            $pendingCount = 0;
            if($design->latestVersion) {
                $pendingCount = \App\Models\DesignFeedback::where('design_version_id', $design->latestVersion->id)
                                    ->where('status', 'Pending')->count();
            }
            $status = $design->latestVersion ? $design->latestVersion->status : null;
            $statusClass = match($status) {
                'Approved' => 'hrm-status-approved',
                'Rejected' => 'hrm-status-rejected',
                'Draft'    => 'hrm-status-draft',
                'Client Review', 'Internal Review', 'Feedback Received', 'Ready For Approval', 'Changes In Progress' => 'hrm-status-review',
                default    => 'hrm-status-other',
            };
        @endphp
        <div class="col-xl-4 col-lg-6 col-md-6 fade-in-up delay-{{ min($i+1,6) }} design-card-item" data-title="{{ strtolower($design->title) }}">
            <div class="hrm-design-card">
                <div class="hrm-card-icon">
                    <i class="ti ti-layout-grid"></i>
                </div>

                <div class="hrm-design-title">
                    {{ $design->title }}
                    @if($pendingCount > 0)
                        <span class="hrm-feedback-badge"><i class="ti ti-bell"></i> {{ $pendingCount }} New</span>
                    @endif
                </div>

                @if(!isset($project))
                <div class="hrm-design-project">
                    <i class="ti ti-folder"></i>
                    {{ $design->project ? $design->project->project_name : '-' }}
                </div>
                @endif

                <div class="hrm-design-meta">
                    <span class="hrm-meta-chip">
                        <i class="ti ti-versions"></i>
                        {{ __('Version') }}: {{ $design->latestVersion ? $design->latestVersion->version : '-' }}
                    </span>
                    @if($status)
                    <span class="hrm-status-badge {{ $statusClass }}">
                        <i class="ti ti-{{ $status == 'Approved' ? 'circle-check' : ($status == 'Rejected' ? 'circle-x' : 'clock') }}"></i>
                        {{ $status }}
                    </span>
                    @else
                    <span class="hrm-status-badge hrm-status-other">{{ __('No Version') }}</span>
                    @endif
                </div>

                <div class="hrm-card-actions">
                    <a href="{{ route('designs.show', $design->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{ __('View') }}">
                        <i class="ti ti-eye"></i>
                    </a>
                    <a href="{{ route('designs.edit', $design->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                        <i class="ti ti-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('designs.destroy', $design->id) }}" style="display:inline-block" class="ms-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure?') }}')">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="hrm-empty">
        <div class="hrm-empty-icon"><i class="ti ti-layout-grid"></i></div>
        <h5>{{ __('No Designs Found') }}</h5>
        <p>{{ __('Start by adding a design to track versions and feedback.') }}</p>
        <a href="{{ route('designs.create', isset($project) ? ['project_id' => $project->id] : []) }}" class="btn btn-sm btn-primary mt-2" data-bs-toggle="tooltip" title="{{ __('Add First Design') }}">
            <i class="ti ti-plus"></i>
        </a>
    </div>
    @endif
</div>

<script>
document.getElementById('designSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.design-card-item').forEach(card => {
        card.style.display = card.dataset.title.includes(q) ? '' : 'none';
    });
});
</script>
@endsection
