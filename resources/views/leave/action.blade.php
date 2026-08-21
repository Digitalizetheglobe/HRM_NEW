@php
    $userType = strtolower(Auth::user()->type);
    $isCompanyUser = $userType == 'company';
    $leaveStatus = strtolower($leave->status ?? '');
    $isPending = $leaveStatus == 'pending';
@endphp

{{ Form::open(['url' => 'leave/changeaction', 'method' => 'post', 'id' => 'leave-action-form']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table" id="pc-dt-simple">
                <tr role="row">
                    <th>{{ __('Employee') }}</th>
                    <td>{{ !empty($employee->full_name) ? $employee->full_name : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Leave Type ') }}</th>
                    <td>{{ !empty($leavetype->title) ? $leavetype->title : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Appplied On') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Start Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->start_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('End Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->end_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Leave Reason') }}</th>
                    <td>{{ !empty($leave->leave_reason) ? $leave->leave_reason : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Days Taken') }}</th>
                    <td>
                        @php
                            $days = $leave->total_leave_days ?? 0;
                            $durType = $leave->leave_duration_type ?? 'full_day';
                        @endphp
                        <span class="fw-bold">{{ number_format($days, 1) }} {{ $days == 1 ? __('Day') : __('Days') }}</span>
                        &nbsp;
                        @if($durType == 'half_day')
                            <span class="badge bg-info">{{ __('Half Day') }}</span>
                            @if(!empty($leave->half_day_session))
                                <span class="badge bg-secondary ms-1">
                                    {{ $leave->half_day_session == 'first_half' ? __('First Half') : __('Second Half') }}
                                </span>
                            @endif
                        @elseif($days == floor($days))
                            <span class="badge bg-primary">{{ __('Full Day') }}</span>
                        @else
                            <span class="badge bg-purple" style="background:#7c3aed;">{{ __('Mixed') }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{ __('Status') }}</th>
                    <td>{{ !empty($leave->status) ? $leave->status : '' }}</td>
                </tr>

                <input type="hidden" value="{{ $leave->id }}" name="leave_id">  
                <input type="hidden" value="{{ $leave->status }}" name="previous_status">
            </table>
        </div>
    </div>

</div>

<div class="modal-footer">
    @if($isPending)
        <input type="submit" value="{{ __('Approved') }}" class="btn btn-success rounded" name="status" id="approve-btn">
        <input type="submit" value="{{ __('Reject') }}" class="btn btn-danger rounded" name="status">
    @else
        <p class="text-muted mb-0">{{ __('Leave status: ') . $leave->status }}</p>
    @endif
</div>

{{ Form::close() }}
