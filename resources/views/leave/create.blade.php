@php
    $setting = App\Models\Utility::settings();
    $plan = Utility::getChatGPTSettings();
@endphp
{{ Form::open(['url' => 'leave', 'method' => 'post']) }}
<div class="modal-body">

    @if ($plan->enable_chatgpt == 'on')
        <div class="card-footer text-end">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="{{ route('generate', ['leave']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
            </a>
        </div>
    @endif

    @if (\Auth::user()->type != 'employee')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('employee_id', __('Employee'), ['class' => 'col-form-label']) }}
                    {{ Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'id' => 'employee_id', 'placeholder' => __('Select Employee')]) }}
                </div>
            </div>
        </div>
    @else
        {!! Form::hidden('employee_id', !empty($employees) ? $employees->id : 0, ['id' => 'employee_id']) !!}
    @endif

    {{-- Leave Type --}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('leave_type_id', __('Leave Type'), ['class' => 'col-form-label']) }}<span class="text-danger pl-1">*</span>
                <select name="leave_type_id" id="leave_type_id" class="form-control select" required>
                    <option value="">{{ __('Select Leave Type') }}</option>
                    @foreach ($leavetypes as $lt)
                        @php
                            $isProbation = false;
                            $employee = \App\Models\Employee::find($employeeId);
                            if ($employee && $employee->company_doj) {
                                $doj = \Carbon\Carbon::parse($employee->company_doj);
                                $monthsSinceJoining = (now()->year - $doj->year) * 12 + (now()->month - $doj->month);
                                if ($monthsSinceJoining < 3) {
                                    $isProbation = true;
                                }
                            }
                            if ($isProbation && strtolower(trim($lt->title)) !== 'earned leave') {
                                continue;
                            }

                            $available = $lt->unlimited ? 'Unlimited' : 0.0;
                            if ($employeeId && !$lt->unlimited) {
                                $available = $lt->getMonthlyBalance($employeeId);
                                $pendingDays = \App\Models\Leave::where('employee_id', $employeeId)
                                    ->where('leave_type_id', $lt->id)
                                    ->where('status', 'Pending')
                                    ->whereMonth('start_date', now()->month)
                                    ->whereYear('start_date', now()->year)
                                    ->sum('total_leave_days');
                                $available = max(0.0, (float)$available - (float)$pendingDays);
                            }
                        @endphp
                        <option value="{{ $lt->id }}"
                                data-unlimited="{{ $lt->unlimited }}"
                                data-available="{{ $available }}">
                            {{ $lt->title }}
                        </option>
                    @endforeach
                </select>
                <div id="balance-info-alert" class="alert alert-info mt-2" style="display: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem;">
                    <i class="fas fa-info-circle me-1"></i> <span id="balance-info-text"></span>
                </div>
                <small class="form-text text-muted">{{ __('Select a leave type') }}</small>
            </div>
        </div>
    </div>

    {{-- Date Range - shown once leave type is selected --}}
    <div id="leave-date-section" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('start_date', __('From Date'), ['class' => 'col-form-label']) }}<span class="text-danger pl-1">*</span>
                    {{ Form::text('start_date', null, ['class' => 'form-control d_week current_date', 'autocomplete' => 'off', 'id' => 'start_date', 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('end_date', __('To Date'), ['class' => 'col-form-label']) }}<span class="text-danger pl-1">*</span>
                    {{ Form::text('end_date', null, ['class' => 'form-control d_week current_date', 'autocomplete' => 'off', 'id' => 'end_date', 'required' => 'required']) }}
                </div>
            </div>
        </div>

        {{-- Last Day is Half Day toggle --}}
        <div class="row" id="half-day-toggle-row" style="display: none;">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="d-flex align-items-center gap-2" style="background: #f0f7ff; border-radius: 8px; padding: 12px 16px; border: 1px solid #c9e0ff;">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="last_day_half_day" name="last_day_half_day" value="1">
                            <label class="form-check-label fw-semibold ms-2" for="last_day_half_day">
                                {{ __('Last day is a Half Day') }}
                            </label>
                        </div>
                        <small class="text-muted ms-auto">{{ __('Toggle if your last day of leave is a half day (0.5 day deducted)') }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Duration Summary --}}
        <div class="row" id="duration-summary-row" style="display: none;">
            <div class="col-md-12">
                <div class="alert py-2" id="duration-summary-alert" style="font-size: 0.875rem; border-radius: 8px;">
                    <i class="fas fa-calendar-check me-1"></i>
                    <span id="duration-summary-text"></span>
                </div>
            </div>
        </div>

        {{-- Hidden fields for backend --}}
        <input type="hidden" name="leave_duration_type" id="leave_duration_type_hidden" value="full_day">
        <input type="hidden" name="total_leave_days_override" id="total_leave_days_override" value="">
    </div>

    {{-- Leave Reason --}}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('leave_reason', __('Reason for Leave'), ['class' => 'col-form-label']) }}<span class="text-danger pl-1">*</span>
                {{ Form::textarea('leave_reason', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Reason for Leave'), 'rows' => '3']) }}
            </div>
        </div>
    </div>

    {{-- Google Calendar Sync --}}
    @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
        <div class="form-group col-md-6">
            {{ Form::label('synchronize_type', __('Synchronize in Google Calendar?'), ['class' => 'form-label']) }}
            <div class="form-switch">
                <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow" value="google_calendar">
                <label class="form-check-label" for="switch-shadow"></label>
            </div>
        </div>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary" id="submit-btn">
</div>
{{ Form::close() }}

<script>
    $(document).ready(function() {
        var now = new Date();
        var month = (now.getMonth() + 1).toString().padStart(2, '0');
        var day = now.getDate().toString().padStart(2, '0');
        var today = now.getFullYear() + '-' + month + '-' + day;
        $('.current_date').val(today);

        function updateLeaveTypes(employeeId) {
            if (!employeeId) return;
            $.ajax({
                url: '{{ route('leave.jsoncount') }}',
                type: 'POST',
                data: { employee_id: employeeId, _token: '{{ csrf_token() }}' },
                success: function(data) {
                    var leaveTypeSelect = $('#leave_type_id');
                    var currentValue = leaveTypeSelect.val();
                    leaveTypeSelect.find('option:not(:first)').remove();
                    $.each(data, function(i, lt) {
                        leaveTypeSelect.append(
                            $('<option></option>').attr('value', lt.id)
                                .attr('data-unlimited', lt.unlimited ? 1 : 0)
                                .attr('data-available', lt.available)
                                .text(lt.title)
                        );
                    });
                    if (currentValue && leaveTypeSelect.find('option[value="' + currentValue + '"]').length) {
                        leaveTypeSelect.val(currentValue).trigger('change');
                    }
                }
            });
        }

        $('#employee_id').on('change', function() { updateLeaveTypes($(this).val()); });
        var initialEmpId = $('#employee_id').val();
        if (initialEmpId) updateLeaveTypes(initialEmpId);

        function calculateWorkingDays(startStr, endStr) {
            if (!startStr || !endStr) return 0;
            var start = new Date(startStr), end = new Date(endStr);
            if (isNaN(start) || isNaN(end) || end < start) return 0;
            var days = 0, cur = new Date(start);
            while (cur <= end) {
                if (cur.getDay() !== 0) days++; // exclude Sunday
                cur.setDate(cur.getDate() + 1);
            }
            return days;
        }

        function fmtDate(str) {
            if (!str) return '';
            var d = new Date(str);
            return isNaN(d) ? str : d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        function updateSummary() {
            var start = $('#start_date').val();
            var end = $('#end_date').val();
            var isLastHalf = $('#last_day_half_day').is(':checked');
            var selOpt = $('#leave_type_id').find('option:selected');
            var isUnlimited = selOpt.data('unlimited') == 1;
            var available = parseFloat(selOpt.data('available')) || 0;

            if (!start || !end || !selOpt.val()) {
                $('#duration-summary-row').hide();
                return;
            }

            var workingDays = calculateWorkingDays(start, end);
            if (workingDays <= 0) {
                $('#duration-summary-row').hide();
                return;
            }

            var effective = isLastHalf ? (workingDays - 0.5) : workingDays;
            if (effective < 0.5) effective = 0.5;

            $('#total_leave_days_override').val(effective.toFixed(1));
            $('#leave_duration_type_hidden').val(isLastHalf ? 'mixed' : 'full_day');

            var text = '', cls = 'alert-success';

            if (!isUnlimited && effective > available) {
                text = '<strong>&#9888; Insufficient Balance:</strong> Requesting <strong>' + effective.toFixed(1) + ' day(s)</strong> but only <strong>' + available.toFixed(2) + ' available</strong>.';
                cls = 'alert-danger';
            } else {
                text = 'You are requesting <strong>' + effective.toFixed(1) + ' day(s)</strong>';
                if (start !== end) {
                    text += ' from <strong>' + fmtDate(start) + '</strong> to <strong>' + fmtDate(end) + '</strong>';
                } else {
                    text += ' on <strong>' + fmtDate(start) + '</strong>';
                }
                if (isLastHalf) {
                    text += ' <span class="badge bg-info ms-1" style="font-size:0.75rem;">Last day: Half Day</span>';
                }
                text += '.';
            }

            $('#duration-summary-text').html(text);
            $('#duration-summary-alert').attr('class', 'alert py-2 ' + cls).css({'font-size':'0.875rem','border-radius':'8px'});
            $('#duration-summary-row').show();
        }

        $('#leave_type_id').on('change', function() {
            var leaveType = $(this).val();
            var selOpt = $(this).find('option:selected');
            var isUnlimited = selOpt.data('unlimited') == 1;
            var available = parseFloat(selOpt.data('available'));

            if (leaveType) {
                var infoText = isUnlimited
                    ? '{{ __("Casual Leave is Unlimited — no balance deducted.") }}'
                    : '{{ __("Available balance: ") }}<strong>' + (isNaN(available) ? '0.00' : available.toFixed(2)) + ' days</strong>';
                $('#balance-info-text').html(infoText);
                $('#balance-info-alert').show();
                $('#leave-date-section').show();

                // If dates are already filled (pre-populated with today), show the toggle immediately
                var start = $('#start_date').val();
                var end = $('#end_date').val();
                if (start && end && calculateWorkingDays(start, end) > 0) {
                    $('#half-day-toggle-row').show();
                }

                updateSummary();
            } else {
                $('#balance-info-alert').hide();
                $('#leave-date-section').hide();
                $('#duration-summary-row').hide();
                $('#half-day-toggle-row').hide();
            }
        });

        $(document).on('change changeDate', '#start_date, #end_date', function() {
            var start = $('#start_date').val();
            var end = $('#end_date').val();
            if (start && end && calculateWorkingDays(start, end) > 0) {
                $('#half-day-toggle-row').show();
            } else {
                $('#half-day-toggle-row').hide();
            }
            updateSummary();
        });

        $('#last_day_half_day').on('change', updateSummary);

        $('form').on('submit', function(e) {
            var selOpt = $('#leave_type_id').find('option:selected');
            var isUnlimited = selOpt.data('unlimited') == 1;
            var available = parseFloat(selOpt.data('available')) || 0;
            var requested = parseFloat($('#total_leave_days_override').val()) || 0;

            if (!isUnlimited && requested > available) {
                e.preventDefault();
                alert('{{ __("Insufficient balance. Available: ") }}' + available.toFixed(2) + '{{ __(" days, Requested: ") }}' + requested.toFixed(1) + '{{ __(" days.") }}');
                return false;
            }
        });
    });
</script>
