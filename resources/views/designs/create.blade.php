@extends('layouts.admin')

@section('page-title')
    {{ __('Create Design') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('designs.index') }}">{{ __('Designs') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-form-wrap * { font-family: 'Inter', sans-serif; }

    .hrm-form-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 24px rgba(0,26,59,0.08);
        overflow: hidden;
    }
    .hrm-form-header {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        padding: 28px 32px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .hrm-form-header-icon {
        width: 52px; height: 52px;
        background: rgba(255,255,255,0.15);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        backdrop-filter: blur(10px);
    }
    .hrm-form-header-icon i { font-size: 1.5rem; color: #fff; }
    .hrm-form-header h4 { color: #fff; margin: 0; font-weight: 700; font-size: 1.15rem; }
    .hrm-form-header p  { color: rgba(255,255,255,0.7); margin: 0; font-size: 0.82rem; }

    .hrm-form-body { padding: 32px; }

    .hrm-field-group { margin-bottom: 24px; }
    .hrm-field-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .hrm-field-label i { color: #001a3b; font-size: 0.9rem; }
    .hrm-field-label .required { color: #ef4444; }

    .hrm-input, .hrm-select, .hrm-textarea {
        width: 100%;
        border: 2px solid #e8edf5;
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 0.88rem;
        color: #1e293b;
        background: #fafbfd;
        transition: all 0.2s ease;
        outline: none;
        font-family: 'Inter', sans-serif;
    }
    .hrm-input:focus, .hrm-select:focus, .hrm-textarea:focus {
        border-color: #001a3b;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(0,26,59,0.08);
    }
    .hrm-textarea { resize: vertical; min-height: 110px; }

    .hrm-form-footer {
        padding: 20px 32px;
        border-top: 1px solid #f1f5f9;
        background: #fafbfd;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }
    .hrm-btn-cancel {
        background: #f1f5f9;
        color: #374151;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s;
    }
    .hrm-btn-cancel:hover { background: #e2e8f0; color: #374151; }
    .hrm-btn-submit {
        background: linear-gradient(135deg, #001a3b 0%, #1e3a8a 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 26px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(0,26,59,0.25);
    }
    .hrm-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,26,59,0.35); }

    .fade-in { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="row justify-content-center hrm-form-wrap fade-in">
    <div class="col-xl-7 col-lg-9">
        <div class="hrm-form-card">
            <div class="hrm-form-header">
                <div class="hrm-form-header-icon">
                    <i class="ti ti-layout-grid"></i>
                </div>
                <div>
                    <h4>{{ __('Create New Design') }}</h4>
                    <p>{{ __('Add a design to track versions and client feedback') }}</p>
                </div>
            </div>
            <form action="{{ route('designs.store') }}" method="POST">
                @csrf
                <div class="hrm-form-body">
                    <div class="hrm-field-group">
                        <label class="hrm-field-label">
                            <i class="ti ti-folder"></i> {{ __('Project') }} <span class="required">*</span>
                        </label>
                        <select name="project_id" class="hrm-select" required>
                            <option value="">{{ __('Select Project') }}</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ (isset($project_id) && $project_id == $proj->id) ? 'selected' : '' }}>
                                    {{ $proj->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hrm-field-group">
                        <label class="hrm-field-label">
                            <i class="ti ti-typography"></i> {{ __('Design Title') }} <span class="required">*</span>
                        </label>
                        <input type="text" name="title" class="hrm-input" required placeholder="{{ __('e.g. Homepage Redesign') }}">
                    </div>
                    <div class="hrm-field-group">
                        <label class="hrm-field-label">
                            <i class="ti ti-file-text"></i> {{ __('Description') }}
                        </label>
                        <textarea name="description" class="hrm-textarea" placeholder="{{ __('Brief description of this design...') }}"></textarea>
                    </div>
                </div>
                <div class="hrm-form-footer">
                    <a href="{{ url()->previous() }}" class="hrm-btn-cancel">
                        <i class="ti ti-arrow-left"></i> {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="hrm-btn-submit">
                        <i class="ti ti-check"></i> {{ __('Create Design') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
