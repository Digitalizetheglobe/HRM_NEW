<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">{{ __('Attendance Regularisation Details') }}</h5>
        </div>
        
        @if (\Auth::user()->type != 'employee')
            <div class="form-group col-lg-6 col-md-6">
                <label class="col-form-label"><strong>{{ __('Employee Name') }}:</strong></label>
                <p>{{ !empty($regularisation->employee) ? $regularisation->employee->full_name : __('N/A') }}</p>
            </div>
        @endif

        <div class="form-group col-lg-6 col-md-6">
            <label class="col-form-label"><strong>{{ __('Missed Attendance Date') }}:</strong></label>
            <p>{{ \Auth::user()->dateFormat($regularisation->missed_attendance_date) }}</p>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label class="col-form-label"><strong>{{ __('Punch In Time') }}:</strong></label>
            <p>{{ \Auth::user()->timeFormat($regularisation->punch_in_time) }}</p>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label class="col-form-label"><strong>{{ __('Punch Out Time') }}:</strong></label>
            <p>{{ \Auth::user()->timeFormat($regularisation->punch_out_time) }}</p>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label class="col-form-label"><strong>{{ __('Reason') }}:</strong></label>
            <p>{{ $regularisation->reason }}</p>
        </div>

        <div class="form-group col-lg-6 col-md-6">
            <label class="col-form-label"><strong>{{ __('Status') }}:</strong></label>
            <p>
                @if ($regularisation->status == 'Pending')
                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                @elseif ($regularisation->status == 'Approved')
                    <span class="badge bg-success">{{ __('Approved') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                @endif
            </p>
        </div>

        <div class="form-group col-lg-12 col-md-12">
            <label class="col-form-label"><strong>{{ __('Remark') }}:</strong></label>
            <p>{{ $regularisation->remark }}</p>
        </div>

        @if ($regularisation->status != 'Pending')
            <div class="form-group col-lg-6 col-md-6">
                <label class="col-form-label"><strong>{{ __('Processed By') }}:</strong></label>
                <p>{{ !empty($regularisation->approvedBy) ? $regularisation->approvedBy->name : __('N/A') }}</p>
            </div>

            <div class="form-group col-lg-6 col-md-6">
                <label class="col-form-label"><strong>{{ __('Processed At') }}:</strong></label>
                <p>{{ $regularisation->approved_at ? \Auth::user()->dateFormat($regularisation->approved_at) . ' ' . \Auth::user()->timeFormat($regularisation->approved_at) : __('N/A') }}</p>
            </div>
        @endif
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>

