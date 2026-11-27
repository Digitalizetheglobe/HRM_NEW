@extends('layouts.admin')

@section('page-title')
    {{ __('Attendance Regularization') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance Regularization') }}</li>
@endsection

@section('action-button')
    @if ($isEmployee)
        <button type="button"
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#regularizationModal"
                title="{{ __('Request Regularization') }}"
                aria-label="{{ __('Request Regularization') }}">
            <i class="ti ti-plus"></i>
        </button>
    @endif
@endsection

@push('css-page')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    .hrm-reg-wrap * { font-family: 'Inter', sans-serif; }

    .hrm-stat-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(0, 26, 59, 0.04);
    }
    .hrm-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .hrm-stat-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: #1e293b;
        margin-top: 2px;
    }
    .hrm-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .hrm-tabs-nav {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 20px;
        padding-bottom: 8px;
        flex-wrap: wrap;
    }
    .hrm-tab-btn {
        background: none;
        border: none;
        padding: 7px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        border-radius: 8px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .hrm-tab-btn.active { background: #eff6ff; color: #1e3a8a; }
    .hrm-tab-btn:hover:not(.active) { background: #f8fafc; color: #1e293b; }

    .hrm-main-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        box-shadow: 0 2px 12px rgba(0, 26, 59, 0.05);
        overflow: hidden;
    }
    .hrm-card-hdr {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fafbfd;
    }
    .hrm-card-hdr h5 {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hrm-table { width: 100%; border-collapse: collapse; }
    .hrm-table th {
        background: #f8fafc;
        color: #6b7280;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 12px 20px;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .hrm-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.84rem;
        color: #374151;
        vertical-align: middle;
    }
    .hrm-table tr:last-child td { border-bottom: none; }
    .hrm-table tbody tr:hover td { background: #fafbfd; }

    .hrm-date-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #1e293b;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .hrm-time-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #eff6ff;
        color: #1e3a8a;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .hrm-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.73rem;
        font-weight: 700;
    }
    .hrm-badge-approved { background: #d1fae5; color: #065f46; }
    .hrm-badge-pending  { background: #fef3c7; color: #92400e; }
    .hrm-badge-rejected { background: #fee2e2; color: #991b1b; }

    .hrm-icon-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        transition: all 0.2s;
        padding: 0;
    }
    .hrm-icon-btn-success { background: #d1fae5; color: #065f46; }
    .hrm-icon-btn-success:hover { background: #a7f3d0; color: #065f46; }
    .hrm-icon-btn-danger  { background: #fee2e2; color: #b91c1c; }
    .hrm-icon-btn-danger:hover { background: #fecaca; color: #b91c1c; }

    .reg-date-hint {
        font-size: 0.82rem;
        border-radius: 10px;
        padding: 8px 12px;
        margin-top: 8px;
        display: none;
    }
    .reg-date-hint.info { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; display: block; }
    .reg-date-hint.warn { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; display: block; }
</style>
@endpush

@section('content')
<div class="hrm-reg-wrap">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="hrm-stat-card">
                <div>
                    <div class="hrm-stat-label">{{ __('Total Requests') }}</div>
                    <div class="hrm-stat-value">{{ $counts['all'] }}</div>
                </div>
                <div class="hrm-stat-icon" style="background:#eef2ff;color:#1e3a8a;">
                    <i class="ti ti-calendar-time"></i>
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
            <a href="{{ route('attendance.regularization.index', ['status' => $tab]) }}"
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
                <i class="ti ti-calendar-check" style="color:#001a3b;"></i>
                {{ __('Regularization Requests') }}
            </h5>
            <span style="background:#f1f5f9; color:#475569; border-radius:8px; padding:4px 12px; font-size:0.78rem; font-weight:700; margin-left:auto;">
                {{ $regularizations->count() }} {{ __('records') }}
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table class="hrm-table">
                <thead>
                    <tr>
                        @if (!$isEmployee)
                            <th>{{ __('Employee') }}</th>
                        @endif
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Punch In') }}</th>
                        <th>{{ __('Punch Out') }}</th>
                        <th>{{ __('Reason') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actioned By') }}</th>
                        @if ($canApprove)
                            <th class="text-end">{{ __('Action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($regularizations as $reg)
                        <tr>
                            @if (!$isEmployee)
                                <td>
                                    <div class="fw-bold text-dark">{{ $reg->employee->full_name ?? $reg->employee->name ?? '-' }}</div>
                                </td>
                            @endif
                            <td>
                                <span class="hrm-date-chip">
                                    <i class="ti ti-calendar"></i>
                                    {{ \Carbon\Carbon::parse($reg->date)->format('d M Y') }}
                                </span>
                                <div class="text-muted small mt-1">{{ \Carbon\Carbon::parse($reg->date)->format('l') }}</div>
                            </td>
                            <td>
                                <span class="hrm-time-tag">
                                    <i class="ti ti-login"></i>
                                    {{ \Carbon\Carbon::parse($reg->clock_in)->format('h:i A') }}
                                </span>
                            </td>
                            <td>
                                <span class="hrm-time-tag">
                                    <i class="ti ti-logout"></i>
                                    {{ \Carbon\Carbon::parse($reg->clock_out)->format('h:i A') }}
                                </span>
                            </td>
                            <td style="max-width: 280px;">
                                <div class="text-dark small">{{ $reg->reason }}</div>
                                @if ($reg->status === 'Rejected' && !empty($reg->rejection_reason))
                                    <div class="text-danger small mt-1">
                                        <strong>{{ __('Rejection Note:') }}</strong> {{ $reg->rejection_reason }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($reg->status === 'Pending')
                                    <span class="hrm-badge hrm-badge-pending"><i class="ti ti-clock"></i> {{ __('Pending') }}</span>
                                @elseif ($reg->status === 'Approved')
                                    <span class="hrm-badge hrm-badge-approved"><i class="ti ti-circle-check"></i> {{ __('Approved') }}</span>
                                @else
                                    <span class="hrm-badge hrm-badge-rejected"><i class="ti ti-circle-x"></i> {{ __('Rejected') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($reg->actionUser)
                                    <div class="small fw-semibold text-dark">{{ $reg->actionUser->name }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">{{ $reg->action_at ? $reg->action_at->format('d M Y, h:i A') : '' }}</div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            @if ($canApprove)
                                <td class="text-end">
                                    @if ($reg->status === 'Pending')
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <button type="button"
                                                    class="hrm-icon-btn hrm-icon-btn-success approve-reg-btn"
                                                    data-action="{{ route('attendance.regularization.approve', $reg->id) }}"
                                                    data-employee="{{ $reg->employee->full_name ?? $reg->employee->name ?? '-' }}"
                                                    data-date="{{ \Carbon\Carbon::parse($reg->date)->format('d M Y') }}"
                                                    data-in="{{ \Carbon\Carbon::parse($reg->clock_in)->format('h:i A') }}"
                                                    data-out="{{ \Carbon\Carbon::parse($reg->clock_out)->format('h:i A') }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#approveRegModal"
                                                    title="{{ __('Approve') }}"
                                                    aria-label="{{ __('Approve') }}">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button type="button"
                                                    class="hrm-icon-btn hrm-icon-btn-danger reject-reg-btn"
                                                    data-action="{{ route('attendance.regularization.reject', $reg->id) }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectRegModal"
                                                    title="{{ __('Reject') }}"
                                                    aria-label="{{ __('Reject') }}">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                    @elseif ($reg->status === 'Approved')
                                        <button type="button"
                                                class="hrm-icon-btn hrm-icon-btn-danger reject-reg-btn"
                                                data-action="{{ route('attendance.regularization.reject', $reg->id) }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectRegModal"
                                                title="{{ __('Reject Approved Request') }}"
                                                aria-label="{{ __('Reject Approved Request') }}">
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
                            <td colspan="{{ $canApprove ? 8 : ($isEmployee ? 6 : 7) }}" class="text-center py-5 text-muted">
                                <i class="ti ti-calendar-off fs-2 d-block mb-2"></i>
                                {{ __('No attendance regularization requests found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($isEmployee)
        <!-- Request Regularization Modal -->
        <div class="modal fade" id="regularizationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header" style="background: #001a3b; color: #fff;">
                        <h5 class="modal-title text-white">
                            <i class="ti ti-calendar-plus me-2"></i>{{ __('Attendance Regularization Request') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('attendance.regularization.store') }}" method="POST" id="regSubmitForm">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Attendance Date') }} <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="date"
                                       id="regDateInput"
                                       class="form-control"
                                       max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                                       value="{{ old('date', date('Y-m-d', strtotime('-1 day'))) }}"
                                       required>
                                <div class="form-text text-muted">
                                    {{ __('Only past dates with a submitted work update (excluding Sundays, Holidays & Leaves) are allowed.') }}
                                </div>
                                <div id="regDateHint" class="reg-date-hint"></div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">{{ __('Punch In Time') }} <span class="text-danger">*</span></label>
                                    <input type="time"
                                           name="clock_in"
                                           id="regClockInInput"
                                           class="form-control"
                                           value="{{ old('clock_in') }}"
                                           required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">{{ __('Punch Out Time') }} <span class="text-danger">*</span></label>
                                    <input type="time"
                                           name="clock_out"
                                           id="regClockOutInput"
                                           class="form-control"
                                           value="{{ old('clock_out') }}"
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('Reason Type') }}</label>
                                <select name="reason_type" id="regReasonType" class="form-select">
                                    <option value="Forgot to Punch Out">{{ __('Forgot to Punch Out') }}</option>
                                    <option value="Forgot to Punch In">{{ __('Forgot to Punch In') }}</option>
                                    <option value="Forgot Both Punch In & Out">{{ __('Forgot Both Punch In & Out') }}</option>
                                    <option value="GPS / Network Issue">{{ __('GPS / Network Issue') }}</option>
                                    <option value="Client Site / Field Work">{{ __('Client Site / Field Work') }}</option>
                                    <option value="Other">{{ __('Other') }}</option>
                                </select>
                            </div>

                            <div class="mb-1">
                                <label class="form-label fw-semibold">{{ __('Reason (Why was attendance missed?)') }} <span class="text-danger">*</span></label>
                                <textarea name="reason"
                                          class="form-control"
                                          rows="3"
                                          placeholder="{{ __('Explain briefly why Punch In / Punch Out was missed...') }}"
                                          required>{{ old('reason') }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary" id="regSubmitBtn">
                                <i class="ti ti-send me-1"></i> {{ __('Submit Request') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($canApprove)
        <!-- Approve Confirmation Modal -->
        <div class="modal fade" id="approveRegModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">
                            <i class="ti ti-circle-check me-2"></i>{{ __('Approve Regularization Request') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="approveRegForm" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="mb-3 text-dark">{{ __('Are you sure you want to approve this regularization request and update the employee attendance?') }}</p>
                            <div class="p-3 rounded-3 bg-light border small">
                                <div class="mb-1"><strong>{{ __('Employee:') }}</strong> <span id="approveEmpName">-</span></div>
                                <div class="mb-1"><strong>{{ __('Date:') }}</strong> <span id="approveRegDate">-</span></div>
                                <div><strong>{{ __('Timing:') }}</strong> <span id="approveRegIn">-</span> - <span id="approveRegOut">-</span></div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-check me-1"></i>{{ __('Confirm Approve') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Reason Modal -->
        <div class="modal fade" id="rejectRegModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white">{{ __('Reject Regularization Request') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="rejectRegForm" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <label class="form-label fw-semibold">{{ __('Rejection Reason (Optional)') }}</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="{{ __('Enter reason for rejection...') }}"></textarea>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger">{{ __('Confirm Reject') }}</button>
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
    const dateInput = document.getElementById('regDateInput');
    const clockInInput = document.getElementById('regClockInInput');
    const clockOutInput = document.getElementById('regClockOutInput');
    const dateHint = document.getElementById('regDateHint');
    const reasonType = document.getElementById('regReasonType');

    async function loadDateAttendance(selectedDate) {
        if (!selectedDate) return;
        dateHint.className = 'reg-date-hint info';
        dateHint.textContent = 'Checking attendance & work update for ' + selectedDate + '...';

        try {
            const url = "{{ route('attendance.regularization.date-info') }}?date=" + encodeURIComponent(selectedDate);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            const submitBtn = document.getElementById('regSubmitBtn');

            if (!data.eligible) {
                clockInInput.value = '';
                clockOutInput.value = '';
                if (submitBtn) submitBtn.disabled = true;
                dateHint.className = 'reg-date-hint warn';
                let msg = '<i class="ti ti-alert-circle me-1"></i>' + (data.reason || 'This date is not eligible for regularization.');
                if (data.needs_work_update) {
                    const updateUrl = "{{ route('projects.my_updates') }}?start_date=" + encodeURIComponent(selectedDate) + "&end_date=" + encodeURIComponent(selectedDate) + "&work_date=" + encodeURIComponent(selectedDate);
                    msg += ' <a href="' + updateUrl + '" class="btn btn-sm btn-primary ms-2 mt-1">{{ __("Add Work Update") }}</a>';
                }
                dateHint.innerHTML = msg;
                return;
            }

            if (submitBtn) submitBtn.disabled = false;

            if (data.exists && data.clock_in) {
                clockInInput.value = data.clock_in;
                clockOutInput.value = data.clock_out ? data.clock_out : '';
                if (!data.clock_out && reasonType) {
                    reasonType.value = 'Forgot to Punch Out';
                }
                dateHint.className = 'reg-date-hint info';
                dateHint.innerHTML = '<i class="ti ti-info-circle me-1"></i>Existing Punch In auto-filled (<strong>' + data.clock_in + '</strong>' + (data.status ? ' • ' + data.status : '') + ').';
            } else {
                clockInInput.value = '';
                clockOutInput.value = '';
                if (reasonType) {
                    reasonType.value = 'Forgot Both Punch In & Out';
                }
                dateHint.className = 'reg-date-hint info';
                dateHint.innerHTML = '<i class="ti ti-check-circle me-1"></i>Work update found. Please enter Punch In and Punch Out times.';
            }
        } catch (e) {
            dateHint.className = 'reg-date-hint';
            dateHint.textContent = '';
        }
    }

    if (dateInput) {
        dateInput.addEventListener('change', function () {
            loadDateAttendance(this.value);
        });
        const regModal = document.getElementById('regularizationModal');
        if (regModal) {
            regModal.addEventListener('shown.bs.modal', function () {
                if (dateInput.value) {
                    loadDateAttendance(dateInput.value);
                }
            });
        }
    }

    const regForm = document.getElementById('regSubmitForm');
    const regSubmitBtn = document.getElementById('regSubmitBtn');
    if (regForm && regSubmitBtn) {
        regForm.addEventListener('submit', function () {
            regSubmitBtn.disabled = true;
            regSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';
        });
    }

    document.querySelectorAll('.approve-reg-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById('approveRegForm');
            if (form) {
                form.action = this.getAttribute('data-action');
            }
            const emp = document.getElementById('approveEmpName');
            const dt = document.getElementById('approveRegDate');
            const cin = document.getElementById('approveRegIn');
            const cout = document.getElementById('approveRegOut');
            if (emp) emp.textContent = this.getAttribute('data-employee') || '-';
            if (dt) dt.textContent = this.getAttribute('data-date') || '-';
            if (cin) cin.textContent = this.getAttribute('data-in') || '-';
            if (cout) cout.textContent = this.getAttribute('data-out') || '-';
        });
    });

    document.querySelectorAll('.reject-reg-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById('rejectRegForm');
            if (form) {
                form.action = this.getAttribute('data-action');
            }
        });
    });
});
</script>
@endpush
