{{ Form::open(['url' => 'attendance-regularisation/changeaction', 'method' => 'post', 'id' => 'regularisation-action-form']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table" id="pc-dt-simple">
                <tr role="row">
                    <th>{{ __('Employee Name') }}</th>
                    <td>{{ !empty($regularisation->employee) ? $regularisation->employee->full_name : __('N/A') }}</td>
                </tr>
                <tr>
                    <th>{{ __('Missed Attendance Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($regularisation->missed_attendance_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Punch In Time') }}</th>
                    <td>{{ \Auth::user()->timeFormat($regularisation->punch_in_time) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Punch Out Time') }}</th>
                    <td>{{ \Auth::user()->timeFormat($regularisation->punch_out_time) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Reason') }}</th>
                    <td>{{ $regularisation->reason }}</td>
                </tr>
                <tr>
                    <th>{{ __('Remark') }}</th>
                    <td>{{ $regularisation->remark }}</td>
                </tr>
                <tr>
                    <th>{{ __('Status') }}</th>
                    <td>
                        @if ($regularisation->status == 'Pending')
                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                        @elseif ($regularisation->status == 'Approved')
                            <span class="badge bg-success">{{ __('Approved') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('Rejected') }}</span>
                        @endif
                    </td>
                </tr>
                <input type="hidden" value="{{ $regularisation->id }}" name="regularisation_id">  
                <input type="hidden" value="{{ $regularisation->status }}" name="previous_status">
            </table>
        </div>
    </div>
</div>

@if (\Auth::user()->type != 'employee' && $regularisation->status == 'Pending')
    <div class="modal-footer">
        <input type="submit" value="{{ __('Approved') }}" class="btn btn-success rounded" name="status" id="approve-btn">
        <input type="submit" value="{{ __('Reject') }}" class="btn btn-danger rounded" name="status">
    </div>
@endif

{{ Form::close() }}

