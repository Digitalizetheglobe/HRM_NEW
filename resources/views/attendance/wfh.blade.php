@extends('layouts.admin')

@section('page-title')
    {{ __('Work From Home') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Work From Home') }}</li>
@endsection

@section('action-button')
    @if ($isEmployee || $canApprove)
        <button type="button"
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#wfhModal"
                title="{{ $isEmployee ? __('Request WFH') : __('Assign WFH') }}"
                aria-label="{{ $isEmployee ? __('Request WFH') : __('Assign WFH') }}">
            <i class="ti ti-plus"></i>
        </button>
    @endif
@endsection

@push('css-page')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-wfh-wrap * { font-family: 'Inter', sans-serif; }

    .hrm-stat-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(0, 26, 59, 0.04);
    }
    .hrm-stat-label { font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; }
    .hrm-stat-value { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-top: 2px; }
    .hrm-stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }

    .hrm-tabs-nav { display: flex; gap: 8px; border-bottom: 2px solid #f1f5f9; margin-bottom: 20px; padding-bottom: 8px; flex-wrap: nowrap; overflow-x: auto; }
    .hrm-tab-btn { background: none; border: none; padding: 7px 14px; font-size: 0.82rem; font-weight: 700; color: #64748b; border-radius: 8px; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; white-space: nowrap; flex-shrink: 0; }
    .hrm-tab-btn.active { background: #eff6ff; color: #1e3a8a; }
    .hrm-tab-btn:hover:not(.active) { background: #f8fafc; color: #1e293b; }

    .hrm-main-card { background: #fff; border-radius: 16px; border: 1px solid #e8edf5; box-shadow: 0 2px 12px rgba(0, 26, 59, 0.05); overflow: hidden; }
    .hrm-card-hdr { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; background: #fafbfd; }
    .hrm-card-hdr h5 { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px; }

    .hrm-table { width: 100%; border-collapse: collapse; }
    .hrm-table th { background: #f8fafc; color: #6b7280; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 12px 18px; text-align: left; border-bottom: 1px solid #f1f5f9; white-space: nowrap; }
    .hrm-table td { padding: 14px 18px; border-bottom: 1px solid #f8fafc; font-size: 0.84rem; color: #374151; vertical-align: middle; }
    .hrm-table tr:last-child td { border-bottom: none; }
    .hrm-table tbody tr:hover td { background: #fafbfd; }

    .hrm-date-chip { display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; color: #1e293b; border-radius: 8px; padding: 4px 10px; font-size: 0.78rem; font-weight: 600; white-space: nowrap; }
    .hrm-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 20px; padding: 4px 11px; font-size: 0.72rem; font-weight: 700; white-space: nowrap; }
    .hrm-badge-approved { background: #d1fae5; color: #065f46; }
    .hrm-badge-pending  { background: #fef3c7; color: #92400e; }
    .hrm-badge-rejected { background: #fee2e2; color: #991b1b; }

    .hrm-icon-btn { width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: all 0.2s; padding: 0; color: #fff; }
    .hrm-icon-btn-success { background: #198754; color: #fff; }
    .hrm-icon-btn-success:hover { background: #157347; color: #fff; }
    .hrm-icon-btn-danger  { background: #dc3545; color: #fff; }
    .hrm-icon-btn-danger:hover { background: #bb2d3b; color: #fff; }

    .wfh-mobile-card { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; }
    .wfh-mobile-card:last-child { border-bottom: none; }

    .hrm-modal .modal-content { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: visible; }
    .hrm-modal .modal-header { background: linear-gradient(135deg, #001a3b, #1e3a8a); padding: 18px 22px; border: none; border-top-left-radius: 16px; border-top-right-radius: 16px; }
    .hrm-modal .modal-title { color: #fff; font-weight: 700; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
    .hrm-modal .btn-close { filter: brightness(0) invert(1); }
    .hrm-modal .modal-body { padding: 22px; overflow: visible; }
    .hrm-modal .modal-footer { padding: 14px 22px; background: #fafbfd; border-top: 1px solid #f1f5f9; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; }
    .hrm-modal-label { font-size: 0.78rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .hrm-modal-label i { color: #001a3b; }
    .hrm-modal-input, .hrm-modal-select, .hrm-modal-textarea { width: 100%; border: 2px solid #e8edf5; border-radius: 10px; padding: 9px 13px; font-size: 0.85rem; color: #1e293b; background: #fafbfd; outline: none; transition: all 0.2s; font-family: 'Inter', sans-serif; }
    .hrm-modal-input:focus, .hrm-modal-select:focus, .hrm-modal-textarea:focus { border-color: #001a3b; background: #fff; box-shadow: 0 0 0 4px rgba(0,26,59,0.08); }
    .hrm-modal-textarea { resize: vertical; min-height: 90px; }
    .hrm-modal-btn-cancel { background: #f1f5f9; color: #374151; border: none; border-radius: 10px; padding: 9px 20px; font-size: 0.84rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.2s; }
    .hrm-modal-btn-cancel:hover { background: #e2e8f0; }
    .hrm-modal-btn-submit { background: linear-gradient(135deg, #001a3b, #1e3a8a); color: #fff; border: none; border-radius: 10px; padding: 9px 22px; font-size: 0.84rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(0,26,59,0.25); }
    .hrm-modal-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,26,59,0.35); }
</style>
@endpush

@section('content')
<div class="hrm-wfh-wrap">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="hrm-stat-card">
                <div>
                    <div class="hrm-stat-label">{{ __('Total Requests') }}</div>
                    <div class="hrm-stat-value">{{ $counts['all'] }}</div>
                </div>
                <div class="hrm-stat-icon" style="background:#eef2ff;color:#1e3a8a;">
                    <i class="ti ti-home"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hrm-stat-card">
                <div>
                    <div class="hrm-stat-label">{{ __('Pending') }}</div>
                    <div class="hrm-stat-value" style="color:#d97706;">{{ $counts['pending'] }}</div>
                </div>
                <div class="hrm-stat-icon" style="background:#fef3c7;color:#d97706;">
                    <i class="ti ti-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hrm-stat-card">
                <div>
                    <div class="hrm-stat-label">{{ __('Approved') }}</div>
                    <div class="hrm-stat-value" style="color:#059669;">{{ $counts['approved'] }}</div>
                </div>
                <div class="hrm-stat-icon" style="background:#d1fae5;color:#059669;">
                    <i class="ti ti-check"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hrm-stat-card">
                <div>
                    <div class="hrm-stat-label">{{ __('Rejected') }}</div>
                    <div class="hrm-stat-value" style="color:#dc2626;">{{ $counts['rejected'] }}</div>
                </div>
                <div class="hrm-stat-icon" style="background:#fee2e2;color:#dc2626;">
                    <i class="ti ti-x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="hrm-tabs-nav">
        @foreach (['All', 'Pending', 'Approved', 'Rejected'] as $tab)
            <a href="{{ route('attendance.wfh.index', ['status' => $tab]) }}"
               class="hrm-tab-btn {{ $statusFilter === $tab ? 'active' : '' }}">
                @if ($tab === 'All')
                    <i class="ti ti-list"></i>
                @elseif ($tab === 'Pending')
                    <i class="ti ti-clock"></i>
                @elseif ($tab === 'Approved')
                    <i class="ti ti-circle-check"></i>
                @else
                    <i class="ti ti-circle-x"></i>
                @endif
                {{ __($tab) }}
            </a>
        @endforeach
    </div>

    <div class="hrm-main-card">
        <div class="hrm-card-hdr">
            <h5>
                <i class="ti ti-home-check" style="color:#001a3b;"></i>
                {{ __('Work From Home Requests') }}
            </h5>
            <span style="background:#f1f5f9; color:#475569; border-radius:8px; padding:4px 12px; font-size:0.78rem; font-weight:700; margin-left:auto;">
                {{ $wfhRequests->count() }} {{ __('records') }}
            </span>
        </div>

        {{-- Desktop Table --}}
        <div class="d-none d-md-block" style="overflow-x:auto;">
            <table class="hrm-table">
                <thead>
                    <tr>
                        @if (!$isEmployee)
                            <th>{{ __('Employee') }}</th>
                        @endif
                        <th>{{ __('Start Date') }}</th>
                        <th>{{ __('End Date') }}</th>
                        <th>{{ __('Reason') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actioned By') }}</th>
                        @if ($canApprove)
                            <th class="text-end">{{ __('Action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($wfhRequests as $wfh)
                        <tr>
                            @if (!$isEmployee)
                                <td>
                                    <div class="fw-bold text-dark">{{ $wfh->employee->full_name ?? $wfh->employee->name ?? '-' }}</div>
                                </td>
                            @endif
                            <td>
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($wfh->start_date)->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($wfh->end_date)->format('d M Y') }}
                                </span>
                            </td>
                            <td style="max-width: 280px;">
                                <div class="text-dark small">{{ $wfh->reason }}</div>
                                @if ($wfh->status === 'Rejected' && !empty($wfh->rejection_reason))
                                    <div class="text-danger small mt-1">
                                        <strong>{{ __('Rejection Note:') }}</strong> {{ $wfh->rejection_reason }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($wfh->status === 'Pending')
                                    <span class="hrm-badge hrm-badge-pending"><i class="ti ti-clock"></i> {{ __('Pending') }}</span>
                                @elseif ($wfh->status === 'Approved')
                                    <span class="hrm-badge hrm-badge-approved"><i class="ti ti-circle-check"></i> {{ __('Approved') }}</span>
                                @else
                                    <span class="hrm-badge hrm-badge-rejected"><i class="ti ti-circle-x"></i> {{ __('Rejected') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($wfh->actionUser)
                                    <div class="small fw-semibold text-dark">{{ $wfh->actionUser->name }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">{{ $wfh->action_at ? $wfh->action_at->format('d M Y, h:i A') : '' }}</div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            @if ($canApprove)
                                <td class="text-end">
                                    @if ($wfh->status === 'Pending')
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <button type="button"
                                                    class="hrm-icon-btn hrm-icon-btn-success approve-wfh-btn"
                                                    data-action="{{ route('attendance.wfh.approve', $wfh->id) }}"
                                                    data-employee="{{ $wfh->employee->full_name ?? $wfh->employee->name ?? '-' }}"
                                                    data-dates="{{ \Carbon\Carbon::parse($wfh->start_date)->format('d M Y') }}{{ $wfh->start_date !== $wfh->end_date ? ' - ' . \Carbon\Carbon::parse($wfh->end_date)->format('d M Y') : '' }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveWfhModal"
                                                    title="{{ __('Approve') }}">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button type="button"
                                                    class="hrm-icon-btn hrm-icon-btn-danger reject-wfh-btn"
                                                    data-action="{{ route('attendance.wfh.reject', $wfh->id) }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectWfhModal"
                                                    title="{{ __('Reject') }}">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                    @elseif ($wfh->status === 'Approved')
                                        <button type="button"
                                                class="hrm-icon-btn hrm-icon-btn-danger reject-wfh-btn"
                                                data-action="{{ route('attendance.wfh.reject', $wfh->id) }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectWfhModal"
                                                title="{{ __('Reject Approved WFH') }}">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">{{ __('Rejected') }}</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canApprove ? 7 : ($isEmployee ? 5 : 6) }}" class="text-center py-5 text-muted">
                                <i class="ti ti-home-off fs-2 d-block mb-2"></i>
                                {{ __('No Work From Home requests found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="d-md-none">
            @forelse ($wfhRequests as $wfh)
                <div class="wfh-mobile-card">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            @if (!$isEmployee)
                                <div class="fw-bold text-dark" style="font-size:0.9rem;">{{ $wfh->employee->full_name ?? $wfh->employee->name ?? '-' }}</div>
                            @endif
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($wfh->start_date)->format('d M Y') }}
                                    @if ($wfh->start_date !== $wfh->end_date)
                                        - {{ \Carbon\Carbon::parse($wfh->end_date)->format('d M Y') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div>
                            @if ($wfh->status === 'Pending')
                                <span class="hrm-badge hrm-badge-pending"><i class="ti ti-clock"></i> {{ __('Pending') }}</span>
                            @elseif ($wfh->status === 'Approved')
                                <span class="hrm-badge hrm-badge-approved"><i class="ti ti-circle-check"></i> {{ __('Approved') }}</span>
                            @else
                                <span class="hrm-badge hrm-badge-rejected"><i class="ti ti-circle-x"></i> {{ __('Rejected') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-dark small mb-2" style="word-break:break-word;">
                        <span class="text-muted fw-semibold">{{ __('Reason:') }}</span> {{ $wfh->reason }}
                        @if ($wfh->status === 'Rejected' && !empty($wfh->rejection_reason))
                            <div class="text-danger small mt-1">
                                <strong>{{ __('Rejection Note:') }}</strong> {{ $wfh->rejection_reason }}
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:1px dashed #e2e8f0;">
                        <div class="text-muted" style="font-size:0.74rem;">
                            @if ($wfh->actionUser)
                                {{ __('By') }} <strong class="text-dark">{{ $wfh->actionUser->name }}</strong>
                                · {{ $wfh->action_at ? $wfh->action_at->format('d M, h:i A') : '' }}
                            @else
                                {{ __('Awaiting action') }}
                            @endif
                        </div>
                        @if ($canApprove)
                            <div class="d-inline-flex align-items-center gap-2">
                                @if ($wfh->status === 'Pending')
                                    <button type="button"
                                            class="hrm-icon-btn hrm-icon-btn-success approve-wfh-btn"
                                            data-action="{{ route('attendance.wfh.approve', $wfh->id) }}"
                                            data-employee="{{ $wfh->employee->full_name ?? $wfh->employee->name ?? '-' }}"
                                            data-dates="{{ \Carbon\Carbon::parse($wfh->start_date)->format('d M Y') }}{{ $wfh->start_date !== $wfh->end_date ? ' - ' . \Carbon\Carbon::parse($wfh->end_date)->format('d M Y') : '' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveWfhModal"
                                            title="{{ __('Approve') }}">
                                        <i class="ti ti-check"></i>
                                    </button>
                                    <button type="button"
                                            class="hrm-icon-btn hrm-icon-btn-danger reject-wfh-btn"
                                            data-action="{{ route('attendance.wfh.reject', $wfh->id) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectWfhModal"
                                            title="{{ __('Reject') }}">
                                        <i class="ti ti-x"></i>
                                    </button>
                                @elseif ($wfh->status === 'Approved')
                                    <button type="button"
                                            class="hrm-icon-btn hrm-icon-btn-danger reject-wfh-btn"
                                            data-action="{{ route('attendance.wfh.reject', $wfh->id) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectWfhModal"
                                            title="{{ __('Reject Approved WFH') }}">
                                        <i class="ti ti-x"></i>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-home-off fs-2 d-block mb-2"></i>
                    {{ __('No Work From Home requests found.') }}
                </div>
            @endforelse
        </div>
    </div>

    @if ($isEmployee || $canApprove)
        <!-- Request / Assign WFH Modal -->
        <div class="modal fade hrm-modal" id="wfhModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-home-plus"></i>
                            {{ $isEmployee ? __('Work From Home Request') : __('Assign Work From Home') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('attendance.wfh.store') }}" method="POST" id="wfhSubmitForm">
                        @csrf
                        <div class="modal-body">
                            @if ($canApprove)
                                <div class="mb-3">
                                    <label class="hrm-modal-label"><i class="ti ti-user"></i> {{ __('Employee') }} <span class="text-danger">*</span></label>
                                    <select name="employee_id" class="hrm-modal-select" required>
                                        <option value="">{{ __('Select Employee') }}</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="hrm-modal-label"><i class="ti ti-calendar"></i> {{ __('Start Date') }} <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="start_date"
                                           id="wfhStartDate"
                                           class="hrm-modal-input"
                                           style="cursor:pointer;"
                                           @if($isEmployee) min="{{ date('Y-m-d') }}" @endif
                                           value="{{ old('start_date', date('Y-m-d')) }}"
                                           onclick="this.showPicker && this.showPicker()"
                                           required>
                                </div>
                                <div class="col-6">
                                    <label class="hrm-modal-label"><i class="ti ti-calendar"></i> {{ __('End Date') }} <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="end_date"
                                           id="wfhEndDate"
                                           class="hrm-modal-input"
                                           style="cursor:pointer;"
                                           @if($isEmployee) min="{{ date('Y-m-d') }}" @endif
                                           value="{{ old('end_date', date('Y-m-d')) }}"
                                           onclick="this.showPicker && this.showPicker()"
                                           required>
                                </div>
                            </div>

                            <div class="mb-1">
                                <label class="hrm-modal-label"><i class="ti ti-file-text"></i> {{ __('Reason') }} <span class="text-danger">*</span></label>
                                <textarea name="reason"
                                          class="hrm-modal-textarea"
                                          placeholder="{{ __('Enter reason for Work From Home...') }}"
                                          required>{{ old('reason') }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="hrm-modal-btn-submit" id="wfhSubmitBtn">
                                <i class="ti ti-send"></i> {{ $isEmployee ? __('Submit Request') : __('Assign WFH') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($canApprove)
        <!-- Approve Confirmation Modal -->
        <div class="modal fade hrm-modal" id="approveWfhModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-circle-check"></i> {{ __('Approve WFH Request') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="approveWfhForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="mb-3 text-dark" style="font-size:0.88rem;">{{ __('Are you sure you want to approve Work From Home for this employee? They will be able to Punch In / Punch Out without office location restriction.') }}</p>
                            <div class="p-3 rounded-3 bg-light border small">
                                <div class="mb-1"><strong>{{ __('Employee:') }}</strong> <span id="approveWfhEmp">-</span></div>
                                <div><strong>{{ __('Dates:') }}</strong> <span id="approveWfhDates">-</span></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="hrm-modal-btn-submit">
                                <i class="ti ti-check"></i> {{ __('Confirm Approve') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Reason Modal -->
        <div class="modal fade hrm-modal" id="rejectWfhModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-circle-x"></i> {{ __('Reject WFH Request') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="rejectWfhForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <label class="hrm-modal-label"><i class="ti ti-file-text"></i> {{ __('Rejection Reason (Optional)') }}</label>
                            <textarea name="rejection_reason" class="hrm-modal-textarea" placeholder="{{ __('Enter reason for rejection...') }}"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="hrm-modal-btn-cancel" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="hrm-modal-btn-submit">
                                <i class="ti ti-x"></i> {{ __('Confirm Reject') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('wfhStartDate');
    const endInput = document.getElementById('wfhEndDate');
    if (startInput && endInput) {
        startInput.addEventListener('change', function () {
            endInput.min = this.value;
            if (endInput.value < this.value) {
                endInput.value = this.value;
            }
        });
    }

    const wfhForm = document.getElementById('wfhSubmitForm');
    const wfhSubmitBtn = document.getElementById('wfhSubmitBtn');
    if (wfhForm && wfhSubmitBtn) {
        wfhForm.addEventListener('submit', function () {
            wfhSubmitBtn.disabled = true;
            wfhSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';
        });
    }

    document.querySelectorAll('.approve-wfh-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById('approveWfhForm');
            if (form) form.action = this.getAttribute('data-action');
            const emp = document.getElementById('approveWfhEmp');
            const dt = document.getElementById('approveWfhDates');
            if (emp) emp.textContent = this.getAttribute('data-employee') || '-';
            if (dt) dt.textContent = this.getAttribute('data-dates') || '-';
        });
    });

    document.querySelectorAll('.reject-wfh-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById('rejectWfhForm');
            if (form) form.action = this.getAttribute('data-action');
        });
    });
});
</script>
@endpush

