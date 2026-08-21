<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table modal-table" id="pc-dt-simple">
                        <tr role="row">
                            <th>{{ __('Employee') }}</th>
                            <td>{{ !empty($employee->full_name) ? $employee->full_name : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Leave Type') }}</th>
                            <td>{{ !empty($leavetype->title) ? $leavetype->title : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Leave Duration') }}</th>
                            <td>
                                @if(isset($leave->leave_duration_type))
                                    @if($leave->leave_duration_type == 'full_day')
                                        <span class="badge bg-primary">{{ __('Full Day') }}</span>
                                    @elseif($leave->leave_duration_type == 'half_day')
                                        <span class="badge bg-info">{{ __('Half Day') }}</span>
                                        @if($leave->remark)
                                            @php
                                                $remark = $leave->remark;
                                                if (strpos($remark, 'Half Day Session:') !== false) {
                                                    $session = str_replace('Half Day Session: ', '', substr($remark, strpos($remark, 'Half Day Session:')));
                                                    $session = trim(explode('|', $session)[0]);
                                                    echo '<br><small class="badge bg-info">' . $session . '</small>';
                                                }
                                            @endphp
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">{{ __('N/A') }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">{{ __('N/A') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Applied On') }}</th>
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
                            <th>{{ __('Total Days') }}</th>
                            <td>{{ $leave->total_leave_days }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Leave Reason') }}</th>
                            <td>{{ !empty($leave->leave_reason) ? $leave->leave_reason : '' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if ($leave->status == 'Pending')
                                    <div class="badge bg-warning p-2 px-3 rounded">
                                        {{ $leave->status }}
                                    </div>
                                @elseif($leave->status == 'Approved')
                                    <div class="badge bg-success p-2 px-3 rounded">
                                        {{ $leave->status }}
                                    </div>
                                @elseif($leave->status == 'Reject')
                                    <div class="badge bg-danger p-2 px-3 rounded">
                                        {{ $leave->status }}
                                    </div>
                                @else
                                    {{ $leave->status }}
                                @endif
                            </td>
                        </tr>
                        @if($leave->forwarded_to_director_id)
                            <tr>
                                <th>{{ __('Forwarded To') }}</th>
                                <td>{{ $leave->forwardedToDirector->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Company Approved') }}</th>
                                <td>{{ $leave->company_approved ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endif

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

